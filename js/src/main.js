jQuery(document).ready(($) => {
	/**
	 * Menun avaus
	 */
	$(document).on('click', '#toggleMenu', () => {
		if ($('#toggleMenu').hasClass('open')) {
			$('#site-navigation').fadeOut(300);
			$('#masthead').removeClass('open');
			$('#toggleMenu').removeClass('open');
			$('#toggleMenu').attr('aria-expanded', 'false');
			$('#site-navigation').attr('aria-expanded', 'false');
			$('body').removeClass('no-scroll');
		} else {
			$('#site-navigation').fadeIn(300);
			$('#toggleMenu').addClass('open');
			$('#masthead').addClass('open');
			$('#toggleMenu').attr('aria-expanded', 'true');
			$('#site-navigation').attr('aria-expanded', 'true');
			$('body').addClass('no-scroll');
		}
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
		'<button class="nav-button"><span class="material-symbols-outlined nav-icon">add</span></button>'
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
