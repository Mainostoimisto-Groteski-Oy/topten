jQuery(document).ready(($) => {
	if (
		$('body').hasClass('single-tulkintakortti') ||
		$('body').hasClass('single-ohjekortti') ||
		$('body').hasClass('single-lomakekortti')
	) {
		if ($('aside.sidebar div.box').length > 0) {
			$('aside.sidebar div.box .box-title').on('click', function () {
				$(this).parent().toggleClass('open');
				// toggle this child button aria-expanded attribute
				$(this)
					.children('button')
					.attr('aria-expanded', (i, attr) => (attr === 'true' ? 'false' : 'true'));
				// toggle this parent child box-content aria-expanded attribute
				$(this)
					.parent()
					.children('.box-content')
					.attr('aria-expanded', (i, attr) => (attr === 'true' ? 'false' : 'true'));
			});
			if (jQuery('.gform_wrapper form .gfield.card-name textarea').length > 0) {
				jQuery('.gform_wrapper form .gfield.card-name textarea').attr('disabled', 'disabled');
			}
		}
	}
});
