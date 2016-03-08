<?php
echo '
        </div>
        <footer class="footer">
            <div class="container">
                <p class="text-center"><a href="#"><i class="fa fa-chevron-up"></i></a></p>
                <p>Progettato e sviluppato da Thomas Zilio.</p>
                <p>Rappresentanti di Istituto che hanno promosso il progetto: Marco Barbin, Cristian Bussolin, Paolo Giacomin, Victor Matvei</p>
                <ul class="links">
                    <li>Link utili:</li>
                    <li><a href="https://github.com/dasc3er/sito-studentesco" target="_blank"><i class="fa fa-github"></i> GitHub</a></li>
                </ul>
            </div>
        </footer>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>';
if (isset($editor) && $editor) echo '
        <script src="' . $dati['info']['root'] . 'vendor/tinymce/tinymce/tinymce.min.js"></script>
        <script type="text/javascript">
            tinyMCE.init({
                selector: "#txtEditor",
                theme: "modern",
                /*language: "it",*/
                menubar: false,
                statusbar: false,
                min_height: 300,
                toolbar: ["bold italic underline strikethrough | link image | alignleft aligncenter alignright | undo redo"]
            });
        </script>';
if (isset($readmore) && $readmore) echo '
        <script src="' . $dati['info']['path'] . 'js/readmore.min.js"></script>
        <script>
            $("*").find(\'#descrizione\').each(function() {
                $(this).readmore({
                    collapsedHeight: 55,
                    speed: 200,
                    moreLink: \'<a href="#">Leggi di pi&ugrave; <i class="fa fa-chevron-right"></i></a>\',
                    lessLink: \'<a href="#"><i class="fa fa-chevron-left"> Compatta</i></a>\'
                });
            });
        </script>';
if (isset($datatable) && $datatable) {
    echo '
        <script src="' .
             $dati['info']['root'] . 'vendor/datatables/datatables/media/js/jquery.dataTables.min.js"></script>
        <script src="' .
             $dati['info']['root'] . 'vendor/datatables/datatables/media/js/dataTables.bootstrap.min.js"></script>
        <script async src="' . $dati['info']['path'] . 'js/minified.js" type="text/javascript"></script>
        <script async src="' . $dati['info']['path'] .
             'js/jquery.tap.min.js" type="text/javascript"></script>';
}
if (isset($complexify) && $complexify) echo '
        <script src="' . $dati['info']['path'] . 'js/jquery.complexify.min.js" type="text/javascript"></script>
        <script>
            function check(){
                if ($("#Password").val() != $("#RipPassword").val()) {
                    $("#Password_error").html("<p>Le due password non corrispondono</p>");
                }
                else $("#Password_error").html("");
            }
            $("#Password").keyup(function(){check()}).keyup();
            $("#RipPassword").keyup(function(){check()}).keyup();
            $("#Password").complexify({}, function (valid, complexity) {
                var progressBar = $("#Password_bar");

                progressBar.toggleClass("progress-bar-success", valid && complexity > 90);
                progressBar.toggleClass("progress-bar-info", valid && complexity > 60 && complexity < 90);
                progressBar.toggleClass("progress-bar-warning", valid && complexity > 30 && complexity < 60);
                progressBar.toggleClass("progress-bar-danger", !valid || complexity < 30);
                progressBar.css({
                    "width" : complexity + "%"
                });
                if (complexity > 90) {
                    $("#Password_text").text("Molto sicura");
                } else if (complexity > 60) {
                    $("#Password_text").text("Sicura");
                } else if (complexity > 30) {
                    $("#Password_text").text("Media");
                } else {
                    $("#Password_text").text("Insicura");
                }
            });
        </script>';
if (isset($style) && $style) echo '
        <script>
            $("#stile").change(function(){
                $("#css").remove();
                if($("#stile :selected").text().toLowerCase() == "boostrap (default)"){
                    $(\'<link id="css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">\').prependTo("#font");
                }
                else {
                    $(\'<link id="css" href="' .
         $dati['info']['root'] . 'vendor/thomaspark/bootswatch/\'+$("#stile :selected").val()+\'/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css">\').prependTo("#font");
                }
            });
        </script>';
if (isset($wait) && $wait) echo '
        <script>
            setInterval(function() {
                if (parseInt($("#time").text()) > 0) $("#time").html(parseInt($("#time").text()) - 1);
                else {
                    $("#user").prop("disabled", false);
                    $("#password").prop("disabled", false);
                    $("#button").removeClass("hidden");
                }
            }, 1000);
        </script>';
echo '
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>';
if ($dati['opzioni']['snow']) echo '
        <script async type="text/javascript" src="' . $dati['info']['path'] . 'js/jquery.let_it_snow.js"></script>
        <script async>
            $("canvas.snow").let_it_snow({
                windPower: 3,
                speed: 1,
                size: 2,
                color: "#ffffff",
            });
        </script>';
echo '
    </body>
</html>';
if ($dati['opzioni']['time']) {
    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];
    $endtime = $mtime;
    $totaltime = ($endtime - $starttime);
    echo "Pagina creata in " . $totaltime . " secondi";
}
?>