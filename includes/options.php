<?php

# If the Fieldmanager is not loaded, bail
if ( ! function_exists( 'fm_register_submenu_page' ) ) {
	return;
}

# Register the Options subpage under Appearance
fm_register_submenu_page( 'wmb_options', 'themes.php', __( 'Options', 'wmb' ) );

# Register the fields of the Options page
add_action( 'fm_submenu_wmb_options', 'wmb_register_options' );
function wmb_register_options() {
	$menus = wp_get_nav_menus();
	$menus_datasource = wp_list_pluck( $menus, 'name', 'term_id' );

	# Register the main options tabbed group
	$fm = new Fieldmanager_Group( array(
		'name'           => 'wmb_options',
		'tabbed'         => 'vertical',
		'children'       => array(
			# Social tab
			'social' => new Fieldmanager_Group( array(
				'label'    => __( 'Social', 'wmb' ),
				'children' => array(
					# Social tab - fields
					'facebook_link'    => new Fieldmanager_TextField( __( 'Facebook Link', 'wmb' ) ),
					'instagram_link'   => new Fieldmanager_TextField( __( 'Instagram Link', 'wmb' ) ),
					'twitter_link'     => new Fieldmanager_TextField( __( 'Twitter Link', 'wmb' ) ),
					'linkedin_link'    => new Fieldmanager_TextField( __( 'LinkedIn Link', 'wmb' ) ),
					'rss_link'         => new Fieldmanager_TextField( __( 'RSS Link', 'wmb' ) ),
				)
			) ),

			# Featured Area tab
			'featured_area' => new Fieldmanager_Group( array(
				'label'    => __( 'Featured Area', 'wmb' ),
				'children' => array(
					# Featured Area tab - fields
					'default_image'    => new Fieldmanager_Media( array(
						'name'               => 'default_image',
						'button_label'       => __( 'Set Default Image (Recommended size: 1290px * 164px)', 'wmb' ),
						'modal_title'        => __( 'Select Image (Recommended size: 1290px * 164px)', 'wmb' ),
						'modal_button_label' => __( 'Use as Default Image', 'wmb' ),
						'preview_size'       => 'icon',
					) )
				)
			) ),

			# Footer tab
			'footer' => new Fieldmanager_Group( array(
				'label'    => __( 'Footer', 'wmb' ),
				'children' => array(
					# Footer tab - about fields
					'about_title'        => new Fieldmanager_TextField( __( 'About Title', 'wmb' ) ),
					'about_text'        => new Fieldmanager_RichTextArea( __( 'About Text', 'wmb' ) ),

					# Footer tab - left column fields
					'left_column_title'  => new Fieldmanager_TextField( __( 'Left Column Title', 'wmb' ) ),
					'left_column_text'  => new Fieldmanager_RichTextArea( __( 'Left Column Text', 'wmb' ) ),
					'left_column_menu'  => new Fieldmanager_Select( array(
						'name' => 'left_column_menu',
						'label' => __( 'Left Column Menu', 'wmb' ),
						'options' => $menus_datasource,
						'first_empty' => true,
					) ),

					# Footer tab - right column fields
					'right_column_title' => new Fieldmanager_TextField( __( 'Right Column Title', 'wmb' ) ),
					'right_column_text' => new Fieldmanager_RichTextArea( __( 'Right Column Text', 'wmb' ) ),
					'right_column_menu'  => new Fieldmanager_Select( array(
						'name' => 'right_column_menu',
						'label' => __( 'Right Column Menu', 'wmb' ),
						'options' => $menus_datasource,
						'first_empty' => true,
					) ),

					# Footer tab - copyright fields
					'copyright_text'    => new Fieldmanager_RichTextArea( __( 'Copyright Text', 'wmb' ) ),
				)
			) ),

			# Discusion tab
			'discussion' => new Fieldmanager_Group( array(
				'label'    => __( 'Discussion', 'wmb' ),
				'children' => array(
					# Discussion tab - fields
					'captcha_question'    => new Fieldmanager_TextArea( __( 'Captcha Question', 'wmb' ) ),
					'captcha_answer'    => new Fieldmanager_TextField( __( 'Captcha Answer', 'wmb' ) ),
				)
			) ),

		)
	) );
	
	# Activate the Options submenu page
	$fm->activate_submenu_page();
}