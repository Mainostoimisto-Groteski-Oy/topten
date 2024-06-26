<?php

/**
 * Groteski functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Topten
 */

require get_template_directory() . '/theme-version.php';

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
 * Topten theme setup
 *
 * @since 1.0.0
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
			'page_title' => 'Sivuston asetukset',
			'menu_title' => 'Sivusto',
			'menu_slug'  => 'general-settings',
			'redirect'   => false,
			'capability' => 'administrator',
			'icon_url'   => 'dashicons-admin-settings',
		)
	);

	acf_add_options_page(
		array(
			'page_title' => 'Footerin asetukset',
			'menu_title' => 'Footer',
			'menu_slug'  => 'footer-settings',
			'redirect'   => false,
			'capability' => 'administrator',
			'icon_url'   => 'dashicons-index-card',
		)
	);

	acf_add_options_page(
		array(
			'page_title' => 'Heräteviestit',
			'menu_title' => 'Heräteviestit',
			'menu_slug'  => 'subscriber-settings',
			'redirect'   => false,
			'capability' => 'administrator',
			'icon_url'   => 'dashicons-email',
		)
	);
}

add_action( 'after_setup_theme', 'topten_setup' );

/**
 * Dequeues WP's default scripts and styles
 *
 * @since 1.0.0
 */
function topten_remove_scripts() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
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
 * Admin side scripts and styles
 *
 * @since 1.0.0
 */
function topten_admin_scripts() {
	wp_enqueue_script( 'topten-admin', get_template_directory_uri() . '/js/dist/admin.min.js', array( 'wp-i18n' ), TOPTEN_VERSION, true );

	wp_localize_script(
		'topten-admin',
		'Ajax',
		array(
			'url'   => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'nonce' ),
		)
	);

	wp_set_script_translations( 'topten-admin', 'topten', get_template_directory() . '/languages' );

	wp_enqueue_style( 'topten-admin', get_template_directory_uri() . '/css/dist/admin.min.css', array(), TOPTEN_VERSION );

	// Datatables
	wp_enqueue_script( 'datatables', '//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js', array(), '1.13.4', true );
	wp_enqueue_style( 'datatables', '//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css', array(), '1.13.4' );
}

add_action( 'admin_enqueue_scripts', 'topten_admin_scripts' );

/**
 * Enqueue scripts and styles.
 *
 * @since 1.0.0
 */
function topten_scripts() {
	wp_enqueue_style( 'topten', get_template_directory_uri() . '/css/dist/site.min.css', array(), TOPTEN_VERSION );

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script( 'jquery-ui', get_template_directory_uri() . '/scripts/jquery-ui/jquery-ui.min.js', array( 'jquery' ), '1.13.2', true );

	wp_enqueue_script( 'topten', get_template_directory_uri() . '/js/dist/main.min.js', array( 'jquery' ), TOPTEN_VERSION, true );

	wp_localize_script(
		'topten',
		'Ajax',
		array(
			'url'   => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'nonce' ),
		)
	);

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
 *
 * @since 1.0.0
 */
function topten_editor_scripts() {
	wp_enqueue_style( 'blinker', get_template_directory_uri() . '/fonts/blinker/blinker.css', array(), TOPTEN_VERSION );

	wp_enqueue_style( 'roboto', get_template_directory_uri() . '/fonts/roboto/roboto.css', array(), TOPTEN_VERSION );

	// Google Material Symbols font
	wp_enqueue_style( 'material-symbols', get_template_directory_uri() . '/fonts/MaterialSymbols/MaterialSymbols.css', array(), TOPTEN_VERSION );

	wp_enqueue_script( 'groteski-script', get_template_directory_uri() . '/js/dist/main.min.js', array( 'jquery' ), TOPTEN_VERSION, true );

	wp_enqueue_style( 'groteski-editor', get_template_directory_uri() . '/css/dist/gutenberg.min.css', array(), TOPTEN_VERSION );
}

add_action( 'enqueue_block_editor_assets', 'topten_editor_scripts' );

/**
 * Global variable for allowed blocks in cards
 */
$card_allowed_blocks = array(
	'acf/otsikko',
	'acf/kuva',
	'acf/teksti',
	'acf/rivi',
	'acf/taulukko',
	'acf/linkkilista',
	'acf/liitteet',
	'acf/tekstikentta',
	'acf/tekstialue',
	'acf/valintaruudut',
	'acf/valintanapit',
);

/**
 * Allowed block types
 *
 * @param bool|string[]           $allowed_blocks Array of block type slugs, or boolean to enable/disable all.
 * @param WP_Block_Editor_Context $editor_context The current block editor context
 */
function topten_allowed_block_types( $allowed_blocks, $editor_context ) {
	// You need to use the block name instead of the slug (because ???)
	$card_types = array(
		'tulkintakortti',
		'ohjekortti',
		'lomakekortti',
	);

	if ( 'page' === $editor_context->post->post_type ) {
		$allowed_blocks = array(
			'acf/hero',
			'acf/tekstilohko',
			'acf/teksti-ja-kuva',
			'acf/teksti-ja-kortti',
			'acf/teksti-ja-lomake',
			'acf/nosto',
			'acf/artikkelit',
			'acf/banneri',
			'acf/nostoryhma',
			'acf/logot',
			'acf/kaksi-saraketta',
			'acf/kolme-saraketta',
			'acf/nelja-saraketta',
			'acf/lista',
			'acf/haitari',
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
 * Register ACF Blocks
 *
 * @since 1.0.0
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

		$block_name  = 'Neljä saraketta';
		$block_slug  = 'four-column-block';
		$description = 'Lohko neljällä sarakkeella';

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

		$block_name  = 'Teksti ja lomake';
		$block_slug  = 'text-and-form-block';
		$description = 'Lohko tekstipaikalla ja lomakkeella';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'icon'            => 'feedback',
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

		$block_name  = 'Lista';
		$block_slug  = 'list-block';
		$description = 'Lohko listaelementille';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'icon'            => 'list-view',
			)
		);

		$block_name  = 'Nostoryhmä';
		$block_slug  = 'lift-group-block';
		$description = 'Lohko nostoryhmälle';

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
		$block_name  = 'Haitari';
		$block_slug  = 'accordion-block';
		$description = 'Lohko haitarielementillä';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
				'icon'            => 'menu',
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

		$block_name  = 'Linkkilista';
		$block_slug  = 'card-link-list';
		$description = 'Linkkilista';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/card-blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
			)
		);

		$block_name  = 'Liitteet';
		$block_slug  = 'card-attachments';
		$description = 'Liitetiedosto';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/card-blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
			)
		);

		$block_name  = 'Tekstikenttä';
		$block_slug  = 'card-input-text';
		$description = 'Tekstikenttä';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/card-blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
			)
		);

		$block_name  = 'Tekstialue';
		$block_slug  = 'card-input-textarea';
		$description = 'Tekstialue';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/card-blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
			)
		);

		$block_name  = 'Valintaruudut';
		$block_slug  = 'card-input-checkboxes';
		$description = 'Valintaruudut';

		acf_register_block_type(
			array(
				'name'            => $block_name,
				'title'           => $block_name,
				'description'     => $description,
				'render_template' => "blocks/card-blocks/$block_slug.php",
				'keywords'        => array( $block_name ),
			)
		);


		$block_name  = 'Valintanapit';
		$block_slug  = 'card-input-radios';
		$description = 'Valintanapit';

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
 * Add CPTs to post_type field in article block
 *
 * @see https://www.advancedcustomfields.com/resources/dynamically-populate-a-select-fields-choices/
 *
 * @param array $field ACFn kenttä
 */
function topten_acf_cpt( $field ) {
	// Empty choices, then add default choice
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
			// Get post type object for cleaning up the name
			$post_type_object = get_post_type_object( $post_type );

			// Array key = post type slug (e.g. 'post')
			// Array value = value that ACF will show (singular_name, e.g. 'Artikkeli')
			// post : Artikkeli
			$field['choices'][ $post_type ] = $post_type_object->labels->singular_name;
		}
	}

	return $field;
}

add_filter( 'acf/load_field/name=post_type', 'topten_acf_cpt' );

/**
 * Populate guide field with data from options page
 *
 * @since 1.0.0
 *
 * @param array $field ACF field
 */
function topten_acf_guide( $field ) {
	$field['choices'] = array(
		'none' => 'Ei tulkintaa',
	);

	if ( have_rows( 'guide', 'options' ) ) {
		while ( have_rows( 'guide', 'options' ) ) {

			the_row();

			$icon  = get_sub_field( 'icon' );
			$color = get_sub_field( 'color' );
			$name  = get_sub_field( 'name' );

			// array key = palautettava arvo ($post_type, esim 'post')
			// array value = ACFn näyttävä arvo (singular_name, esim 'Artikkeli')
			// eli sama asia kuin 'post : Artikkeli'
			$field['choices'][ $icon ] = $name;
		}
	}

	return $field;
}

add_filter( 'acf/load_field/name=tulkinta', 'topten_acf_guide' );

/**
 * Custom login logo
 *
 * @since 1.0.0
 */
function groteski_login_logo() {
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

add_action( 'login_enqueue_scripts', 'groteski_login_logo' );

/**
 * Custom login logo link
 *
 * @since 1.0.0
 */
function groteski_login_logo_url() {
	return 'https://groteski.fi/';
}

add_filter( 'login_headerurl', 'groteski_login_logo_url' );

/**
 * Custom login logo url title
 *
 * @since 1.0.0
 */
function groteski_login_logo_url_title() {
	return 'Mainostoimisto Groteski Oy';
}

add_filter( 'login_headertext', 'groteski_login_logo_url_title' );

/**
 * Groteski dashboard widgets
 *
 * @since 1.0.0
 */
function groteski_dashboard_widgets() {
	wp_add_dashboard_widget( 'groteski_help_widget', 'Tekninen tuki', 'groteski_help_widget', null, null, 'normal', 'high' );
}

add_action( 'wp_dashboard_setup', 'groteski_dashboard_widgets' );

/**
 * Groteski help widget
 *
 * @since 1.0.0
 */
function groteski_help_widget() {
	?>
	<div class="dashboard-grid">
		<div class="left">
			<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/dist/images/groketti.gif" alt="" />
		</div>

		<div class="right">
			<p>
				<strong>
					<?php esc_html_e( 'Ongelmia sivuston kanssa?', 'topten' ); ?>
				</strong>
				<br />

				<strong>
				<?php esc_html_e( 'Kaipaatko uusia ominaisuuksia?', 'topten' ); ?>
				</strong>
			</p>

			<p>
			<?php esc_html_e( 'Autamme mielellämme kehittämään sivustoasi entistäkin paremmaksi!', 'topten' ); ?>
			</p>

			<p>
			<?php esc_html_e( 'Ota yhteyttä Groteskin tukeen:', 'topten' ); ?>
			<a href="mailto:tuki@groteski.fi">tuki@groteski.fi</a>
			</p>
		</div>
	</div>

	<?php
}

/**
 * Card search
 *
 * @since 1.0.0
 */
function topten_card_search() {
	/*
	Is this even required for public facing things that have no security issues? Commented out because server has some issues with nonce verification
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'nonce' ) ) {
		wp_send_json_error( 'Nonce value cannot be verified.' );

		wp_die();
	}
	*/

	$args = array(
		'posts_per_page'         => -1,
		'post_status'            => 'publish',
		'fields'                 => 'ids',
		'ignore_custom_sort'     => true,
		'meta_query'             => array(
			'relation' => 'AND',
			array(
				'relation' => 'AND',
				array(
					'key'     => 'card_status',
					'value'   => 'publish',
					'compare' => '=',
				),
			),
		),
		'tax_query'              => array(),
		'update_post_term_cache' => true,
		'update_post_meta_cache' => true,
	);

	if ( isset( $_POST['cardStatusType'] ) ) {
		$card_status_type = sanitize_text_field( wp_unslash( $_POST['cardStatusType'] ) );
		if ( 'valid' !== $card_status_type && 'future' !== $card_status_type && 'past' !== $card_status_type ) {
			wp_die( 'Invalid card status type' );
		} else {
			if ( 'past' === $card_status_type ) {
				$args['meta_query'][] =
				array(
					'relation' => 'OR',
					array(
						'key'     => 'card_status_publish',
						'value'   => 'expired',
						'compare' => '=',
					),
					array(
						'key'     => 'card_status_publish',
						'value'   => 'repealed',
						'compare' => '=',
					),
				);

			} elseif ( 'valid' === $card_status_type ) {
				$args['meta_query'][] =
				array(
					'relation' => 'OR',
					array(
						'key'     => 'card_status_publish',
						'value'   => 'valid',
						'compare' => '=',
					),
					array(
						'key'     => 'card_status_publish',
						'value'   => 'approved_for_repeal',
						'compare' => '=',
					),
				);
			} elseif ( 'future' === $card_status_type ) {
				$args['meta_query'][] =
				array(
					'relation' => 'OR',
					array(
						'key'     => 'card_status_publish',
						'value'   => 'future',
						'compare' => '=',
					),
				);
			}
		}
	}



	// Card types
	$incoming_post_types = array();
	$post_types          = array();
	// sanitize array and set post types
	if ( isset( $_POST['cardTypes'] ) ) {
		if ( ! empty( $_POST['cardTypes'] ) ) {
			$incoming_post_types = array_map( 'sanitize_text_field', $_POST['cardTypes'] );
			foreach ( $incoming_post_types as $post_type ) {
				// This has some extra crap coming in from ajax so get rid of it
				$post_type    = explode( '|', $post_type );
				$post_types[] = $post_type[0];
			}
		}
		if ( ! $post_types ) {
			$post_types = '';
		}
	}

	$args['post_type'] = $post_types;

	// Search text input
	$s = isset( $_POST['freeText'] ) ? sanitize_text_field( $_POST['freeText'] ) : '';

	if ( $s ) {
		$args['s'] = $s;
	}

	if ( isset( $_POST['cardClasses'] ) ) {
		if ( ! empty( $_POST['cardClasses'] ) ) {
			// sanitize array values
			$card_classes = array_map( 'intval', $_POST['cardClasses'] );
		}
		if ( ! $card_classes ) {
			$card_classes = '';
		}
	}

	if ( ! empty( $card_classes ) && isset( $card_classes ) ) {

		$args['tax_query'][] =
			array(
				'taxonomy' => 'luokka',
				'field'    => 'term_id',
				'terms'    => $card_classes,
			);

	}

	/**
	 * Municipality (multiple values)
	 * Not in use due to customer request
	 */
	// if ( isset( $_POST['cardmunicipalities'] ) ) {
	// if ( ! empty( $_POST['cardmunicipalities'] ) ) {
	// sanitize array values
	// $municipality = array_map( 'intval', $_POST['cardmunicipalities'] );
	// }
	// if ( ! $municipality ) {
	// $municipality = '';
	// }
	// }

	// if ( $municipality ) {

	// $args['tax_query'][] =
	// array(
	// 'taxonomy' => 'kunta',
	// 'field'    => 'term_id',
	// 'terms'    => $municipality,
	// );
	// }


	// Law article (single value)
	$law = '';

	if ( isset( $_POST['cardLaw'] ) ) {
		$law = intval( $_POST['cardLaw'] );

		if ( ! $law ) {
			$law = '';
		}
	}

	if ( $law ) {
		$args['tax_query'][] =
			array(
				'taxonomy' => 'laki',
				'field'    => 'term_id',
				'terms'    => $law,
			);

	}

	// Category (single value)
	$category = '';

	if ( isset( $_POST['cardCategory'] ) ) {
		$category = intval( $_POST['cardCategory'] );

		if ( ! $category ) {
			$category = '';
		}
	}

	if ( $category ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'kortin_kategoria',
			'field'    => 'term_id',
			'terms'    => $category,
		);
	}

	// Keywords (multiple)
	$keywords = '';

	if ( isset( $_POST['cardkeywords'] ) && is_array( $_POST['cardkeywords'] ) ) {

		// Sanitize array
		$keywords = array_map( 'intval', $_POST['cardkeywords'] );

		if ( ! $keywords ) {
			$keywords = '';
		}
	}

	if ( isset( $keywords ) && ! empty( $keywords ) ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'asiasanat',
			'field'    => 'term_id',
			'terms'    => $keywords,
		);
	}

	// Card publish date. User can filter by either starting from, ending at or both.
	$cardDateStart = isset( $_POST['cardDateStart'] ) ? sanitize_text_field( $_POST['cardDateStart'] ) : '';
	$cardDateEnd   = isset( $_POST['cardDateEnd'] ) ? sanitize_text_field( $_POST['cardDateEnd'] ) : '';

	if ( $cardDateStart && ! $cardDateEnd ) {
		$args['date_query'] = array(
			array(
				'after' => $cardDateStart,

			),
		);
	} elseif ( ! $cardDateStart && $cardDateEnd ) {
		$args['date_query'] = array(
			array(
				'before' => $cardDateEnd,

			),
		);
	} elseif ( $cardDateStart && $cardDateEnd ) {
		$args['date_query'] = array(
			array(
				'after'  => $cardDateStart,
				'before' => $cardDateEnd,

			),
		);
	}

	// Display order of cards
	$filterOrder = isset( $_POST['filterOrder'] ) ? sanitize_text_field( $_POST['filterOrder'] ) : '';

	if ( $filterOrder ) {

		if ( $filterOrder === 'publishDate' ) {
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
			// card name, alphabetical order
		} elseif ( $filterOrder === 'title' ) {
			$args['orderby'] = 'title';
			$args['order']   = 'ASC';
		}
		// with card identifier (note: not WordPress [if you don't type WordPress like this it causes a build error :D] ID) we do custom array sorting, since meta_query with relevassi takes longer than the heat death of the universe
	}

	// $cache_key = $args;
	// $cache = get_transient( $cache_key );

	// if ( $cache ) {
	// return $cache;
	// }

	$the_query = new WP_Query();

	$the_query->parse_query( $args );

		// If Relevanssi is installed, use it
	if ( function_exists( 'relevanssi_do_query' ) ) {
		if ( ! empty( $s ) ) {
			relevanssi_do_query( $the_query );
		} else {
			$the_query->query( $args );
		}
	} else {
		$the_query->query( $args );
	}

	// Create arrays for different card types
	$tulkinta_array = array();
	$ohje_array     = array();
	$lomake_array   = array();

	// If posts are found, save them to their own arrays from which one array is created
	if ( 'identifier' === $filterOrder ) {
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$post_id = get_the_ID();
				// combine identifier from acf fields start and end
				$identifier = get_field( 'identifier_start' ) . ' ' . get_field( 'identifier_end' );
				if ( 'tulkintakortti' === get_post_type( $post_id ) ) {
					$tulkinta_array[] = array(
						'ID'         => $post_id,
						'identifier' => $identifier,
					);
				} elseif ( 'ohjekortti' === get_post_type( $post_id ) ) {
					$ohje_array[] = array(
						'ID'         => $post_id,
						'identifier' => $identifier,
					);
				} elseif ( 'lomakekortti' === get_post_type( $post_id ) ) {
					$lomake_array[] = array(
						'ID'         => $post_id,
						'identifier' => $identifier,
					);
				}
			}
		}
	} else {
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$post_id = get_the_ID();
				if ( 'tulkintakortti' === get_post_type( $post_id ) ) {
					$tulkinta_array[] = $post_id;
				} elseif ( 'ohjekortti' === get_post_type( $post_id ) ) {
					$ohje_array[] = $post_id;
				} elseif ( 'lomakekortti' === get_post_type( $post_id ) ) {
					$lomake_array[] = $post_id;
				}
			}
		}
	}

	// If nothing is found, return notice to user
	if ( empty( $tulkinta_array ) && empty( $ohje_array ) && empty( $lomake_array ) ) {
		$results  = '<div class="no-results">';
		$results .= '<p>' . esc_html__( 'Ei hakutuloksia.', 'topten' ) . '</p>';
		$results .= '</div>';

		// return results and die
		echo $results; // phpcs:ignore

		wp_die();
	} else {
		// Sort every array by identifier
		// only do this if we sort by identifier
		if ( 'identifier' === $filterOrder ) {
			usort(
				$tulkinta_array,
				function( $a, $b ) {
					return $a['identifier'] <=> $b['identifier'];
				}
			);
			usort(
				$ohje_array,
				function( $a, $b ) {
					return $a['identifier'] <=> $b['identifier'];
				}
			);
			usort(
				$lomake_array,
				function( $a, $b ) {
					return $a['identifier'] <=> $b['identifier'];
				}
			);
			// remove identifiers from arrays and leave only IDs
			$tulkinta_array = array_column( $tulkinta_array, 'ID' );
			$ohje_array     = array_column( $ohje_array, 'ID' );
			$lomake_array   = array_column( $lomake_array, 'ID' );
		}

		// Create array of arrays
		$card_array = array(
			'tulkinta' => $tulkinta_array,
			'ohje'     => $ohje_array,
			'lomake'   => $lomake_array,
		);

		// Run function to get the results
		topten_card_list( $card_array );
	}

	// $cache = set_transient( $cache_key, $result );

	// You need to use wp_die for ajax calls
	wp_die();
}

add_action( 'wp_ajax_topten_card_search', 'topten_card_search' );
add_action( 'wp_ajax_nopriv_topten_card_search', 'topten_card_search' );

/**
 * Return autocomplete suggestions
 *
 * @since 1.0.0
 */
function topten_fetch_suggestions() {
	/*
	Is this even required for public facing things that have no security issues? Commented out because server has some issues with nonce verification
	
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'nonce' ) ) {
		wp_send_json_error( 'Nonce value cannot be verified.' );
		wp_die();
	}

	*/

	// Get input

	/*
	if ( isset( $_POST['userInput'] ) ) {
		$userInput = sanitize_text_field( $_POST['userInput'] );
		if ( ! $type ) {
			wp_die();
		} else {

			if ( 'asiasanat' === $type ) {
				$tax = array( 'asiasanat' );
			} elseif ( 'kunta' === $type ) {
				$tax = array( 'kunta' );
			}
		}
	}
	*/

	$args = array(
		'taxonomy' => 'asiasanat',
		'orderby'  => 'title',
		'order'    => 'DESC',
		// 'name__like' => $userInput,
	);

	$terms = get_terms( $args );
	// sort alphabetically
	$terms = array_reverse( $terms );
	if ( $terms ) {
		$list = array();
		foreach ( $terms as $index => $term ) {
			$list[ $index ]['label'] = $term->name;
			$list[ $index ]['value'] = $term->term_id;
		}
	} else {
		$list = array();
	}

	wp_send_json_success( $list );

	wp_die();
}

add_action( 'wp_ajax_topten_fetch_suggestions', 'topten_fetch_suggestions' );
add_action( 'wp_ajax_nopriv_topten_fetch_suggestions', 'topten_fetch_suggestions' );

/**
 * Fetches terms for the search form
 *
 * @since 1.0.0
 */
function topten_fetch_terms() {
	/*
	Is this even required for public facing things that have no security issues? Commented out because server has some issues with nonce verification
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'nonce' ) ) {
		wp_send_json_error( 'Nonce value cannot be verified.' );
		wp_die();
	}
	*/


	// This does nothing yet
	if ( ! isset( $_POST['type'] ) ) {
		wp_send_json_error( 'Type is not set.' );
		wp_die();
	} else {
		$type = sanitize_text_field( $_POST['type'] );
		if ( ! $type ) {
			wp_die();
		} else {

			if ( 'asiasanat' === $type ) {
				$tax = array( 'asiasanat' );
			} elseif ( 'kunta' === $type ) {
				$tax = array( 'kunta' );
			}
		}
	}

	if ( isset( $_POST['keywords'] ) ) {
		// Sanitize array of keyword IDs
		$keywords = array_map( 'intval', $_POST['keywords'] );

		if ( ! $keywords ) {
			$keywords = '';
			wp_die();
		}
	}

	$args  = array(
		'taxonomy' => $tax,
		'orderby'  => 'title',
		'order'    => 'DESC',
		'include'  => $keywords,
	);
	$terms = get_terms( $args );

	if ( $terms ) {
		$list = array();
		foreach ( $terms as $index => $term ) {
			$list[ $index ]['label'] = $term->name;
			$list[ $index ]['value'] = $term->term_id;
		}
	} else {
		$list = array();
	}

	wp_send_json_success( $list );

	wp_die();
}

add_action( 'wp_ajax_topten_fetch_terms', 'topten_fetch_terms' );
add_action( 'wp_ajax_nopriv_topten_fetch_terms', 'topten_fetch_terms' );

/**
 * Customize the cutoff for the excerpt
 *
 * @since 1.0.0
 *
 * @param string $more The excerpt more string
 */
function topten_excerpt_more( $more ) {
	return '..';
}

add_filter( 'excerpt_more', 'topten_excerpt_more' );

/**
 * Customize the length of the excerpt
 *
 * @param int $length The length of the excerpt
 */
function topten_excerpt_length( $length ) {
	return 20;
}

add_filter( 'excerpt_length', 'topten_excerpt_length', 999 );

add_filter( 'wp_lazy_loading_enabled', '__return_true' );

// Everyone knows what asterisk means in forms so we don't need to display this
add_filter( 'gform_required_legend', '__return_empty_string' );

/**
 * Generate unique ACF block id
 *
 * @see https://www.advancedcustomfields.com/resources/whats-new-with-acf-blocks-in-acf-6/#block-id)
 *
 * @param array $attributes ACF block attributes
 */
function topten_acf_unique_block_id( $attributes ) {
	if ( empty( $attributes['anchor'] ) ) {
		$attributes['anchor'] = 'acf-block-' . uniqid();
	}

	return $attributes;
}

add_filter( 'acf/pre_save_block', 'topten_acf_unique_block_id' );

if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
	/**
	 * Add missing breadcrumbs to single card pages based on the card status, only if Yoast SEO is active.
	 *
	 * @param array $links The breadcrumbs array
	 */
	function topten_yoast_breadcrumbs( $links ) {
		if ( is_page_template( 'template-vertaa.php' ) ) {
			global $card_id;

			$id         = $card_id;
			$status     = get_field( 'card_status_publish', $id );
			$breadcrumb = array();

			if ( is_array( $status ) ) {
				if ( in_array( 'valid', $status, true ) || in_array( 'approved_for_repeal', $status, true ) ) {
					$breadcrumb[] = array(
						'url'  => get_permalink( get_field( 'main_card_archive', 'options' ) ),
						'text' => get_the_title( get_field( 'main_card_archive', 'options' ) ),
					);
				}
				if ( in_array( 'expired', $status, true ) ) {
					$breadcrumb[] = array(
						'url'  => get_permalink( get_field( 'expired_card_archive', 'options' ) ),
						'text' => get_the_title( get_field( 'expired_card_archive', 'options' ) ),
					);
				}
				if ( in_array( 'future', $status, true ) ) {
					$breadcrumb[] = array(
						'url'  => get_permalink( get_field( 'future_card_archive', 'options' ) ),
						'text' => get_the_title( get_field( 'future_card_archive', 'options' ) ),
					);
				}

				$breadcrumb[] = array(
					'url'  => get_permalink( $id ),
					'text' => get_the_title( $id ),
				);
			}

			array_splice( $links, 1, -2, $breadcrumb );
		} elseif ( is_singular( 'tulkintakortti' ) || is_singular( 'ohjekortti' ) || is_singular( 'lomakekortti' ) ) {
			$id         = get_the_ID();
			$status     = get_field( 'card_status_publish', $id );
			$breadcrumb = array();

			if ( is_array( $status ) ) {
				if ( in_array( 'valid', $status, true ) || in_array( 'approved_for_repeal', $status, true ) ) {
					$breadcrumb[] = array(
						'url'  => get_permalink( get_field( 'main_card_archive', 'options' ) ),
						'text' => get_the_title( get_field( 'main_card_archive', 'options' ) ),
					);
				}
				if ( in_array( 'expired', $status, true ) ) {
					$breadcrumb[] = array(
						'url'  => get_permalink( get_field( 'expired_card_archive', 'options' ) ),
						'text' => get_the_title( get_field( 'expired_card_archive', 'options' ) ),
					);
				}
				if ( in_array( 'future', $status, true ) ) {
					$breadcrumb[] = array(
						'url'  => get_permalink( get_field( 'future_card_archive', 'options' ) ),
						'text' => get_the_title( get_field( 'future_card_archive', 'options' ) ),
					);
				}
			}

			array_splice( $links, 1, -2, $breadcrumb );
		}

		return $links;

	}

	add_filter( 'wpseo_breadcrumb_links', 'topten_yoast_breadcrumbs' );
}

/**
 * Remove dashboard bloat
 */
function topten_remove_dashboard_widgets() {
	// Tapahtumat ja uutiset
	remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );

	// Sisällöt lyhyesti
	remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_right_now', 'dashboard', 'side' );

	// Nopea luonnos
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );

	// Recent activity
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_activity', 'dashboard', 'side' );

	// Sivuston terveys
	remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_site_health', 'dashboard', 'side' );

	// Yoast
	remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' );
	remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'side' );

	// Yoast SEO / Wincher
	remove_meta_box( 'wpseo-wincher-dashboard-overview', 'dashboard', 'normal' );
	remove_meta_box( 'wpseo-wincher-dashboard-overview', 'dashboard', 'side' );

	// Limit Login Attempts
	remove_meta_box( 'llar_stats_widget', 'dashboard', 'normal' );
	remove_meta_box( 'llar_stats_widget', 'dashboard', 'side' );

	// Gravity Forms
	remove_meta_box( 'rg_forms_dashboard', 'dashboard', 'normal' );
	remove_meta_box( 'rg_forms_dashboard', 'dashboard', 'side' );

	// Broken Link Checker
	remove_meta_box( 'blc_dashboard_widget', 'dashboard', 'normal' );
	remove_meta_box( 'blc_dashboard_widget', 'dashboard', 'side' );

	// Easy WP SMTP
	remove_meta_box( 'easy_wp_smtp_reports_widget_lite', 'dashboard', 'normal' );
	remove_meta_box( 'easy_wp_smtp_reports_widget_lite', 'dashboard', 'side' );
}

add_action( 'wp_dashboard_setup', 'topten_remove_dashboard_widgets' );



/**
 * Do this only if gravity forms exists
 */
if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
	/**
	 * Changing default error message Gravity Forms (GF2.5)
	 *
	 * @param message $message = the error message
	 * @param form    $form = the form
	 */
	function topten_change_message( $message, $form ) {
		return '<p class="gform_submission_error hide_summary">
					Ongelma lomakkeen täytössä. Ole hyvä ja tarkista alla olevat kentät.
				</p>';
	}

	add_filter( 'gform_validation_message', 'topten_change_message', 10, 2 );

	/**
	 * Add subscriber to list when a specific form is submitted
	 *
	 * @param entry $entry = submitted entry
	 * @param form  $form = the form it was submitted from
	 */
	function topten_after_submission_handler( $entry, $form ) {
		// Get form ID from options page
		$subscriberForm   = get_field( 'subscriber_form', 'options' );
		$subscriberFormId = $subscriberForm['fields'][0]['formId'];
		// get id of the form that was just submitted
		$submittedFormId = intval( $entry['form_id'] );
		// if the form is subscriber form, email is set and consent is acquired (these are required fields, but you know, javascript)
		if ( $subscriberFormId === $submittedFormId ) {
			if ( ! empty( $entry['10.1'] ) && ! empty( $entry['3'] ) ) {
				// unsure if it's somehow possible to set these in gravity forms so we don't get these ridiculous 10.1, 3 and 7 values as ids. at least not in the free version gui, probably somehow possible via code but this seems like a clusterf
				$userEmail        = esc_html( $entry['3'] );
				$userOrganization = esc_html( $entry['7'] );
				// make logic for this when needed, language versions will not be present at release
				$language = 'fi';
				// check if email exists in list, if it does, do nothing
				$alreadyExists = false;
				if ( have_rows( 'subscribers', 'options' ) ) {
					while ( have_rows( 'subscribers', 'options' ) ) {
						the_row();
						if ( get_sub_field( 'email', 'options' ) === $userEmail ) {
							$alreadyExists = true;
						}
					}
				}
				// in other case add to the list
				if ( false === $alreadyExists ) {
					$data = array(
						'email'        => $userEmail,
						'organization' => $userOrganization,
						'language'     => $language,
					);
					add_row( 'subscribers', $data, 'option' );
				}
			}
		}
	}
	add_action( 'gform_after_submission', 'topten_after_submission_handler', 10, 2 );
}

/**
 * Do not optimize PDFs
 *
 * @param bool  $optimize      True to optimize, false otherwise.
 * @param int   $attachment_id Attachment ID.
 * @param array $metadata      An array of attachment meta data.
 */
function topten_imagify_ignore( $optimize, $attachment_id, $metadata ) {
	$attachment_type = get_post_mime_type( $attachment_id );

	if ( 'application/pdf' === $attachment_type ) {
		return false;
	}

	return $optimize;
}

add_filter( 'imagify_auto_optimize_attachment', 'topten_imagify_ignore', 10, 3 );


/**
 * ACF the_field replacement that allows iframes
 *
 * @param string   $field_name ACF field name
 * @param int|bool $post_id Post ID
 */
function gro_the_field( $field_name, $post_id = false ) {
	$post_id = acf_filter_post_id( $post_id );

	$field = get_field( $field_name, $post_id );

	if ( $field ) {
		
		$allowed_html           = wp_kses_allowed_html( 'post' );
		$allowed_html['iframe'] = array(
			'src'             => true,
			'width'           => true,
			'height'          => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
		);
		echo wp_kses( $field, $allowed_html );
	}
}

/**
 * ACF the_sub_field replacement that allows iframes
 *
 * @param string   $field_name ACF field name
 * @param int|bool $post_id Post ID
 */
function gro_the_sub_field( $field_name, $post_id = false ) {
	$post_id = acf_filter_post_id( $post_id );

	$field = get_sub_field( $field_name, $post_id );

	if ( $field ) {
		
		$allowed_html           = wp_kses_allowed_html( 'post' );
		$allowed_html['iframe'] = array(
			'src'             => true,
			'width'           => true,
			'height'          => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
		);
		echo wp_kses( $field, $allowed_html );
	}
}
