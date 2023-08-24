jQuery(document).ready(($) => {
	if ($('section.cards-notification').length > 0) {
		const breadcrumbs = $('section.page-breadcrumbs').outerHeight();
		const masthead = $('#masthead').outerHeight();
		let wpadminbar = '';
		if ($('#wpadminbar').length > 0) {
			wpadminbar = $('#wpadminbar').outerHeight();
		} else {
			wpadminbar = 0;
		}
		// count these together
		const combinedHeight = breadcrumbs + wpadminbar;
		const topValue = masthead + wpadminbar;

		// on scroll past combinedHeight add class to cards-notification
		$(window).scroll(() => {
			if ($(window).scrollTop() > combinedHeight) {
				$('section.cards-notification').addClass('scrolled');
				$('section.cards-notification').css('top', topValue);
				// add cards-notification worth of padding to body
				$('body').css('padding-top', $('section.cards-notification').outerHeight());
			} else {
				$('section.cards-notification').removeClass('scrolled');
				$('section.cards-notification').css('top', 0);
				// remove cards-notification worth of padding to body
				$('body').css('padding-top', 0);
			}
		});
	}
});
