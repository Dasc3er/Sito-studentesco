$(document).ready(function() {
	var offset = 10,
		//browser window scroll (in pixels) after which the "back to top" link opacity is reduced
		offset_opacity = 100,
		//duration of the top scrolling animation (in ms)
		scroll_top_duration = 700;

	//smooth scroll to top
	$("#top").on('click', function(event) {
		event.preventDefault();
		$('body,html').animate({
			scrollTop: 0,
		}, scroll_top_duration);
	});

	$('.alert-success').removeClass('hidden');
	//Messaggio di avviso salvataggio a comparsa sulla destra solo nella versione a desktop intero
	if ($(window).width() > 1023) {
		var i = 0;

		$('.alert-success').each(function() {
			i++;
			tops = 60 * i + 25;

			$(this).css({
				'position': 'absolute',
				'top': -100,
				'right': 10,
				'opacity': 1
			}).delay(1000).animate({
				'top': tops,
				'opacity': 1
			}).delay(3000).animate({
				'top': -100,
				'opacity': 0
			});

			$(this).html($(this).find('.container').html());
		});
	}
});
