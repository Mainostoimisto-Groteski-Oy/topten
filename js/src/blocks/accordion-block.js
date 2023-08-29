jQuery(document).ready(($) => {
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
