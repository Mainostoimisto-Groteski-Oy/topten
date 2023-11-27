jQuery(document).ready(($) => {
	if (
		$('body').hasClass('single-tulkintakortti') ||
		$('body').hasClass('single-ohjekortti') ||
		$('body').hasClass('single-lomakekortti') ||
		$('body').hasClass('page-template-template-vertaa')
	) {
		if ($('aside.sidebar div.box').length > 0) {
			$('aside.sidebar div.box .box-title').on('click', function () {
				$(this).parent().toggleClass('open');
				// toggle this child button aria-expanded attribute

				$(this)
					.children('button')
					.attr('aria-expanded', (i, attr) => (attr === 'true' ? 'false' : 'true'));

				// aria-expanded should be on the button, not the box-content
				if ($(this).parent().hasClass('open')) {
					$(this).parent().children('.box-content').show();
				} else {
					$(this).parent().children('.box-content').hide();
				}

				// toggle this parent child box-content aria-expanded attribute
				// $(this)
				// 	.parent()
				// 	.children('.box-content')
				// 	.attr('aria-expanded', (i, attr) => (attr === 'true' ? 'false' : 'true'));
			});
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

		// Is this actually needed? top: 150px; should have the same effect (if position: sticky)
		// $(window).on('scroll', function () {
		// if ($(window).scrollTop() > 0) {
		// 	$('aside.sidebar .boxes').css('top', '150px');
		// } else {
		// 	$('aside.sidebar .boxes').css('top', '0px');
		// }
		// });

		$('.card-text-block .text-wrapper').each(function () {
			if ($(this).children('table').length > 0) {
				$(this).addClass('tabled');
				$(this).parent('.column-item').parent('.column').css('overflow', 'hidden');
				const parentWidth = $(this).parent('.card-text-block').width();
				$(this).css('width', parentWidth + 'px');
			}
		});
		$(window).on('resize', function () {
			$('.card-text-block .text-wrapper').each(function () {
				if ($(this).children('table').length > 0) {
					const parentWidth = $(this).parent('.card-text-block').width();
					$(this).css('width', parentWidth + 'px');
				}
			});
		});
	}

	// Show info boxes and set their position correctly on mouseenter, hide on mouseout
	$('.sidebar .keywords .keyword button').on('click', function () {
		$('.keyword-description-container').removeClass('active');

		// Set aria-expanded attribute to true
		$(this).attr('aria-expanded', 'true');

		const controls = $(this).attr('aria-controls');

		// get sidebar width
		const sidebarWidth = $('.sidebar').width();

		// set keyword-description-container width to sidebar width
		$('.keyword-description-container').css('width', sidebarWidth + 'px');

		const tooltip = $('.keyword-description-container#' + controls);

		// get height of keyword-description-container and add some space
		const height = tooltip.height() + 35;

		// get left border of ul.keywords
		const left = $('.sidebar .keywords').offset().left;

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
		tooltip.css('left', leftOffset + 'px');

		// keyword-description-container should appear a bit above keyword
		tooltip.css('top', -height + 'px');

		// set to active (shown) on mouseenter
		tooltip.addClass('active');
	});

	$(document).on('click', '.keyword.close-button', function () {
		$('.keyword-description-container').removeClass('active');

		// Set aria-expanded attribute to false
		$('.sidebar .keywords .keyword button').attr('aria-expanded', 'false');
	});

	// Close the keyword-description-container with esc
	$(document).keyup(function (e) {
		if (e.keyCode === 27) {
			$('.keyword-description-container').removeClass('active');

			// Set aria-expanded attribute to false
			$('.sidebar .keywords .keyword button').attr('aria-expanded', 'false');
		}
	});

	/* $('.sidebar .keywords .keyword .keyword-link').on('mouseleave', function () {
		$('.keyword-description-container').removeClass('active');
	}); */
});
