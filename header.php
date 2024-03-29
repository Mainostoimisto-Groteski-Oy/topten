<?php
/**
 * The header for our theme that uses hamburger menu and no site branding
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Topten
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<link rel="preload" as="style" href="<?php echo esc_url( get_template_directory_uri() . '/fonts/blinker/blinker.css' ); ?>" onload="this.rel='stylesheet'">

	<link rel="preload" as="style" href="<?php echo esc_url( get_template_directory_uri() . '/fonts/roboto/roboto.css' ); ?>" onload="this.rel='stylesheet'">

	<link rel="preload" as="style" href="<?php echo esc_url( get_template_directory_uri() . '/fonts/MaterialSymbols/MaterialSymbols.css' ); ?>" onload="this.rel='stylesheet'">

	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary">
		<?php esc_html_e( 'Skip to content', 'topten' ); ?>
	</a>

	<header id="masthead" class="site-header">
		<div class="grid">
			<div class="container">

				<?php if ( get_field( 'show_topten_logo', 'options' ) && ! get_field( 'topten_logo_home_link', 'options' ) ) : ?>

				<div class="topten-branding">
					<?php $logo = get_field( 'topten_logo', 'options' ); ?>
					<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" />
				</div>

				<?php else : ?>

				<a class="topten-branding" href="<?php echo esc_url( home_url() ); ?>">
					<?php $logo = get_field( 'topten_logo', 'options' ); ?>
					<span class="screen-reader-text"><?php echo esc_html_e( 'Palaa etusivulle', 'topten' ); ?></span>

					<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" />
				</a>

				<?php endif; ?>

				<button class="menu-toggle" id="toggleMenu" aria-label="Valikko" aria-controls="site-navigation" aria-expanded="false">
					<span class="material-symbols" aria-hidden="true">
						menu
					</span>
				</button>

				<?php if ( get_field( 'show_rty_logo', 'options' ) && ! get_field( 'rty_logo_link', 'options' ) ) : ?>

					<div class="rty-branding">
						<?php $logo = get_field( 'rty_logo', 'options' ); ?>
						<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" />
					</div>

				<?php else : ?>

					<a class="rty-branding" href="<?php echo esc_url( get_field( 'rty_logo_link_url', 'options' ) ); ?>">
						<?php $logo = get_field( 'rty_logo', 'options' ); ?>
						<span class="screen-reader-text"><?php echo esc_html_e( 'Rakennustarkastusyhdistyksen sivuille', 'topten' ); ?></span>
						<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" />
					</a>

				<?php endif; ?>
			</div>
		</div>
	</header>

	<nav id="site-navigation" class="main-navigation">
		<?php
		wp_nav_menu(
			array(
				'theme_location'  => 'primary-menu',
				'menu_id'         => 'primary-menu',
				'container_class' => 'menu-container grid',
				'link_before'     => '<span class="link-text">',
				'link_after'      => '</span>',
			)
		);
		?>
	</nav>
