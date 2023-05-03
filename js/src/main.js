jQuery(document).ready(($) => {
	/**
	 * Mobiilimenun avaus
	 */
	$(document).on('click', '#toggleMenu', () => {
		if($('#toggleMenu').hasClass('open')) {
			$('#mobile-navigation').fadeOut(300);
			$('body').removeClass('disable-scroll');
			$('#toggleMenu').removeClass('open');
		} else {
			$('#mobile-navigation').fadeIn(300);
			$('body').addClass('disable-scroll');
			$('#toggleMenu').addClass('open');
			// Set top value based on header items height and scroll position
			const logoheader = $('#logoheader').height();
			const masthead = $('#masthead').height();
			const checkBar = $('#wpadminbar');
			let adminbar = 0;
			let top = 0;
			let check = 0;
			// check adminbar exists
			if(checkBar.length > 0) {
				adminbar = $('#wpadminbar').height();
			}
			if(adminbar > 0) {
				check = masthead + adminbar;
				top = logoheader + masthead + adminbar;
			} else {
				check = masthead;
				top = logoheader + masthead;
			}
			// If scrolltop value is bigger than top value, use check value
			if($(window).scrollTop() > top) {
				$('#mobile-navigation').css('top', check);
			} else {
				// in other case use top value minus scrolltop value
				const scrollvalue = top - $(window).scrollTop();
				$('#mobile-navigation').css('top', scrollvalue);
				
			}
		}
		
	});

	/**
	 * Mobiilimenun sulkeminen
	 */
	$('#close-mobile-menu').on('click', () => {
		$('#mobile-navigation').fadeOut(300);

		$('body').removeClass('disable-scroll');
	});

	/**
	 * Alavalikon avaus
	 */
	$('#primary-menu li.menu-item-has-children > a').on('click', function (event) {
		event.preventDefault();

		const submenu = $(this).parent().children('ul.sub-menu');

		if ($(submenu).hasClass('open')) {
			$(submenu).removeClass('open');

			$(submenu).fadeOut(200);
		} else {
			$(submenu).addClass('open');

			$(submenu).fadeIn(200);
		}
	});

	$('.sidebar-navigation li.page_item_has_children > a').after(
		'<button class="nav-button"><span class="material-icons-outlined nav-icon">add</span></button>'
	);

	$(document).on('click', '.page_item_has_children button.nav-button', function () {
		if ($(this).hasClass('open')) {
			$(this).removeClass('open');

			$(this).children('.nav-icon').text('add');

			$(this).parent().children('.children').fadeOut(200);
		} else {
			$(this).addClass('open');

			$(this).children('.nav-icon').text('remove');

			$(this).parent().children('.children').fadeIn(200);
		}
	});

	/**
	 * Asettaa navigaation korkeuden CSS-muuttujaan --navbar-height
	 */
	function setNavbarHeight() {
		const navbar = $('.site-header').height();

		$(':root').css('--navbar-height', `${navbar}px`);
	}

	// Asetetaan korkeus heti sivun latauksen jälkeen
	setNavbarHeight();

	// Asetetaan korkeus myös ikkunan koon muuttuessa
	$(window).resize(() => {
		setNavbarHeight();
	});
});
