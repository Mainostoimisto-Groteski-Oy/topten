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

	// If editor is disabled, append text
	if ($('body').hasClass('tt-editor-disabled')) {
		$('#editor').append(
			'<div class="tt-editor-disabled-message"><p>Kortti odottaa hyväksyntää, eikä sitä voi tällä hetkellä muokata.</p></div>',
		);
	}
});
