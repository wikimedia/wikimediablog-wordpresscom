<?php

add_action( 'wp_head', 'wmb_init_ajaxurl' );
function wmb_init_ajaxurl() {
	$url = admin_url( 'admin-ajax.php' );
	?>
	<script type="text/javascript">
		var ajaxurl = <?php echo wp_json_encode( $url ); ?>;
	</script>
	<?php
}

add_filter( 'media_upload_tabs', 'wmb_add_media_tabs' );
function wmb_add_media_tabs( $tabs ) {
	$tabs['importFromCommons'] = 'Import from Commons';
	return $tabs;
}

add_action( 'media_upload_importFromCommons', 'wmb_add_load_page' );
function wmb_add_load_page() {
    return wp_iframe( 'wmb_content_load_page' );
}

function wmb_content_load_page() {
	global $post;
	media_upload_header();
	?>
    <div class="describe" style="margin: 20px;">Image URL:
		<input id="src" type="text" name="imageurl" style="font-size: 18px;padding: 12px 14px;width: 100%;"><br/>
		<div class="help-text" style="font-size:13px;color: #000088;padding: 5px;">You MUST use an image URL that specifies the width (i.e. <i>https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Bald_Eagle_Head_sq.jpg/768px-Bald_Eagle_Head_sq.jpg</i>)</div>
		<input type="submit" id="loadImage" value="Upload">
		<div id="succes"></div>
    </div>
	<?php
}

add_action( 'admin_enqueue_scripts', 'wmb_commons_caption' );
function wmb_commons_caption( $hook ) {
    wp_deregister_script( 'upload-media-script' );
    wp_register_script( 'upload-media-script', get_template_directory_uri() . '/js/caption-from-commons.js' );
    wp_enqueue_script( 'upload-media-script' );
}

function wmb_get_xml( $url ) {
	$xml_text = wpcom_vip_file_get_contents( $url );

	libxml_use_internal_errors( true );
	$xml = simplexml_load_string( $xml_text );

	return $xml;
}

function wmb_ajax_error() {
    header('HTTP/1.1 500 Commons API Error');
    header('Content-Type: application/json');
    die();
}

function wmb_media_upload_type_form1( $type = 'file', $errors = null, $id = null ) {
	$post_id = intval( wmb_request_param( 'post_id' ) );

	$form_action_url = admin_url( "media-upload.php?type=$type&tab=type&post_id=$post_id" );
	$form_action_url = apply_filters( 'media_upload_form_url', $form_action_url, $type );
	?>

	<form enctype="multipart/form-data" method="post" action="<?php echo esc_url( $form_action_url ); ?>" class="media-upload-form type-form validate" id="<?php echo esc_attr( $type ); ?>-form">
	<input type="submit" class="hidden" name="save" value="" />
	<input type="hidden" name="post_id" id="post_id" value="<?php echo esc_attr( intval( $post_id ) ); ?>" />
	<?php wp_nonce_field( 'media-form' ); ?>

	<script type="text/javascript">
		//<![CDATA[
		jQuery(function($){
			var preloaded = $(".media-item.preloaded");
			if ( preloaded.length > 0 ) {
				preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
			}
			updateMediaForm();
		});
		//]]>
	</script>
	<div id="media-items">
	<?php
		if($id) {
			if( ! is_wp_error( $id ) ) {
				add_filter( 'attachment_fields_to_edit', 'wmb_custom_attachments_fields', 10, 2 );
				echo get_media_items( $id, $errors );
			} else {
				echo '<div id="media-upload-error">' . esc_html( $id->get_error_message() ) . '</div>';
				exit;
			}
		}
	?>
	</div>
	<p class="savebutton ml-submit">
		<input type="submit" class="button" name="save" value="<?php esc_attr_e( 'Save all changes' ); ?>" />
	</p>
	</form>

	<?php
}

function wmb_custom_attachments_fields( $form_fields, $post ) {
    $field_value = get_post_meta( $post->ID, 'post_excerpt', true );
    $allowed_html = array(
        'a' => array(
            'href' => array(),
            'title' => array()
        )
    );
    $value = html_entity_decode( $post->post_excerpt, ENT_QUOTES, 'UTF-8' );

    $form_fields['post_excerpt'] = array(
        'label' => __( 'Caption' ),
        'input' => 'html',
        'html' => "<textarea name='attachments[" . esc_attr( $post->ID ) . "][post_excerpt]' id='attachments[" . esc_attr( $post->ID ) . "][post_excerpt]'>" . wp_kses( $value, $allowed_html ) . "</textarea>"
    );
    return $form_fields;
}


add_action( 'wp_ajax_get_commons_data', 'wmb_ajax_get_commons_data' );
function wmb_ajax_get_commons_data() {
	require_once( ABSPATH . 'wp-admin' . '/includes/image.php' );
	require_once( ABSPATH . 'wp-admin' . '/includes/file.php' );
	require_once( ABSPATH . 'wp-admin' . '/includes/media.php' );

	$url = sanitize_text_field( $_POST['url'] );
	$from_last_slash = strrchr( $url, '/' ); // get part of string containing width
	$pos_px = strpos( $from_last_slash, 'px' ); // get position of "px"
	$width = substr( $from_last_slash, 1, $pos_px - 1 ); // get width
	$url_without_width = str_replace( $from_last_slash, '', $url ); // remove part of string containing width
	$filename = strrchr( $url_without_width, '/' ); // get filename with leading slash
	$filename = substr( $filename, 1 ); // remove leading slash

	if ( ! $width || ! $filename ) {
		wmb_ajax_error();
	}

	$xml = wmb_get_xml( 'https://tools.wmflabs.org/magnus-toolserver/commonsapi.php?image=' . $filename . '&thumbwidth=' . $width );

	if ( !$xml || property_exists( $xml, 'error' ) ) {
		wmb_ajax_error();
	}

	$extension = strrchr( $xml->file->name, '.' ); // get file extension
	$commons_filename = str_replace( $extension, '', $xml->file->name ); // remove extension

	$commons_uploader = $xml->file->uploader;
	$commons_uploader_url = 'https://commons.wikimedia.org/wiki/User:' . str_replace( ' ', '_', $commons_uploader );

	$post_id = absint( $_POST['post_id'] );
	$desc = $commons_filename;
	$commons_url = str_replace( 'http:', 'https:', $xml->file->urls->description );
	$caption = '<a href="' . esc_url( $commons_url ) . '">"' . esc_html( $commons_filename ) . '"</a> by <a href="' . esc_url( $commons_uploader_url ) . '">' . esc_html( $commons_uploader ) .
	  '</a>, under <a href="' . esc_url( $xml->licenses->license->license_text_url ) . '">' . esc_html( $xml->licenses->license->name ) . '</a>';

	if ( function_exists( 'wpcom_vip_download_image' ) ) {
		$id = wpcom_vip_download_image( $url, $post_id, $desc );

		update_post_meta( $id, 'commons_attach', stripslashes( $commons_url ) );
		update_post_meta( $post_id, 'files', $id );
		if ( $id ) {
			wp_update_post( array(
			      'ID'           => $id,
			      'post_excerpt' => $caption
			  ) );

			wmb_media_upload_type_form1( 'image', '', $id );
		}
	}

	die;
}

add_filter( 'attachment_link', 'wmb_filter_attachment_link', 10, 2 );
function wmb_filter_attachment_link( $link, $postID = 0 ) {
	update_option( 'image_default_link_type', 'post' );

	$commons_attach = get_post_meta( $postID, 'commons_attach', true );
	if ( empty( $commons_attach ) ) {
		$commons_attach = $link;
	}

	return $commons_attach;
}
