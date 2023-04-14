<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Groteski
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

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
			<div class="site-branding">
				<?php the_custom_logo(); ?>
			</div>

			<nav id="site-navigation" class="main-navigation">
				<?php
				wp_nav_menu(
					array(
						'theme_location'  => 'primary-menu',
						'menu_id'         => 'primary-menu',
						'container_class' => 'menu-container',
						'link_before'     => '<span class="link-text">',
						'link_after'      => '</span>',
					)
				);
				?>
			</nav>

			<button class="menu-toggle" id="open-mobile-menu" aria-label="Avaa valikko" aria-controls="mobile-menu" aria-expanded="false">
				<span class="material-icons" aria-hidden="true">
					menu
				</span>
			</button>
		</div>
	</header>

	<nav id="mobile-navigation" class="mobile-menu">
		<div class="site-branding-mobile">
			<?php the_custom_logo(); ?>
		</div>
		<?php
		wp_nav_menu(
			array(
				'theme_location'  => 'primary-menu',
				'menu_id'         => 'mobile-menu',
				'container_class' => 'menu-container',
			)
		);
		?>

		<button class="menu-close" id="close-mobile-menu" aria-label="Sulje valikko" aria-controls="mobile-menu" aria-expanded="true">
			<span class="material-icons" aria-hidden="true">
				close
			</span>
		</button>
	</nav>
