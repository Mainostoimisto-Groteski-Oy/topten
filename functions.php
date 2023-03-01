<?php

/**
 * Groteski functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Groteski
 */

require get_template_directory() . '/theme-version.php';

// Version fallback
if ( ! defined( 'GROTESKI_VERSION' ) ) {
	define( 'GROTESKI_VERSION', '1.0.0' );
}

/**
 * Groteski functions
 */
require get_template_directory() . '/includes/groteski-functions.php';

if ( ! function_exists( 'groteski_setup' ) ) :
	function groteski_setup() {
		/**
		 * Enable essential features.
		 */
		load_theme_textdomain( 'groteski', get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		add_image_size( 'fullhd', 1920 );
		add_image_size( 'qhd', 2560 );

		register_nav_menus(
			array(
				'primary-menu' => esc_html__( 'Päävalikko', 'groteski' ),
			)
		);
	}
endif;

/**
 * ACF Options to admin menu
 */
if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page(array(
		'page_title' => 'Footer',
		'menu_title' => 'Footer',
		'menu_slug'  => 'footer-settings',
		'redirect'   => false,
	));
}

add_action( 'after_setup_theme', 'groteski_setup' );

/**
 * Dequeue useless stuff
 */
function groteski_remove_scripts() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );

	// REMOVE WOOCOMMERCE BLOCK CSS
	// wp_dequeue_style( 'wc-block-style' );
}

add_action( 'wp_enqueue_scripts', 'groteski_remove_scripts', 100 );

remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_footer', 'wp_enqueue_global_styles' );

/**
 * Exclude content form All-in-one Migration
 */

// wp-content
add_filter( 'ai1wm_exclude_content_from_export', function ( $exclude_filters ) {
	$exclude_filters[] = 'updraft';

	return $exclude_filters;
} );

// wp-content/themes
add_filter( 'ai1wm_exclude_themes_from_export', function ( $exclude_filters ) {
	$exclude_filters[] = get_stylesheet() . '/node_modules';
	$exclude_filters[] = get_stylesheet() . '/vendor';
	$exclude_filters[] = 'twentytwenty';
	$exclude_filters[] = 'twentytwentyone';
	$exclude_filters[] = 'twentytwentytwo';
	$exclude_filters[] = 'twentytwentythree';
	$exclude_filters[] = 'twentytwentythree';

	return $exclude_filters;
});

// wp-content/uploads
add_filter( 'ai1wm_exclude_media_from_export', function ( $exclude_filters ) {
	$exclude_filters[] = 'backup';

	return $exclude_filters;
} );

/**
 * Disable logging login attempts
 */
add_filter('simple_history/simple_logger/log_message_key', function( $do_log, $logger_slug, $message_key ) {
	if ( 'SimpleUserLogger' === $logger_slug && 'user_unknown_login_failed' === $message_key ) {
		$do_log = false;
	}
	if ( 'SimpleUserLogger' === $logger_slug && 'user_login_failed' === $message_key ) {
		$do_log = false;
	}
	return $do_log;
}, 10, 5);

// Lisätään CPT artikkelilohkon post_type kenttään
// https://www.advancedcustomfields.com/resources/dynamically-populate-a-select-fields-choices/
function groteski_acf_cpt( $field ) {
	// Nollataan valinnat ja lisätään WPn oletus post type
	$field['choices'] = array(
		'post' => 'Artikkeli',
	);

	// Haetaan CPTt
	$post_types = get_post_types( array(
		'public'   => true,
		'_builtin' => false,
	) );

	if ( $post_types ) {
		foreach ( $post_types as $post_type ) {
			// Haetaan post type object siistimpää nimeä varten
			$post_type_object = get_post_type_object( $post_type );

			// array key = palautettava arvo ($post_type, esim 'post')
			// array value = ACFn näyttävä arvo (singular_name, esim 'Artikkeli')
			// eli sama asia kuin 'post : Artikkeli'
			$field['choices'][ $post_type ] = $post_type_object->labels->singular_name;
		}
	}

	return $field;
}

add_filter( 'acf/load_field/name=post_type', 'groteski_acf_cpt' );

/**
 * Enqueue scripts and styles.
 */
function groteski_scripts() {
	// wp_enqueue_style( 'font', '//fonts.googleapis.com/css2?family=Oswald&display=swap', array(), GROTESKI_VERSION );

	wp_enqueue_style( 'animate', '//cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', array(), '4.1.1' );

	wp_enqueue_style( 'groteski', get_template_directory_uri() . '/css/dist/site.min.css', array(), GROTESKI_VERSION );

	wp_enqueue_style( 'material-icons', '//fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Sharp|Material+Icons+Round|Material+Icons+Outlined&display=swap', array(), GROTESKI_VERSION );

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script( 'groteski', get_template_directory_uri() . '/js/dist/main.min.js', array( 'jquery' ), GROTESKI_VERSION, true );

	wp_localize_script( 'groteski', 'Ajax', array(
		'url'   => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'nonce' ),
	) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'groteski_scripts' );

/**
 * Enqueue editor scripts and styles
 */
function groteski_editor_scripts() {
	// wp_enqueue_style( 'font', '//fonts.googleapis.com/css2?family=Oswald&display=swap', array(), GROTESKI_VERSION );

	wp_enqueue_style( 'material-icons', '//fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Sharp|Material+Icons+Round|Material+Icons+Outlined&display=swap', array(), GROTESKI_VERSION );

	wp_enqueue_script( 'groteski-script', get_template_directory_uri() . '/js/dist/main.min.js', array( 'jquery' ), GROTESKI_VERSION, true );

	wp_enqueue_style( 'groteski-editor', get_template_directory_uri() . '/css/dist/gutenberg.min.css', array(), GROTESKI_VERSION );
}

add_action( 'enqueue_block_editor_assets', 'groteski_editor_scripts' );

/**
 * Allowed block types
 */
function groteski_allowed_block_types( $allowed_blocks, $editor_context ) {
	// Tässä pitää käyttää blockin nimeä slugin sijasta (koska ???)

	if ( 'page' === $editor_context->post->post_type ) {
		$allowed_blocks = array(
			'acf/hero',
			'acf/teksti',
			// 'acf/kaksi-saraketta',
			// 'acf/kolme-saraketta',
			// 'acf/teksti-ja-kuva',
			// 'acf/artikkelit',
			// 'acf/upotus',
			// 'acf/nosto',
			// 'acf/logot',
			// 'acf/yhteystiedot',
			// 'acf/sivupalkki-ja-sisalto',
			// 'acf/painikkeet',
		);

		return $allowed_blocks;
	} else {
		$allowed_blocks = array();

		$all_block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();

		foreach ( $all_block_types as $block_type ) {
			if ( ! str_contains( $block_type->name, 'acf' ) ) {
				$allowed_blocks[] = $block_type->name;
			}
		}

		return $allowed_blocks;
	}
}

	add_filter( 'allowed_block_types_all', 'groteski_allowed_block_types', 9, 2 );

	/**
	 * ACF Blocks
	 */
function groteski_acf() {
	if ( function_exists( 'acf_register_block' ) ) {

		$block_name  = 'Hero';
		$block_slug  = 'hero-block';
		$description = 'Heroalueen lohko kuvalla, videolla tai kuvakarusellilla';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'icon'            => 'cover-image',
			)
		);

		$block_name  = 'Teksti';
		$block_slug  = 'text-block';
		$description = 'Lohko tekstipaikalla';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'icon'            => 'text',
			)
		);

		$block_name  = 'Kaksi saraketta';
		$block_slug  = 'two-column-block';
		$description = 'Lohko kahdella sarakkeella';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'icon'            => 'columns',
			)
		);

		$block_name  = 'Kolme saraketta';
		$block_slug  = 'three-column-block';
		$description = 'Lohko kolmella sarakkeella';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'icon'            => 'layout',
			)
		);

		$block_name  = 'Teksti ja kuva';
		$block_slug  = 'text-and-image-block';
		$description = 'Lohko tekstipaikalla ja kuvalla';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'icon'            => 'format-image',
			)
		);

		$block_name  = 'Artikkelit';
		$block_slug  = 'articles-block';
		$description = 'Lohko artikkeleille';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'icon'            => 'admin-post',
			)
		);

		$block_name  = 'Upotus';
		$block_slug  = 'embed-block';
		$description = 'Lohko täysleveälle upotukselle';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'icon'            => 'embed-generic',
			)
		);

		$block_name  = 'Nosto';
		$block_slug  = 'lift-block';
		$description = 'Lohko täysleveälle nostolle';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'icon'            => 'align-wide',
			)
		);

		$block_name  = 'Logot';
		$block_slug  = 'logos-block';
		$description = 'Lohko tekstille ja usealle logolle';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'icon'            => 'images-alt',
			)
		);

		$block_name  = 'Yhteystiedot';
		$block_slug  = 'contacts-block';
		$description = 'Lohko henkilöiden yhteystiedoille';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'icon'            => 'phone',
			)
		);

		$block_name  = 'Sivupalkki ja sisältö';
		$block_slug  = 'sidebar-and-content-block';
		$description = 'Lohko sivupalkilla, sekä sisältöalueella, jossa käytössä muut teeman lohkot.';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'mode'            => 'preview',
				'supports'        => array(
					'align' => true,
					'mode'  => false,
					'jsx'   => true,
				),
				'icon'            => 'welcome-widgets-menus',
			)
		);

		$block_name  = 'Painikkeet';
		$block_slug  = 'buttons-block';
		$description = 'Lohko painikkeille (esim. ankkurilinkeiksi)';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'icon'            => 'button',
			)
		);
	}
}

	add_action( 'acf/init', 'groteski_acf' );

//Login screen ulkoasua
function groteski_login_logo() { ?>
    <style type="text/css">
        #login h1 a,
		.login h1 a {
            background-image: url("<?php echo get_stylesheet_directory_uri(); ?>/assets/dist/groteski-logo.png.webp");
			width: 100%;
			height: 200px;
			background-size: 320px 65px;
			background-repeat: no-repeat;
        	padding-bottom: 30px;
			background-size: contain;
        }

		body.login {
			background: linear-gradient(180deg,#0f1127,#010007 99.99%,rgba(24,26,41,0));
		}

		body.login #backtoblog a, body.login #nav a {
			color: white;
		}

		body.login #wp-submit {
			background-color: #de2115;
			border: none;
			border-radius: 34px;
		}

		body.login form {
			background-color: #ebeefe;
		}
    </style>
<?php }

add_action( 'login_enqueue_scripts', 'groteski_login_logo' );

function groteski_login_logo_url() {
    return 'https://groteski.fi/';
}
add_filter( 'login_headerurl', 'groteski_login_logo_url' );

function groteski_login_logo_url_title() {
    return 'Mainostoimisto Groteski Oy';
}

add_filter( 'login_headertext', 'groteski_login_logo_url_title' );

// Analytics (uncomment add action and change G-XXX code)
function groteski_analytics() { ?>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxxxx"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'G-xxxxxxxxxx');
	</script>
	<?php
}

// add_action( 'wp_head','groteski_analytics', 20 );

if ( $asd === 'test' ) {