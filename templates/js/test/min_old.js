(function(document, $) {
    'use strict';

    /**
     * Event namespace
     *
     * @type String
     * @final
     */
    var HELPER_NAMESPACE = '._tap';

    /**
     * Event namespace
     *
     * @type String
     * @final
     */
    var HELPER_ACTIVE_NAMESPACE = '._tapActive';

    /**
     * Event name
     *
     * @type String
     * @final
     */
    var EVENT_NAME = 'tap';

    /**
     * Event variables to copy to touches
     *
     * @type String[]
     * @final
     */
    var EVENT_VARIABLES = 'clientX clientY screenX screenY pageX pageY'.split(' ');

    /**
     * jQuery body object
     *
     * @type jQuery
     */
    var $BODY;

    /**
     * Last canceled tap event
     *
     * @type jQuery.Event
     * @private
     */
    var _lastTap;

    /**
     * Last touchstart event
     *
     * @type jQuery.Event
     * @private
     */
    var _lastTouch;

    /**
     * Object for tracking current touch
     *
     * @type Object
     * @static
     */
    var TOUCH_VALUES = {

        /**
         * Number of touches currently active on touchstart
         *
         * @property count
         * @type Number
         */
        count: 0,

        /**
         * touchstart/mousedown jQuery.Event object
         *
         * @property event
         * @type jQuery.Event
         */
        event: 0

    };

    /**
     * Create a new event from the original event
     * Copy over EVENT_VARIABLES from the original jQuery.Event
     *
     * @param {String} type
     * @param {jQuery.Event} e
     * @return {jQuery.Event}
     * @private
     */
    var _createEvent = function(type, e) {
        var originalEvent = e.originalEvent;
        var event = $.Event(originalEvent);

        event.type = type;

        var i = 0;
        var length = EVENT_VARIABLES.length;

        for (; i < length; i++) {
            event[EVENT_VARIABLES[i]] = e[EVENT_VARIABLES[i]];
        }

        return event;
    };

    /**
     * Determine if a valid tap event
     *
     * @param {jQuery.Event} e
     * @return {Boolean}
     * @private
     */
    var _isTap = function(e) {
        if (e.isTrigger) {
            return false;
        }

        var startEvent = TOUCH_VALUES.event;
        var xDelta = Math.abs(e.pageX - startEvent.pageX);
        var yDelta = Math.abs(e.pageY - startEvent.pageY);
        var delta = Math.max(xDelta, yDelta);

        return (
            e.timeStamp - startEvent.timeStamp < $.tap.TIME_DELTA &&
            delta < $.tap.POSITION_DELTA &&
            (!startEvent.touches || TOUCH_VALUES.count === 1) &&
            Tap.isTracking
        );
    };

    /**
     * Determine if mousedown event was emulated from the last touchstart event
     *
     * @function
     * @param {jQuery.Event} e
     * @returns {Boolean}
     * @private
     */
    var _isEmulated = function(e) {
        if (!_lastTouch) {
            return false;
        }

        var xDelta = Math.abs(e.pageX - _lastTouch.pageX);
        var yDelta = Math.abs(e.pageY - _lastTouch.pageY);
        var delta = Math.max(xDelta, yDelta);

        return (
            Math.abs(e.timeStamp - _lastTouch.timeStamp) < 750 &&
            delta < $.tap.POSITION_DELTA
        );
    };

    /**
     * Normalize touch events with data from first touch in the jQuery.Event
     *
     * This could be done using the `jQuery.fixHook` api, but to avoid conflicts
     * with other libraries that might already have applied a fix hook, this
     * approach is used instead.
     *
     * @param {jQuery.Event} event
     * @private
     */
    var _normalizeEvent = function(event) {
        if (event.type.indexOf('touch') === 0) {
            event.touches = event.originalEvent.changedTouches;
            var touch = event.touches[0];

            var i = 0;
            var length = EVENT_VARIABLES.length;

            for (; i < length; i++) {
                event[EVENT_VARIABLES[i]] = touch[EVENT_VARIABLES[i]];
            }
        }

        // Normalize timestamp
        event.timeStamp = Date.now ? Date.now() : +new Date();
    };

    /**
     * Tap object that will track touch events and
     * trigger the tap event when necessary
     *
     * @class Tap
     * @static
     */
    var Tap = {

        /**
         * Flag to determine if touch events are currently enabled
         *
         * @property isEnabled
         * @type Boolean
         */
        isEnabled: false,

        /**
         * Are we currently tracking a tap event?
         *
         * @property isTracking
         * @type Boolean
         */
        isTracking: false,

        /**
         * Enable touch event listeners
         *
         * @method enable
         */
        enable: function() {
            if (Tap.isEnabled) {
                return;
            }

            Tap.isEnabled = true;

            // Set body element
            $BODY = $(document.body)
                .on('touchstart' + HELPER_NAMESPACE, Tap.onStart)
                .on('mousedown' + HELPER_NAMESPACE, Tap.onStart)
                .on('click' + HELPER_NAMESPACE, Tap.onClick);
        },

        /**
         * Disable touch event listeners
         *
         * @method disable
         */
        disable: function() {
            if (!Tap.isEnabled) {
                return;
            }

            Tap.isEnabled = false;

            // unbind all events with namespace
            $BODY.off(HELPER_NAMESPACE);
        },

        /**
         * Store touch start values and target
         *
         * @method onTouchStart
         * @param {jQuery.Event} e
         */
        onStart: function(e) {
            if (e.isTrigger) {
                return;
            }

            _normalizeEvent(e);

            // Ignore non left mouse clicks
            if ($.tap.LEFT_BUTTON_ONLY && !e.touches && e.which !== 1) {
                return;
            }

            if (e.touches) {
                TOUCH_VALUES.count = e.touches.length;
            }

            if (Tap.isTracking) {
                return;
            }

            if (!e.touches && _isEmulated(e)) {
                return;
            }

            Tap.isTracking = true;

            TOUCH_VALUES.event = e;

            if (e.touches) {
                _lastTouch = e;
                $BODY
                    .on('touchend' + HELPER_NAMESPACE + HELPER_ACTIVE_NAMESPACE, Tap.onEnd)
                    .on('touchcancel' + HELPER_NAMESPACE + HELPER_ACTIVE_NAMESPACE, Tap.onCancel);
            } else {
                $BODY.on('mouseup' + HELPER_NAMESPACE + HELPER_ACTIVE_NAMESPACE, Tap.onEnd);
            }
        },

        /**
         * If touch has not been canceled, create a
         * tap event and trigger it on the target element
         *
         * @method onTouchEnd
         * @param {jQuery.Event} e
         */
        onEnd: function(e) {
            var event;

            if (e.isTrigger) {
                return;
            }

            _normalizeEvent(e);

            if (_isTap(e)) {
                event = _createEvent(EVENT_NAME, e);
                _lastTap = event;
                $(TOUCH_VALUES.event.target).trigger(event);
            }

            // Cancel active tap tracking
            Tap.onCancel(e);
        },

        /**
         * Cancel tap and remove event listeners for active tap tracking
         *
         * @method onTouchCancel
         * @param {jQuery.Event} e
         */
        onCancel: function(e) {
            if (e && e.type === 'touchcancel') {
                e.preventDefault();
            }

            Tap.isTracking = false;

            $BODY.off(HELPER_ACTIVE_NAMESPACE);
        },

        /**
         * If tap was canceled, cancel click event
         *
         * @method onClick
         * @param {jQuery.Event} e
         * @return {void|Boolean}
         */
        onClick: function(e) {
            if (
                !e.isTrigger &&
                _lastTap &&
                _lastTap.isDefaultPrevented() &&
                _lastTap.target === e.target &&
                _lastTap.pageX === e.pageX &&
                _lastTap.pageY === e.pageY &&
                e.timeStamp - _lastTap.timeStamp < 750
            ) {
                _lastTap = null;
                return false;
            }
        }

    };

    // Enable tab when document is ready
    $(document).ready(Tap.enable);

    // Configurable options
    $.tap = {
        POSITION_DELTA: 10, // Max distance between touchstart and touchend to be considered a tap
        TIME_DELTA: 400, // Max duration between touchstart and touchend to be considered a tap
        LEFT_BUTTON_ONLY: true // Only accept left mouse button actions
    };

}(document, jQuery));

$(document).ready(function() {
    $(".page-wrap").css("margin-bottom", -$(".footer").height());
    $("<style>.page-wrap:after{height:" + $(".footer").height() + "px}</style>").appendTo("head");
    
    var indirizzo = "/edsa-Autogestione";
    var datatable = {
        bLengthChange: false,
        paging: true,
        ordering: true,
        info: true,
        responsive: true,
        language: {
          decimal: ",",
          zeroRecords: "Nessun risultato :(",
          info: "Pagina _PAGE_ di _PAGES_",
          infoEmpty: "",
          infoFiltered: "",
          emptyTable: "Nessun risultato :(",
          infoPostFix: "",
          thousands: ".",
          loadingRecords: "Caricamento...",
          processing: "Elaborazione...",
          search: "Ricerca:",
          paginate: {
              first: "Prima",
              last: "Ultima",
              next: "Successivo",
              previous: "Precedente"
            },
          aria: {
              sortAscending: "Ordinamento alfabetico",
              sortDescending: "Ordinamento inverso"
            }
        },
        order: [1, 'desc'],
        columns: [null, { "visible": false }, { "visible": false }, { "visible": false }]
    }
    
    var show = {
        bLengthChange: false,
        bFilter: false,
        paging: false,
        ordering: true,
        info: false,
        language: {
            emptyTable: "Nessun risultato :("
        },
        responsive: true,
        columns: [null, { "visible": false }, { "visible": false }, { "visible": false }]
    }

    /*var table = $('.datatable').DataTable(datatable);*/
    var iscritto = $('#iscritto').DataTable(datatable);
    var good = $('#good').DataTable(datatable);
    var blocked = $('#blocked').DataTable(datatable);
    var to = $('#to').DataTable(datatable);
    var primo = $('#primo').DataTable(show);
    var secondo = $('#secondo').DataTable(show);
    var terzo = $('#terzo').DataTable(show);
    var first = $('#first').DataTable(datatable);
    var second = $('#second').DataTable(datatable);
    var third = $('#third').DataTable(datatable);
    
    var t1 = "primo turno";
    var t2 = "secondo turno";
    var t3 = "giornata intera (torneo)";
    
    $('.scroll').DataTable({
        paging: true,
        ordering: true,
        info: true,
        responsive: true,
        language: {
          decimal: ",",
          lengthMenu: "_MENU_ risultati per pagina",
          zeroRecords: "Nessun risultato :(",
          info: "_TOTAL_ risultati",
          infoEmpty: "",
          infoFiltered: "(filtrato da _MAX_ risultati totali)",
          emptyTable: "Nessun risultato :(",
          infoPostFix: "",
          thousands: ".",
          loadingRecords: "Caricamento...",
          processing: "Elaborazione...",
          search: "Ricerca:",
          paginate: {
              first: "Prima",
              last: "Ultima",
              next: "Successivo",
              previous: "Precedente"
            },
          aria: {
              sortAscending: "Ordinamento alfabetico",
              sortDescending: "Ordinamento inverso"
            }
        },
        scrollY: 400,
        scrollCollapse: true,
        paging: false
    });

    $(document).on('tap', '#professore', function() {
        $(this).parent().parent().find(".active").removeClass("active");
        $(this).parent().addClass("active");
        var key = $(this).find("#nome").text();
        var regExp = "."
        if (key && key !="Tutti i professori"){
            regExp = "^\\s*" + $(this).find("#nome").text() + "\\s*$";
            $("#who").text("di " + $(this).find("#nome").text());
        }
        else $("#who").text("");
        to.columns(2).search(regExp, true).draw();
        good.columns(2).search(regExp, true).draw();
        blocked.columns(2).search(regExp, true).draw();
    });
    
    $(document).on('tap', '#sort', function() {
        $(this).parent().parent().find(".active").removeClass("active");
        $(this).parent().addClass("active");
        var text = $(this).find("#val").text().toLowerCase();
        var order;
        if(text == "1") order = [ 1, 'asc' ];
        else if(text == "1d") order = [ 1, 'desc' ];
        else if(text == "2") order = [ 2, 'asc' ];
        else if(text == "2d") order =  [ 2, 'desc' ];
        else if(text == "3") order = [ 3, 'asc' ];
        else if(text == "3d") order = [3, 'desc' ];
        else order = [0, 'asc' ];
        iscritto.order(order).draw();
        good.order(order).draw();
        blocked.order(order).draw();
        to.order(order).draw();
        primo.order(order).draw();
        secondo.order(order).draw();
        terzo.order(order).draw();
        first.order(order).draw();
        second.order(order).draw();
        third.order(order).draw();
    });

    $(document).on('tap', '#reset', function() {
        var button = $(this);
        button.html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        $.ajax({
            method: "POST",
            url: indirizzo + "/templates/ajax/reset.php",
            data: {
                id: button.parent().find("#value").text()
            }
        }).done(function(msg) {
            button.parent().html(msg);
        });
    });

    $(document).on('tap', '#presenza', function() {
        var button = $(this);
        button.html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        button.removeClass("btn-success");
        button.removeClass("btn-danger");
        button.addClass("btn-info");
        $.ajax({
            method: "POST",
            url: indirizzo + "/templates/ajax/presente.php",
            data: {
                id: button.parent().find("#persona").text(),
                corso: $("#value").text()
            }
        }).done(function(msg) {
            if (msg == 1) {
                button.addClass("btn-danger");
                button.removeClass("btn-info");
                button.parent().parent().find("#pres").html("Attualmente presente");
                button.html("Assente");
            }
            else if (msg == 0) {
                button.removeClass("btn-info");
                button.addClass("btn-success");
                button.parent().parent().find("#pres").html("Attualmente assente");
                button.html("Presente");
            }
        });
    });

    $(document).on('tap', '#like', function() {
        var button = $(this);
        var parent = $(this).parent().parent().parent();
        button.find("#text").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        button.removeClass("btn-success").removeClass("btn-danger").addClass("btn-info disabled");
        $.ajax({
            method: "POST",
            url: indirizzo + "/templates/ajax/like.php",
            data: {
                id: parent.find("#value").text(),
                page: $("#page").text()
            }
        }).done(function(msg) {
            if (msg == 1) {
                button.removeClass("btn-info disabled").addClass("btn-danger").find("#text").html('<i class="fa fa-thumbs-o-up"></i>');
                parent.parent().parent().find("#cont").text(parseInt(button.parent().find("#cont").text()) + 1);
            }
            else if (msg == 0) {
                button.removeClass("btn-info disabled").addClass("btn-success").find("#text").html('<i class="fa fa-thumbs-o-up"></i>');
                parent.parent().parent().find("#cont").text(parseInt(button.parent().find("#cont").text()) - 1);
            }
        });
    });
    
    function switch_row(parent, fromTable, toTable){
        var addRow = fromTable.row(parent);
        var row = toTable.row.add(addRow.data()).draw().node();
        addRow.remove().draw();
        check();
        return row;
    }
    
    function isnull(something){
        return (typeof something === "undefined");
    }
    
    function check(){
        /*if(primo.data().length === 0) $("#primo").parent().addClass("hidden");
        else $("#primo").parent().removeClass("hidden");
        if(secondo.data().length === 0) $("#secondo").parent().addClass("hidden");
        else $("#secondo").parent().removeClass("hidden");
        if(terzo.data().length === 0) $("#terzo").addClass("hidden");
        else $("#terzo").removeClass("hidden");*/
        if(primo.data().length === 0 && secondo.data().length === 0 && terzo.data().length === 0) $("#iscrizioni").addClass("hidden");
        else $("#iscrizioni").removeClass("hidden");
    }
    
    check();
    
    $(document).on('tap', '#iscriviti', function() {
        var button = $(this);
        var parent = $(this).parent().parent().parent().parent().parent();
        var orario = parent.find("#orario").text().toLowerCase();
        button.removeClass("btn-success").removeClass("btn-danger").addClass("btn-info disabled").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        $.ajax({
            method: "POST",
            url: indirizzo + "/templates/ajax/iscriviti.php",
            data: {
                id: parent.find("#value").text(),
                page: $("#page").text()
            }
        }).done(function(msg) {
            var row;
            if (msg == 1) {
                if (orario && $("#page").text().toLowerCase() == "corsi") {
                    if (orario == t1) {
                        row = switch_row(parent, first, primo);
                        $("#first").find("#iscriviti").parent().remove();
                        $("#third").find("#iscriviti").parent().remove();
                    }
                    else if (orario == t2) {
                        row = switch_row(parent, second, secondo);
                        $("#second").find("#iscriviti").parent().remove();
                        $("#third").find("#iscriviti").parent().remove();
                    }
                    else if (orario == t3) {
                        row = switch_row(parent, third, terzo);
                        $(row).find(".links").append('<li><a id="sqaud" href=indirizzo + "/squadra" class="btn btn-primary btn-lg">Crea squadra</a></li>');
                        $("#first").find("#iscriviti").parent().remove();
                        $("#second").find("#iscriviti").parent().remove();
                        $("#third").find("#iscriviti").parent().remove();
                    }
                    else row = switch_row(parent, good, sub);
                    $(row).find("#orario").parent().parent().removeClass("hidden");
                }
                else if (orario == t3) $(row).find(".links").append('<li><a id="sqaud" href=indirizzo + "/squadra" class="btn btn-primary btn-lg">Crea squadra</a></li>');
                if (!isnull($(row).find("#iscriviti").html())) $(row).find("#iscriviti").removeClass("btn-success").addClass("btn-danger").html('<i class="fa fa-close"></i> Elimina iscrizione');
                else $(row).find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-close"> Elimina iscrizione</a></li>');
                $(row).find("#number").text(parseInt(parent.find("#number").text()) + 1);
                $(row).find("#stato").removeClass("btn-success").addClass("btn-warning").html('<i class="fa fa-eye-slash"></i> Blocca');
            }
            else if (msg == 0) {
                if (orario && $("#page").text().toLowerCase() == "corsi") {
                    if (orario == t1) {
                        $("#first").find("section").not('.yellow').find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"></i> Iscriviti</a></li></li>');
                        if (secondo.data().length === 0 ) {
                            $("#third").find("section").not('.yellow').find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"></i> Iscriviti</a></li>');
                        }
                        row = switch_row(parent, primo, first);
                    }
                    else if (orario == t2) {
                        $("#second").find("section").not('.yellow').find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"></i> Iscriviti</a></li>');
                        if (primo.data().length === 0 ) $("#third").find("section").not('.yellow').find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"></i> Iscriviti</a></li>');
                        row = switch_row(parent, secondo, second);
                    }
                    else if (orario == t3) {
                        
                        $("#first").find("section").not('.yellow').find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"></i> Iscriviti</a></li>');
                        $("#second").find("section").not('.yellow').find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"></i> Iscriviti</a></li>');
                        $("#third").find("section").not('.yellow').find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"></i> Iscriviti</a></li>');
                        row = switch_row(parent, terzo, third);
                    }
                    else row = switch_row(parent, iscritto, good);
                    $(row).find("#orario").parent().parent().addClass("hidden");
                }
                if (!isnull($(row).find("#iscriviti").html())) $(row).find("#iscriviti").removeClass("btn-danger").addClass("btn-success").html('<i class="fa fa-check"></i> Iscriviti');
                else $(row).find(".links").prepend('<li><a id="iscriviti" class="btn btn-danger"><i class="fa fa-check"></i> Iscriviti</a></li>');
                $(row).find("#number").text(parseInt(parent.find("#number").text()) - 1);
                $(row).find("#squad").parent().remove();
                $(row).find("#stato").removeClass("btn-success").addClass("btn-warning").html('<i class="fa fa-eye-slash"></i> Blocca');
            }
            $(row).find(".progress-bar").attr("aria-valuenow", parseInt(parent.find("#number").text()) * 100 / parseInt(parent.find("#max").text()));
            $(row).find(".progress-bar").css("width", parseInt(parent.find("#number").text()) * 100 / parseInt(parent.find("#max").text()) + "%");
        });
    });
    
    $(document).on('tap', '#stato', function() {
        var button = $(this);
        var parent = $(this).parent().parent().parent().parent().parent();
        var orario = parent.find("#orario").text().toLowerCase();
        button.removeClass("btn-success").removeClass("btn-warning").addClass("btn-info disabled").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        $.ajax({
            method: "POST",
            url: indirizzo + "/templates/ajax/stato.php",
            data: {
                id: parent.find("#value").text(),
                page: $("#page").text()
            }
        }).done(function(msg) {
            var row;
            if (msg == 1) {
                if(orario){
                    if (orario == t1) {
                        if(parent.find("#orario").parent().parent().hasClass("hidden")) row = switch_row(parent, first, blocked);
                        else{
                            $("#first").find("section").not('.yellow').find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"> Iscriviti</a></li>');
                            if (secondo.data().length === 0 ) $("#third").find("section").find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"> Iscriviti</a></li>');
                            row = switch_row(parent, primo, blocked);
                        }
                    }
                    else if (orario == t2) {
                        if(parent.find("#orario").parent().parent().hasClass("hidden")) row = switch_row(parent, second, blocked);
                        else{
                            $("#second").find("section").not('.yellow').find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"> Iscriviti</a></li>');
                            if (primo.data().length === 0 ) $("#third").find("section").find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"> Iscriviti</a></li>');
                            row = switch_row(parent, secondo, blocked);
                        }
                    }
                    else if (orario == t3) {
                        if(parent.find("#orario").parent().parent().hasClass("hidden")) row = switch_row(parent, third, blocked);
                        else{
                            $("#first").find("section").not('.yellow').find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"> Iscriviti</a></li>');
                            $("#second").find("section").not('.yellow').find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"> Iscriviti</a></li>');
                            $("#third").find("section").not('.yellow').find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"> Iscriviti</a></li>');
                            row = switch_row(parent, terzo, blocked);
                        }
                    }
                    $(row).find("#orario").parent().parent().removeClass("hidden");
                }
                else row = switch_row(parent, good, blocked);
                $(row).find("#stato").removeClass("btn-warning").addClass("btn-success").html('<i class="fa fa-eye"></i> Abilita');
                $(row).find("#squad").parent().remove();
                $(row).find("#like").parent().remove();
                $(row).find(".level").remove();
                $(row).find("#iscriviti").parent().remove();
            }
            else if (msg == 0) {
                if(orario){
                    if (orario == t1) row = switch_row(parent, blocked, first);
                    else if (orario == t2) row = switch_row(parent, blocked, second);
                    else if (orario == t3) row = switch_row(parent, blocked, third);
                    $(row).find("#orario").parent().parent().addClass("hidden");
                    orario = $(row).find("#orario").text().toLowerCase();
                    if ($(row).find("section").not(".yellow")) {
                        if ((orario == t1 && primo.data().length === 0  && terzo.data().length === 0 ) || (orario == t2 && secondo.data().length === 0  && terzo.data().length === 0 ) || (orario == t3 && primo.data().length === 0  && secondo.data().length === 0  && terzo.data().length === 0 )) {
                            if (!isnull($(row).find("#iscriviti").html())) $(row).find("#iscriviti").removeClass("btn-danger").addClass("btn-success").html('<i class="fa fa-check"></i> Iscriviti');
                            else $(row).find(".links").prepend('<li><a id="iscriviti" class="btn btn-success"><i class="fa fa-check"> Iscriviti</a></li>');
                        }
                        else if ($(row).find("#iscriviti")){
                            $(row).find("#iscriviti").parent().remove();
                        }
                        
                    }
                }
                else {
                    if(parent.parent().parent().attr("id") == "to") row = switch_row(parent, to, good);
                    else row = switch_row(parent, blocked, good);
                    if (isnull($(row).find("#like").html())) $(row).find(".links").prepend('<li><a id="like" class="btn btn-success"><span id="text"><i class="fa fa-thumbs-o-up"></i></span> <span id="cont">0</span></a></li>');
                }
                $(row).find("#stato").removeClass("btn-success").addClass("btn-warning").html('<i class="fa fa-eye-slash"></i> Blocca');
                $(row).find("#dis").remove();
            }
            $(row).find("#cambia").parent().remove();
        });
    });

    $(document).on('tap', '#cambia', function() {
        var button = $(this);
        button.html('<i class="fa fa-circle-o-notch fa-spin"></i>').addClass("disabled");
        $.ajax({
            method: "POST",
            url: indirizzo + "/templates/ajax/cambia.php",
            data: {
                id: button.parent().parent().parent().find("#value").text(),
                page: $("#page").text()
            }
        }).done(function(msg) {
            var parent = button.parent().parent().parent().parent().parent();
            var row = switch_row(parent, to, blocked);
            $(row).find("#cambia").parent().remove();
            $("#change").text(parseInt($("#change").text()) - 1);
        });
    });
});