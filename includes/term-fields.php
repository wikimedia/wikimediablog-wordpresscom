<?php
# Creating the choose sidebar term meta field
$choose_sidebar = new Wikimedia_Term_Choose_Sidebar_Field('choose_sidebar', 'Category Sidebar', array(
	'category'
));

# Register the Category custom fields
add_action( 'fm_term_category', 'wmb_register_category_term_fields' );
function wmb_register_category_term_fields() {
	# If the Fieldmanager is not loaded, bail
	if ( ! class_exists( 'Fieldmanager_Field' ) ) {
		return;
	}

	# Register the Default Category Image custom fields group
	$image_field = new Fieldmanager_Media( array(
		'name'               => 'default_image',
		'button_label'       => __( 'Set Default Image (Recommended size: 1290px * 164px)', 'wmb' ),
		'modal_title'        => __( 'Select Image (Recommended size: 1290px * 164px)', 'wmb' ),
		'modal_button_label' => __( 'Use as Default Image', 'wmb' ),
		'preview_size'       => 'icon',
	) );
	
	# Activate the Featured Post meta box
	if ( method_exists( $image_field, 'add_term_meta_box' ) ) {
		$image_field->add_term_meta_box( __( 'Default Image', 'wmb' ), 'category' );
	} else {
		$image_field->add_term_form( __( 'Default Image', 'wmb' ), 'category', true, true );
	}

}
