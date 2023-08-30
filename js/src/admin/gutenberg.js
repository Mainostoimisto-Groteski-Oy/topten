jQuery(document).ready(($) => {
	// Width inputs
	const selector =
		'.post-type-tulkintakortti .acf-input .acf-range-wrap input[type="range"], .post-type-ohjekortti .acf-input .acf-range-wrap input[type="range"], .post-type-lomakekortti .acf-input .acf-range-wrap input[type="range"]';

	const previewSelector =
		'.post-type-tulkintakortti .acf-block-preview .column-item, .post-type-lomakekortti .acf-block-preview .column-item, .post-type-ohjekortti .acf-block-preview .column-item';

	// Fix preview widths
	function fixWidths() {
		$(this)
			.closest('.wp-block')
			.css('width', `calc(${$(this).val()}% - 10px)`);
	}

	// Fix preview widths
	function fixPreviewWidths() {
		const width = $(this).css('--width');

		$(this).closest('.wp-block').css('width', `calc(${width} - 10px)`);

		if ($(this).attr('id') === 'block-f8634fab-40f4-47eb-a792-1e9f07dde401') {
			// Todo?
			// console.log($(this).closest('.wp-block'));
		}
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
		}

		if ($(previewSelector).length > 0) {
			$(previewSelector).each(function () {
				fixPreviewWidths.call(this);
			});
		}
	}, 1000);

	// In all cases stop the interval after 30 seconds
	setTimeout(() => {
		clearInterval(interval);
	}, 20000);
});
