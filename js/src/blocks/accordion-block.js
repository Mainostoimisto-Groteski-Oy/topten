jQuery(document).ready(($) => {
	if ($('.accordion-title').length > 0 && $(location).attr('hash').length > 0) {
		// find accordion title with this hash
		const hash = $(location).attr('hash');
		if ($('.accordion-title' + hash).length > 0) {
			$('.accordion-title' + hash).addClass('toggled');
			$('.accordion-title' + hash).attr('aria-expanded', 'true');
			const noTagHash = hash.replace('#', '');
			$('#text-' + noTagHash).addClass('toggled');
		}
	}

	$('.accordion-title').on('click', function (event) {
		event.preventDefault();

		$(this).toggleClass('toggled');

		const controls = $(this).attr('aria-controls');

		if ($(`#${controls}`).hasClass('toggled')) {
			$(`#${controls}`).removeClass('toggled');

			$(this).attr('aria-expanded', 'false');
		} else {
			$(`#${controls}`).addClass('toggled');

			$(this).attr('aria-expanded', 'true');
		}
	});
});
