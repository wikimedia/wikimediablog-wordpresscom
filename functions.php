<?php

require_once( WP_CONTENT_DIR . '/themes/vip/plugins/vip-init.php' );

# Load VIP plugins & configuration settings
wpcom_vip_load_plugin( 'fieldmanager' );
wpcom_vip_load_plugin( 'co-authors-plus', 'plugins', '3.2' );
wpcom_vip_load_category_base( 'c' );

# Load libraries and includes
include_once( __DIR__ . '/lib/widget-fields.php' );

include_once( __DIR__ . '/includes/post-types-taxonomies.php' );
include_once( __DIR__ . '/includes/common.php' );
include_once( __DIR__ . '/includes/comments.php' );
include_once( __DIR__ . '/includes/translation.php' );
include_once( __DIR__ . '/includes/user.php' );
include_once( __DIR__ . '/includes/author.php' );
include_once( __DIR__ . '/includes/categories.php' );
include_once( __DIR__ . '/includes/feed.php' );
include_once( __DIR__ . '/includes/commons.php' );
include_once( __DIR__ . '/includes/options.php' );
include_once( __DIR__ . '/includes/custom-fields.php' );
include_once( __DIR__ . '/includes/title.php' );
include_once( __DIR__ . '/includes/excerpt.php' );
include_once( __DIR__ . '/includes/image.php' );
include_once( __DIR__ . '/includes/layouts.php' );
include_once( __DIR__ . '/includes/shortcodes.php' );
include_once( __DIR__ . '/includes/widgets.php' );
include_once( __DIR__ . '/includes/rewrite.php' );
include_once( __DIR__ . '/includes/term-choose-sidebar.php' );
include_once( __DIR__ . '/includes/term-fields.php' );
include_once( __DIR__ . '/includes/admin-columns.php' );
include_once( __DIR__ . '/includes/scripts.php' );
include_once( __DIR__ . '/includes/co-authors.php' );

# Enqueue JS and CSS assets on the front-end
add_action( 'wp_enqueue_scripts', 'wmb_wp_enqueue_scripts' );
function wmb_wp_enqueue_scripts() {
	$template_dir = get_template_directory_uri();

	# Enqueue jQuery
	wp_enqueue_script( 'jquery' );

	# Enqueue Custom JS files
	wp_enqueue_script( 'theme-flexslider', $template_dir . '/js/jquery.flexslider-min.js', array( 'jquery' ), '2.5.0' );
	wp_enqueue_script( 'wmb-json', $template_dir . '/js/jquery.json.js', array('jquery') );
	wp_enqueue_script( 'wmb-event-logging', $template_dir . '/js/jquery.eventlogging.js', array('jquery') );
	wp_enqueue_script( 'theme-functions', $template_dir . '/js/functions.js', array( 'jquery' ) );

	# Enqueue Custom CSS files
	wp_enqueue_style( 'theme-font-awesome', $template_dir . '/css/font-awesome.min.css', array( 'theme-styles' ), '4.4.0' );
	wp_enqueue_style( 'theme-styles', get_stylesheet_uri() );

	# Enqueue comments reply script on singular pages
	if ( is_singular() ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

# Enqueue JS and CSS assets on the back-end
add_action( 'admin_enqueue_scripts', 'wmb_admin_enqueue_scripts' );
function wmb_admin_enqueue_scripts() {

	$template_dir = get_template_directory_uri();

	# Enqueue Custom CSS files
	wp_enqueue_style( 'theme-admin-css', $template_dir . '/css/admin.css' );

}

# Theme setup
add_action( 'after_setup_theme', 'wmb_after_setup_theme' );
function wmb_after_setup_theme() {
	# Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	# Enable automatic feed links
	add_theme_support( 'automatic-feed-links' );

	# Allow ordering of posts
	add_post_type_support( 'post', 'page-attributes' );

	# Enable post thumbnails
	add_theme_support( 'post-thumbnails' );

	# Register custom image sizes
	add_image_size( 'blog_listing', 275, 0, true );
	add_image_size( 'featured_slider_full', 1290, 500, array( 'right', 'top' ) );
	add_image_size( 'featured_slider_half', 645, 500, array( 'right', 'top' ) );
	add_image_size( 'featured_slider_post', 1290, 164, array( 'right', 'top' ) );
	add_image_size( 'featured_slider_post_half', 645, 164, array( 'right', 'top' ) );
	add_image_size( 'featured_slider_cat_default', 1290, 164, array( 'center', 'center' ) );
	add_image_size( 'category_posts_widget', 146, 110, true );

	# Add Jetpack infinite scroll theme support
	add_theme_support( 'infinite-scroll', array(
		'container' => 'articles',
		'type'      => 'click',
		'render'    => 'wmb_jetpack_infinite_scroll_render',
	) );

	# Add theme support for navigation menus
	add_theme_support( 'menus' );

	# Register custom navigation menu locations
	register_nav_menus(array(
		'main-menu'   => __( 'Main Menu', 'wmb' ),
	) );

	# Register sidebars
	add_action( 'widgets_init', 'wmb_widgets_init' );
}


# Register Sidebars
# Note: In a child theme with custom wmb_setup_theme() this function is not hooked to widgets_init
function wmb_widgets_init() {
	register_sidebar(array(
		'name'          => __( 'Default Sidebar', 'wmb' ),
		'id'            => 'default-sidebar',
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>'
	) );
}

# Register custom query vars
add_filter( 'query_vars', 'wmb_add_query_vars' );
function wmb_add_query_vars( $vars ){
	$vars[] = "wmb_post_id";
	return $vars;
}

# Checks if a query should be ordered by featured posts
function wmb_is_ordered_query( $q ) {
	return ! is_admin() && $q->is_main_query() && $q->is_home();
}

# Locates a template and includes it with the given args as locals
function wmb_template( $template, $template_vars = array() ) {
	$template = str_replace('_', '-', $template);

	if ( 0 === validate_file( 'templates/' . $template . '.php' ) ) {
		include( locate_template( 'templates/' . $template . '.php' ) );
	}
}

# Add 'current-menu-item' to each item that is somewhat active
add_filter('nav_menu_css_class' , 'wmb_nav_menu_class');
function wmb_nav_menu_class($classes){
	$active = array(
		'current-menu-item',
		'current-menu-parent',
		'current-menu-ancestor',
		'current_page_item',
		'current_page_parent',
		'current_page_ancestor',
	);

	foreach( $active as $c ) {
		if( in_array( $c, $classes, true ) ) {
			$classes[] = 'current-menu-item';
		}
	}

	return $classes;
}

# Custom render function for Jetpack Infinite Scroll module
function wmb_jetpack_infinite_scroll_render() {
	get_template_part( 'loop' );
}

# Add a wrapper around the embeds
add_filter( 'embed_oembed_html', 'wmb_embed_oembed_html', 10, 4 );
function wmb_embed_oembed_html( $html, $url, $attr, $post_id ) {
	$before = '<div class="video-frame"><div class="wrap">';
	$after = '</div></div>';
	$html = $before . $html . $after;
	return $html;
}

# Since our weekly updates are set to go out on Thursdays at 12pm Pacific, we'll only want to grab posts since then for the weekly update preview
function wmb_posts_since_last_monday( $where = '' ) {
	$where .= " AND post_date > '" . date('Y-m-d', strtotime("last Thursday")) . " 12:00:00'";
    return $where;
}

# Force the visibility of revisions and excerpt boxes
add_filter( 'default_hidden_meta_boxes', 'wmb_enable_custom_fields_per_default', 20, 1 );
function wmb_enable_custom_fields_per_default( $hidden ) {
	foreach ( $hidden as $i => $metabox ) {
		if ( 'revisionsdiv' == $metabox || 'postexcerpt' == $metabox ) {
			unset ( $hidden[$i] );
		}
	}
	return $hidden;
}

# Get the caption of the active thumbnail
function wmb_thumbnail_caption() {
	if( ! has_post_thumbnail() ) {
		return;
	}

	$thumbnail = get_post( get_post_meta( get_the_ID(), '_thumbnail_id', true ) );
	if( $thumbnail && $thumbnail->post_excerpt ) {
		echo '<p class="img-info">' . nl2br( strip_tags( $thumbnail->post_excerpt, '<a>' ) ) . '</p>';
	}
}

# Disable media embeds in comments, for reader privacy reasons
# per https://lobby.vip.wordpress.com/2014/09/04/coming-soon-enabling-embeds-in-comments/
add_filter( 'wpcom_vip_enable_full_comment_embeds', '__return_false' );

# Deactivate anti-widowing (https://vip.wordpress.com/functions/widont/ ) because
# it often causes post titles in the "Featured posts" list to run into the image
# per https://wordpressvip.zendesk.com/requests/33250
remove_filter( 'the_title', 'widont' );

function wmb_custom_is_support() {
	$supported = current_theme_supports( 'infinite-scroll' ) && ( is_home() || is_archive() || is_search() );

	return $supported;
}
add_filter( 'infinite_scroll_archive_supported', 'wmb_custom_is_support' );

function wmb_filter_jetpack_infinite_scroll_js_settings( $settings ) {
	$settings['text'] = __( 'Load more', 'l18n' );
	return $settings;
}
add_filter( 'infinite_scroll_js_settings', 'wmb_filter_jetpack_infinite_scroll_js_settings' );

# Default to HTTPS canonical URLs
add_filter( 'rel_canonical', function( $canonical_url ) {
	return str_replace( 'http://', 'https://', $canonical_url );
} );

# Default to HTTPS shortlink URLs
add_filter( 'get_shortlink', 'wmb_filter_shortlink', 1000, 3 );
function wmb_filter_shortlink( $shortlink, $id, $context ) {
    return str_replace( 'http://', 'https://', $shortlink );
}

