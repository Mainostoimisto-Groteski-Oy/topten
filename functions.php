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
if ( ! defined( 'TOPTEN_VERSION' ) ) {
	define( 'TOPTEN_VERSION', '1.0.0' );
}

/**
 * General functions
 */
require get_template_directory() . '/includes/functions/general.php';

/**
 * Backend functions
 */
require get_template_directory() . '/includes/functions/backend.php';

/**
 * Block functions
 */
require get_template_directory() . '/includes/functions/block.php';

/**
 * Init backend
 */
topten_backend_init();

/**
 * Theme setup
 */
function topten_setup() {
	/**
	 * Enable essential features.
	 */
	load_theme_textdomain( 'topten', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			// 'comment-list',
			// 'comment-form',
			'search-form',
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
			'primary-menu' => esc_html__( 'Päävalikko', 'topten' ),
		)
	);
}

/**
 * ACF Options to admin menu
 */
if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page(
		array(
			'page_title' => 'Footer',
			'menu_title' => 'Footer',
			'menu_slug'  => 'footer-settings',
			'redirect'   => false,
			'capability' => 'administrator',
		)
	);
}

add_action( 'after_setup_theme', 'topten_setup' );

/**
 * Dequeue useless stuff
 */
function topten_remove_scripts() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );

	// REMOVE WOOCOMMERCE BLOCK CSS
	// wp_dequeue_style( 'wc-block-style' );
}

add_action( 'wp_enqueue_scripts', 'topten_remove_scripts', 100 );

remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_footer', 'wp_enqueue_global_styles' );

/**
 * Exclude content form All-in-one Migration
 */

// wp-content
add_filter(
	'ai1wm_exclude_content_from_export',
	function ( $exclude_filters ) {
		$exclude_filters[] = 'updraft';

		return $exclude_filters;
	}
);

// wp-content/themes
add_filter(
	'ai1wm_exclude_themes_from_export',
	function ( $exclude_filters ) {
		$exclude_filters[] = get_stylesheet() . '/node_modules';
		$exclude_filters[] = get_stylesheet() . '/vendor';
		$exclude_filters[] = 'twentytwenty';
		$exclude_filters[] = 'twentytwentyone';
		$exclude_filters[] = 'twentytwentytwo';
		$exclude_filters[] = 'twentytwentythree';
		$exclude_filters[] = 'twentytwentythree';

		return $exclude_filters;
	}
);

// wp-content/uploads
add_filter(
	'ai1wm_exclude_media_from_export',
	function ( $exclude_filters ) {
		$exclude_filters[] = 'backup';

		return $exclude_filters;
	}
);

/**
 * Disable logging login attempts
 */
add_filter(
	'simple_history/simple_logger/log_message_key',
	function( $do_log, $logger_slug, $message_key ) {
		if ( 'SimpleUserLogger' === $logger_slug && 'user_unknown_login_failed' === $message_key ) {
			$do_log = false;
		}
		if ( 'SimpleUserLogger' === $logger_slug && 'user_login_failed' === $message_key ) {
			$do_log = false;
		}
		return $do_log;
	},
	10,
	5
);

/**
 * Enqueue scripts and styles.
 */
function topten_scripts() {

	wp_enqueue_style( 'roboto', get_template_directory_uri() . '/fonts/roboto/roboto.css', array(), TOPTEN_VERSION );

	wp_enqueue_style( 'animate', '//cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', array(), '4.1.1' ); // TODO: Tarvitaanko tätä?

	wp_enqueue_style( 'topten', get_template_directory_uri() . '/css/dist/site.min.css', array(), TOPTEN_VERSION );

	wp_enqueue_style( 'material-icons', '//fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Sharp|Material+Icons+Round|Material+Icons+Outlined&display=swap', array(), TOPTEN_VERSION );

	wp_enqueue_script( 'jquery' );
	
	wp_enqueue_script( 'jquery-ui', 'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', array(), TOPTEN_VERSION );

	wp_enqueue_script( 'topten', get_template_directory_uri() . '/js/dist/main.min.js', array( 'jquery' ), TOPTEN_VERSION, true );

	wp_localize_script(
		'topten',
		'Ajax',
		array(
			'url'   => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'nonce' ),
		)
	);

	$scripts = array(
		'topten_card_search',
		'topten_fetch_suggestions',
		'topten_fetch_terms',
	);

	foreach ( $scripts as $script ) {
		wp_localize_script( 'topten', $script, array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'nonce' )) );
	}
	
	wp_localize_script(
		'topten',
		'REST',
		array(
			'url'   => get_rest_url( null, '/topten/v1' ),
			'nonce' => wp_create_nonce( 'wp_rest' ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'topten_scripts' );

/**
 * Enqueue editor scripts and styles
 */
function topten_editor_scripts() {

	wp_enqueue_style( 'roboto', get_template_directory_uri() . '/fonts/roboto/roboto.css', array(), TOPTEN_VERSION );

	wp_enqueue_style( 'material-icons', '//fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Sharp|Material+Icons+Round|Material+Icons+Outlined&display=swap', array(), TOPTEN_VERSION );

	wp_enqueue_script( 'groteski-script', get_template_directory_uri() . '/js/dist/main.min.js', array( 'jquery' ), TOPTEN_VERSION, true );

	wp_enqueue_style( 'groteski-editor', get_template_directory_uri() . '/css/dist/gutenberg.min.css', array(), TOPTEN_VERSION );
}

add_action( 'enqueue_block_editor_assets', 'topten_editor_scripts' );

/**
 * Globaali muuttuja korttien sallittuihin lohkoihin
 */
$card_allowed_blocks = array(
	'acf/otsikko',
	'acf/kuva',
	'acf/teksti',
	'acf/rivi',
	'acf/taulukko',
);

/**
 * Allowed block types
 *
 * @param bool|string[]           $allowed_blocks Array of block type slugs, or boolean to enable/disable all.
 * @param WP_Block_Editor_Context $editor_context The current block editor context
 */
function topten_allowed_block_types( $allowed_blocks, $editor_context ) {
	// Tässä pitää käyttää blockin nimeä slugin sijasta (koska ???)
	$card_types = array(
		'tulkintakortti',
		'ohjekortti',
		'lomakekortti',
	);

	if ( 'page' === $editor_context->post->post_type ) {
		$allowed_blocks = array(
			'acf/hero',
			'acf/teksti',
			'acf/teksti-ja-kuva',
			'acf/teksti-ja-kortti',
			'acf/nosto',
			'acf/artikkelit',
			'acf/banneri',
			// 'acf/kaksi-saraketta',
			// 'acf/kolme-saraketta',
			// 'acf/upotus',
			// 'acf/logot',
			// 'acf/yhteystiedot',
			// 'acf/sivupalkki-ja-sisalto',
			// 'acf/painikkeet',
		);

		return $allowed_blocks;
	} elseif ( in_array( $editor_context->post->post_type, $card_types, true ) ) {
		global $card_allowed_blocks;

		$allowed_blocks = $card_allowed_blocks;

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

add_filter( 'allowed_block_types_all', 'topten_allowed_block_types', 9, 2 );

/**
 * ACF Blocks
 */
function topten_acf() {
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

		$block_name  = 'Tekstilohko';
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

		$block_name  = 'Teksti ja kortti';
		$block_slug  = 'text-and-card-block';
		$description = 'Lohko tekstipaikalla ja korttinostolla';

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
		$description = 'Lohko isolle nostolle';

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

		$block_name  = 'Banneri';
		$block_slug  = 'banner-block';
		$description = 'Lohko täysleveälle tekstibannerille';

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
					'align' => false,
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

		/**
		 * Korttien lohko
		 */
		$block_name  = 'Rivi';
		$block_slug  = 'card-row';
		$description = 'Rivi';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/card-blocks/$block_slug.php",
				'mode'            => 'preview',
				'supports'        => array(
					'align'  => false,
					'anchor' => true,
					'mode'   => false,
					'jsx'    => true,
				),
				'keywords'        => array( $block_name ),
			)
		);

		$block_name  = 'Sarake';
		$block_slug  = 'card-column';
		$description = 'Sarake';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/card-blocks/$block_slug.php",
				'mode'            => 'preview',
				'supports'        => array(
					'align'  => false,
					'anchor' => true,
					'mode'   => false,
					'jsx'    => true,
				),
				'keywords'        => array( $block_name ),
			)
		);

		$block_name  = 'Otsikko';
		$block_slug  = 'card-title';
		$description = 'Otsikko';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/card-blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
			)
		);

		$block_name  = 'Teksti';
		$block_slug  = 'card-text';
		$description = 'Teksti';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/card-blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
			)
		);

		$block_name  = 'Kuva';
		$block_slug  = 'card-image';
		$description = 'Kuva';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/card-blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
			)
		);

		$block_name  = 'Taulukko';
		$block_slug  = 'card-table';
		$description = 'Taulukko';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/card-blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
			)
		);
	}
}

add_action( 'acf/init', 'topten_acf' );

/**
 * Lisätään CPT artikkelilohkon post_type kenttään
 * https://www.advancedcustomfields.com/resources/dynamically-populate-a-select-fields-choices/
 *
 * @param array $field ACFn kenttä
 */
function topten_acf_cpt( $field ) {
	// Nollataan valinnat ja lisätään WPn oletus post type
	$field['choices'] = array(
		'post' => 'Artikkeli',
	);

	// Haetaan CPTt
	$post_types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		)
	);

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

add_filter( 'acf/load_field/name=post_type', 'topten_acf_cpt' );

/**
 * WP login sivun logo
 */
function topten_login_logo() {
	$logo_src = get_stylesheet_directory_uri() . '/assets/dist/images/groteski-logo.png.webp';
	?>
	<style type="text/css">
		#login h1 a,
		.login h1 a {
			background-image: url("<?php echo esc_url( $logo_src ); ?>");
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
	<?php
}

add_action( 'login_enqueue_scripts', 'topten_login_logo' );

/**
 * WP login sivun logo linkki
 */
function topten_login_logo_url() {
	return 'https://groteski.fi/';
}

add_filter( 'login_headerurl', 'topten_login_logo_url' );

/**
 * WP login sivun logo linkin teksti
 */
function topten_login_logo_url_title() {
	return 'Mainostoimisto Groteski Oy';
}

add_filter( 'login_headertext', 'topten_login_logo_url_title' );

/**
 * Card search
 */
function topten_card_search() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'nonce' ) ) {
		wp_send_json_error( 'Nonce value cannot be verified.' );

		wp_die();
	}

	$args = array(
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => 'card_status',
				'value'   => 'publish',
				'compare' => '=',
			),
			array(
				'key'     => 'card_status_publish',
				'value'   => 'valid',
				'compare' => '=',
			),
		),
	);

	// Card types
	$post_types = array();

	// Tulkintakortti
	if ( isset( $_POST['cardTulkinta'] ) && sanitize_text_field( $_POST['cardTulkinta'] ) ) {
		$post_types[] = 'tulkintakortti';
	}

	// Ohjekortti
	if ( isset( $_POST['cardOhje'] ) && sanitize_text_field( $_POST['cardOhje'] ) ) {
		$post_types[] = 'ohjekortti';
	}

	// Lomakekortti
	if ( isset( $_POST['cardLomake'] ) && sanitize_text_field( $_POST['cardLomake'] ) ) {
		$post_types[] = 'lomakekortti';
	}

	$args['post_type'] = $post_types;

	// Search text input
	$s = isset( $_POST['freeText'] ) ? sanitize_text_field( $_POST['freeText'] ) : '';

	if ( $s ) {
		$args['s'] = $s;
	}

	// Municipality (multiple values)
	if ( isset( $_POST['cardMunicipality'] ) ) {
		
		// sanitize array integer values
		$municipality = array_map( 'intval', $_POST['cardMunicipality'] );
		
		if ( ! $municipality ) {
			$municipality = '';
		}
	}

	if ( $municipality ) {

		$args['tax_query'] = array(
			array(
				'taxonomy' => 'kunta',
				'field'    => 'term_id',
				'terms'    => $municipality,
			),
		);
	}

	// Law article (single value)
	if( isset( $_POST['cardLaw'] ) ) {
		$law = intval( $_POST['cardLaw'] );
		if ( !$law ) {
			$law = '';
		}
	}

	if ( $law ) {
		
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'laki',
				'field'    => 'term_id',
				'terms'    => $law,
			),
		);
		
	}

	// Category (single value)
	if( isset( $_POST['cardCategory'] ) ) {
		$category = intval( $_POST['cardCategory'] );
		if ( !$category ) {
			$category = '';
		}
	}

	if ( $category ) {

		$args['tax_query'] = array(
			array(
				'taxonomy' => 'kortin_kategoria',
				'field'    => 'term_id',
				'terms'    => $category,
			),
		);
	}

	// Keywords (multiple), TODO: jatka tästä
	if ( isset( $_POST['cardKeywords'] ) && is_array( $_POST['cardKeywords'])) {
		// Sanitize array
		$keywords = array_map( 'intval', $_POST['cardKeywords'] );
		
		if( !$keywords ) {
			$keywords = '';
		}
		
	}
	

	if ( $keywords ) {
		
		$args['tax_query'] = array(
			array(
				'taxonomy' 		=> 'asiasanat',
				'field'			=> 'term_id',
				'terms'			=> $keywords,
			),
		);
	} */

	// Card publish date. User can filter by either starting from, ending at or both.
	$cardDateStart = isset( $_POST['cardDateStart'] ) ? sanitize_text_field( $_POST['cardDateStart'] ) : '';
	$cardDateEnd = isset( $_POST['cardDateEnd'] ) ? sanitize_text_field( $_POST['cardDateEnd'] ) : '';
	
	
	if ($cardDateStart && !$cardDateEnd) {
		
		$args['date_query'] = array(
			array(
				'after' => $cardDateStart,
				
			),
		);
	
	} else if (!$cardDateStart && $cardDateEnd) {
		
		$args['date_query'] = array(
			array(
				'before' => $cardDateEnd,
				
			),
		);
	
	} else if ($cardDateStart && $cardDateEnd) {
		
		$args['date_query'] = array(
			array(
				'after' => $cardDateStart,
				'before' => $cardDateEnd,
				
			),
		);

	} 

	// Display order of cards
	$filterOrder = isset( $_POST['filterOrder'] ) ? sanitize_text_field( $_POST['filterOrder'] ) : '';

	if ( $filterOrder ) {
		// Identifier, notice that this is not a WordPress ID
		if ( $filterOrder === 'identifier') {
			$args['orderby'] = 'meta_value';
			$args['meta_key'] = 'identifier_start';
			$args['order'] = 'ASC';
		// Publish date, descending order // TODO: Should this be modified time instead?
		} else if ( $filterOrder === 'publishDate') {
			$args['orderby'] = 'date';
			$args['order'] = 'DESC';
		// card name, alphabetical order
		} else if ( $filterOrder === 'title') {
			$args['orderby'] = 'title';
			$args['order'] = 'ASC';
		}

	}
	

	$the_query = new WP_Query();
	json_log($args);
	$the_query->parse_query( $args );

		// If Relevanssi is installed, use it
	if ( function_exists( 'relevanssi_do_query' ) ) {
		relevanssi_do_query( $the_query );
	} else {
		$the_query->query( $args );
	}
	
	
	// If posts are found, save them to their own arrays from which one array is created

	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$post_id = get_the_ID();
			if('tulkintakortti' === get_post_type($post_id) ) {
				$tulkinta_array[] = $post_id;
			} else if ('ohjekortti' === get_post_type($post_id) ) {
				$ohje_array[] = $post_id;
			} else if( 'lomakekortti' === get_post_type($post_id) ) {
				$lomake_array[] = $post_id;
			} else {
				// Nothing here.
			}
			
		}
	}
	// If nothing is found, return notice to user
	if(empty($tulkinta_array) && empty($ohje_array) && empty($lomake_array)) {
		$results = '<div class="noResults">';
		$results .= '<p>'.esc_html('Ei hakutuloksia','topten').'</p>';
		$results .= '</div>';
	} else {
		// Create array of arrays
		$card_array = array('tulkinta' => $tulkinta_array, 'ohje' => $ohje_array, 'lomake' => $lomake_array);
		// Run function to get the results
		$results = topten_card_list($card_array);
	}
	// Send back as json
	wp_send_json_success($results);
	
	// You need to use wp_die for ajax calls
	wp_die();
}

add_action( 'wp_ajax_topten_card_search', 'topten_card_search' );
add_action( 'wp_ajax_nopriv_topten_card_search', 'topten_card_search' );

function topten_fetch_suggestions() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'nonce' ) ) {
		wp_send_json_error( 'Nonce value cannot be verified.' );
		wp_die();
	}
	// This does nothing yet
	if ( ! isset($_POST['type'] ) ) {
		wp_send_json_error( 'Type is not set.' );
		wp_die();
	} else {
		$type = sanitize_text_field( $_POST['type'] );
	}

	if ( isset($_POST['userInput'] ) ) {
		$userInput = sanitize_text_field( $_POST['userInput'] ); 
	}

	if ($type === 'keywords') {
		//$terms = get_terms('asiasanat');
	}

	$args = array(
		'taxonomy' 		=> array('asiasanat'),
		'orderby'		=> 'title',
		'order'			=> 'DESC',
		'name__like'	=> $userInput,
	);

	$terms = get_terms($args);
	

	if($terms) {
		$list = [];
		foreach ($terms as $index => $term) {
			$list[$index]['label'] = $term->name;
			$list[$index]['value'] = $term->term_id;
		}

	} else {
		$list = [];
	}
	wp_send_json_success($list);
	wp_die();
}

add_action( 'wp_ajax_topten_fetch_suggestions', 'topten_fetch_suggestions' );
add_action( 'wp_ajax_nopriv_topten_fetch_suggestions', 'topten_fetch_suggestions' );

function topten_fetch_terms() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'nonce' ) ) {
		wp_send_json_error( 'Nonce value cannot be verified.' );
		wp_die();
	}
	// This does nothing yet
	if ( ! isset($_POST['type'] ) ) {
		wp_send_json_error( 'Type is not set.' );
		wp_die();
	} else {
		$type = sanitize_text_field( $_POST['type'] );
	}

	if ( isset($_POST['keywords'] ) ) {

		// Sanitize array of keyword IDs
		$keywords = array_map( 'intval', $_POST['keywords'] );
		if ( ! $keywords ) {
			$keywords = '';
			wp_die();
		}
	}
	
	$args = array(
		'taxonomy' 		=> array('asiasanat'),
		'orderby'		=> 'title',
		'order'			=> 'DESC',
		'include'		=> $keywords,
	);
	$terms = get_terms($args);

	if($terms) {
		$list = [];
		foreach ($terms as $index => $term) {
			$list[$index]['label'] = $term->name;
			$list[$index]['value'] = $term->term_id;
		}

	} else {
		$list = [];
	}
	wp_send_json_success($list);
	
	wp_die();
}

add_action( 'wp_ajax_topten_fetch_terms', 'topten_fetch_terms' );
add_action( 'wp_ajax_nopriv_topten_fetch_terms', 'topten_fetch_terms' );