<?php

# If the Fieldmanager is not loaded, bail
if ( ! class_exists( 'Fieldmanager_Field' ) ) {
	return;
}

# Register the Post custom fields
add_action( 'fm_post_post', 'wmb_register_post_custom_fields' );
function wmb_register_post_custom_fields() {

	# Register the Featured Post custom fields group
	$fm = new Fieldmanager_Group( array(
		'name'           => 'featured_post',
		'children'       => array(
			# Featured image field
			'image' => new Fieldmanager_Media( array(
				'name'               => 'image',
				'button_label'       => __( 'Set Featured Post Image (Half size: 645x500, Full size: 1290x500)', 'wmb' ),
				'modal_title'        => __( 'Select Image (Half size: 645x500, Full size: 1290x500)', 'wmb' ),
				'modal_button_label' => __( 'Use as Featured Image', 'wmb' ),
				'preview_size'       => 'icon',
			) ),
		)
	) );
	
	# Activate the Featured Post meta box
	$fm->add_meta_box( __( 'Featured Post', 'wmb' ), 'post' );

	# Register the Post Banner custom fields group
	$fm = new Fieldmanager_Group( array(
		'name'           => 'post_banner',
		'children'       => array(
			# Post Banner image field
			'image' => new Fieldmanager_Media( array(
				'name'               => 'image',
				'button_label'       => __( 'Set Post Banner Image (Half size: 645x164, Full size: 1290x164)', 'wmb' ),
				'modal_title'        => __( 'Select Image (Half size: 645x164, Full size: 1290x164)', 'wmb' ),
				'modal_button_label' => __( 'Use as Banner Image', 'wmb' ),
				'preview_size'       => 'icon',
			) ),
		)
	) );
	
	# Activate the Post Banner meta box
	$fm->add_meta_box( __( 'Post Banner', 'wmb' ), 'post' );
}