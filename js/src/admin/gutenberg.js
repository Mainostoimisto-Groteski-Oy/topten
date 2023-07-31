jQuery(document).ready(($) => {
	// Width inputs
	const selector =
		'.post-type-tulkintakortti .acf-input .acf-range-wrap input[type="range"], .post-type-ohjekortti .acf-input .acf-range-wrap input[type="range"], .post-type-lomakekortti .acf-input .acf-range-wrap input[type="range"]';

	// Fix preview widths
	function fixWidths() {
		$(this)
			.closest('.wp-block')
			.css('width', `calc(${$(this).val()}% - 10px)`);
	}

	// Fix widths on change
	$(document).on('change', selector, function () {
		fixWidths.call(this);
	});

	// Gutenberg/ACF doesn't seem to have a ready event, so we need to poll
	const interval = setInterval(() => {
		if ($(selector).length > 0) {
			$(selector).each(function () {
				fixWidths.call(this);
			});

			// Stop interval if we have found the elements
			clearInterval(interval);
		}
	}, 1000);

	// In all cases stop the interval after 30 seconds
	setTimeout(() => {
		clearInterval(interval);
	}, 30000);
});
