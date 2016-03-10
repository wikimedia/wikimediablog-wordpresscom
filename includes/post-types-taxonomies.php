<?php  

add_action( 'init', 'wmb_cpt_init' );
function wmb_cpt_init() {
	register_post_type('non-english', array(
		'labels' => array(
			'name'	 => 'Non-English Posts',
			'singular_name' => 'Non-English Post',
			'add_new' => '',
			'add_new_item' => __( 'Add new Non-English Post' ),
			'view_item' => 'View Non-English Post',
			'edit_item' => 'Edit Non-English Post',
			'new_item' => __('New Non-English Post'),
			'view_item' => __('View Non-English Post'),
			'search_items' => __('Search Non-English Posts'),
			'not_found' =>  __('No Non-English Posts found'),
			'not_found_in_trash' => __('No Non-English Posts found in Trash'),
		),
		'public' => true,
		'exclude_from_search' => false,
		'show_ui' => true,
		'hierarchical' => true,
	    'publicly_queryable' => true,
	    'query_var' => true,
	    'rewrite' => false,
		'supports' => array('title', 'author', 'editor', 'thumbnail', 'excerpt', 'comments'),
	));

	$labels = array(
		'name' => _('Languages' ),
		'singular_name' => _('Language' ),
		'search_items' =>  __( 'Search Languages' ),
		'popular_items' => __( 'Popular Languages' ),
		'all_items' => __( 'All Languages' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Language' ), 
		'update_item' => __( 'Update Language' ),
		'add_new_item' => __( 'Add New Language' ),
		'new_item_name' => __( 'New Language' ),
		'separate_items_with_commas' => __( 'Separate Languages with commas' ),
		'add_or_remove_items' => __( 'Add or remove Languages' ),
		'choose_from_most_used' => __( 'Choose from the most used Languages' ),
		'menu_name' => __( 'Languages' ),
	); 

	register_taxonomy('languages', array('non-english'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'query_var' => true,
	));

	$labels = array(
		'name' => _('Email Updates' ),
		'singular_name' => _('Email Update' ),
		'search_items' =>  __( 'Search Email Updates' ),
		'popular_items' => __( 'Popular Email Updates' ),
		'all_items' => __( 'All Email Updates' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Email Updates' ), 
		'update_item' => __( 'Update Email Updates' ),
		'add_new_item' => __( 'Add New Email Updates' ),
		'new_item_name' => __( 'New Email Updates' ),
		'separate_items_with_commas' => __( 'Separate Email Updates with commas' ),
		'add_or_remove_items' => __( 'Add or remove Email Updates' ),
		'choose_from_most_used' => __( 'Choose from the most used Email Updates' ),
		'menu_name' => __( 'Email Updates' ),
	); 

	register_taxonomy('email_update', array('post'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'show_in_menu' => false,
		'query_var' => true,
		'meta_box_cb' => 'wmb_email_update_taxonomy_meta_box'
	));

	if ( is_admin() && ! wpcom_vip_term_exists( 'not-in-email-updates', 'email_update' ) ) { 
		wp_insert_term(
			"Don't show in email updates",
			'email_update',
			array(
				'slug' => 'not-in-email-updates'
			)
		);
	}
}

// Only allow one featured_layout selection by changing checkboxes to radios
// Still a hack, but a little less hacky :)
function wmb_featured_layout_taxonomy_meta_box( $post, $box ) {
	ob_start();

	post_categories_meta_box( $post, $box );

	$html = ob_get_clean();

	$html = str_replace( '"checkbox"', '"radio"', $html );

	echo $html;
}

function wmb_email_update_taxonomy_meta_box( $post, $box ) {
	?>
	<style>
		#email_update-adder, #email_update-tabs { display: none; }
	</style>
	<?php
	post_categories_meta_box( $post, $box );

	echo '<p><a target="_blank" href="' . esc_attr( site_url() ) . '?email_digest=weekly">Preview weekly update</a>';
	echo '<p>Use the <b>Order</b> attribute below to change where this post appears in the weekly update. Lower numbers are shown first.</p>';
}