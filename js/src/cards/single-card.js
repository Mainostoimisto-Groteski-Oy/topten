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

			if ($('.gform_wrapper form .gfield.card-name textarea').length > 0) {
				$('.gform_wrapper form .gfield.card-name textarea').attr('disabled', 'disabled');
			}
		}
		$('#toggleSidebar').on('click', function () {
			$('aside.sidebar').toggleClass('active');

			$(this).toggleClass('active');

			// aria-expanded attribute for this and sidebar
			$(this).attr('aria-expanded', (i, attr) => (attr === 'true' ? 'false' : 'true'));

			// Aside should not have aria-expanded attribute
			// $('aside.sidebar').attr('aria-expanded', (i, attr) => (attr === 'true' ? 'false' : 'true'));

			// toggle menu-explanation
			if ($('aside.sidebar').hasClass('active')) {
				$('.menu-explanation.closed').removeClass('active');
				$('.menu-explanation.open').addClass('active');
			} else {
				$('.menu-explanation.closed').addClass('active');
				$('.menu-explanation.open').removeClass('active');
			}
		});
	}
	// Show info boxes and set their position correctly on mouseenter, hide on mouseout
	$('#sidebar-menu .keywords .keyword a.keyword-link').on('mouseenter', function () {
		const id = $(this).attr('data-id');
		// get sidebar width
		const sidebarWidth = $('#sidebar-menu').width();
		// set keyword-description-container width to sidebar width
		$('.keyword-description-container').css('width', sidebarWidth + 'px');
		// get height of keyword-description-container and add some space
		const height = $('.keyword-description-container[id=desc-' + id + ']').height() + 35;
		// get left border of ul.keywords
		const left = $('#sidebar-menu .keywords').offset().left;
		// get left border of keyword
		const keywordLeft = $(this).offset().left;
		// get width of keyword
		const keywordWidth = $(this).parent('.keyword').innerWidth();
		// calculate left offset for keyword-description-container
		let leftOffset = keywordLeft - left - keywordWidth;
		if (leftOffset < 0) {
			// Don't want this to go out of bounds
			leftOffset = -1;
		} else {
			// -30 because of padding
			leftOffset = leftOffset - leftOffset * 2 - 30;
		}
		// set left offset for keyword-description-container
		$('.keyword-description-container[id=desc-' + id + ']').css('left', leftOffset + 'px');

		// keyword-description-container should appear a bit above keyword
		$('.keyword-description-container[id=desc-' + id + ']').css('top', -height + 'px');
		// set to active (shown) on mouseenter
		$('.keyword-description-container[id=desc-' + id + ']').addClass('active');
	});
	$('#sidebar-menu .keywords .keyword .keyword-link').on('mouseleave', function () {
		$('.keyword-description-container').removeClass('active');
	});
});
