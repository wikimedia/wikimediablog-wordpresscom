<?php

# Modify whether the taxonomy is globalized.
add_action( 'wpcom_is_globalized_taxonomy', 'wmb_wpcom_is_globalized_taxonomy', 10, 2 );
function wmb_wpcom_is_globalized_taxonomy( $is_global, $taxonomy ) {
	if ( 'languages' == $taxonomy )
		return false;

	return $is_global;
}

# Change the way available languages are displayed before the post
add_filter( 'icl_post_alternative_languages', 'wmb_alternative_languages' );
function wmb_alternative_languages( $code ) {
	$languages = icl_get_languages('skip_missing=true');
	if( count( $languages ) < 2 ) {
		return '';
	}
	
	$out = '<div class="translate-options not-mobile">';
	$out .= '<span class="label">' . sprintf( esc_html__( 'This article is available in %d languages', 'wmb' ), count( $languages ) ) .  '</span>';
	foreach( $languages as $code => $language ) {
		$out .= '<a href="' . esc_url( $language[ 'url' ] ) . '">' . esc_html( $language[ 'translated_name' ] ) . '</a>';
	}
	$out .= '</div>';

	$out .= '<div class="mobile-only translate-options">';
		$out .= '<a href="#lang-chooser" class="colorbox">' . sprintf( esc_html__( 'This article is available in %d languages ', 'wmb' ), count( $languages) ) . '</a>';
		$out .= '<div style="display:none">
			<div id="lang-chooser">
				<p>' . sprintf( esc_html__( 'This article is available in %d languages ', 'wmb' ), count( $languages) ) . '</p>
				<ul>';

				foreach( $languages as $code => $language ) {
					$out .= '<li><a href="' . esc_url( $language[ 'url' ] ) . '">' . esc_html( $language[ 'translated_name' ] ) . '</a></li>';
				}

				$out .= '</ul>
			</div>
		</div>';
	$out .= '</div>';

	return $out;
}


function wmb_get_english_post_id( $post_id ) {
	$post = get_post($post_id);
	if ( $post->post_type=='non-english' ) {
		return get_post_meta($post->ID, 'english_post', true);
	} else {
		return $post_id;
	}
}

add_action('add_meta_boxes', 'wmb_add_translate_metabox');
function wmb_add_translate_metabox() {
	add_meta_box('translate', 'Language', 'wmb_generate_translate_box', 'post', 'side', 'high');
	add_meta_box('translate', 'Language', 'wmb_generate_translate_box', 'non-english', 'side', 'high');
}

function wmb_generate_translate_box($post) {
	if($post->post_type == 'non-english') {
		$post_id = get_post_meta($post->ID, 'english_post', true);
	} else {
		$post_id = $post->ID;
	}
	$translates = get_post_meta($post_id, 'translates', true);
	$languages = get_terms('languages', array('hide_empty'=>false));
	$translation = '';
	$current = false;
	foreach($languages as $value) {
		$flag = false;
		if(!empty($translates)) {
			foreach($translates as $translate) { 
				$translate_terms = get_the_terms($translate, 'languages'); 
				if ( ! $translate_terms || is_wp_error( $translate_terms ) ) {
					$translate_terms = array();
				}
				$translate_terms = wp_list_pluck($translate_terms, 'term_id'); 

				if(!empty($translate_terms)) {
					if($translate_terms[0] == $value->term_id) {
						$flag = $translate;
						break;
					}
				}
			}			
		}
		if($flag && get_post_status($flag) == 'publish') {
			if($flag != $post->ID) {
				$translation .= 'Translation: <a href="' . esc_url( get_edit_post_link( $flag ) ) . '" title="">' . esc_html( $value->name ) . '</a> <br>';
			} else {
				$current = $value->name;
				$translation .= 'Translation: <a href="' . esc_url( get_edit_post_link( $post_id ) ) . '" title=""> English </a> <br>';
			}
		}
	}

	if($current) {
		echo 'Language of this article: <b>' . esc_html( $current ) . '</b><br><br>';
	} elseif($post->post_type == 'post') {
		echo 'Language of this article: <b>English </b><br> <br>';
	} else {
		echo 'Language of this article: <b>Non-english </b><br> <br>';
	}

	echo $translation;
	echo '<br/><a href="' . esc_url( admin_url( 'post-new.php?post_type=non-english&english_post=' . $post_id ) ) . '" title="">Add Translation</a> <br>';
}

add_action('admin_menu', 'wmb_remove_add_new_link', 999);
function wmb_remove_add_new_link() {
	$page = remove_submenu_page('edit.php?post_type=non-english', 'post-new.php?post_type=non-english');
}

function wmb_language_block_for_single($post) { 
	if($post->post_type == 'non-english') {
		$post_id = get_post_meta($post->ID, 'english_post', true);
	} else {
		$post_id = $post->ID;
	}
	$translates = get_post_meta($post_id, 'translates', true); 
	$languages = get_terms('languages', array('hide_empty'=>false)); 
	$translation = '<a class="langLink" href="' . esc_url( get_permalink( $post_id ) ) . '" title=""> English </a>';
	$translation_mobile = '<li><a href="' . esc_url( get_permalink( $post_id ) ) . '"> English </a></li>';
	$count = 1;
	if(!empty($translates))	{
		foreach($languages as $value) {
			$flag = false;
			foreach($translates as $translate) {
				$translate_terms = get_the_terms($translate, 'languages');
				if ( ! $translate_terms || is_wp_error( $translate_terms ) ) {
					$translate_terms = array();
				}
				$translate_terms = wp_list_pluck($translate_terms, 'term_id');

				if(!empty($translate_terms)) {
					if($translate_terms[0] == $value->term_id) {
						$flag = $translate;
						break;
					}
				}
				
			}
			if($flag) { 
				$translation .= '<a class="langLink" href="' . esc_url( get_permalink( $flag ) ) . '" title="">' . esc_html( $value->description ) . '</a>';
				$translation_mobile .= '<li><a href="' . esc_url( get_permalink( $flag ) ) . '">' . esc_html( $value->description ) . '</a></li>';
				$count++;
			}
		}
		if($count > 1) {
			echo 	'<div class="translate-options">
						<span class="label">This article is available in:</span>' . wp_kses_post( $translation ) . 
					'</div>';
		}

	}
}

add_action('pre_post_update', 'wmb_update_none_english', 10, 2);
function wmb_update_none_english($post_id, $post) {
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}
	$type_flug = false;
	$english_post = get_post_meta($post_id, 'english_post', true);
	if($post['post_type'] === 'non-english' && !empty($english_post)) { 
		$prev_translates = get_post_meta($english_post, 'translates', true);
		if ( ! $prev_translates ) {
			$prev_translates = array();
		}
		if( ! in_array($post_id, $prev_translates, true)) {
			foreach($prev_translates as $key => $value) {
				$translate_post = get_post($value);
				$translate_terms = get_the_terms($translate_post->ID, 'languages');
				if ( ! $translate_terms || is_wp_error( $translate_terms ) ) {
					$translate_terms = array();
				}
				$translate_terms = wp_list_pluck($translate_terms, 'term_id');

				if(!empty($_POST['tax_input'])) {
					$languages_input = isset( $_POST['tax_input']['languages'] ) ? (array) $_POST['tax_input']['languages'] : array();
					$languages = array_map( 'sanitize_text_field', $languages_input );
					if(in_array($translate_terms[0], $languages, true)) {
						$type_flug = true;
						$prev_translates[$key] = $post_id;
						break;
					}
				} else {
					$translate_new_terms = get_the_terms($post_id, 'languages');
					if ( ! $translate_new_terms || is_wp_error( $translate_new_terms ) ) {
						$translate_new_terms = array();
					}
					$translate_new_terms = wp_list_pluck($translate_terms, 'term_id');
					if(!empty($translate_new_terms) && in_array($translate_terms[0], $translate_new_terms[0], true)) {
						$type_flug = true;
						$prev_translates[$key] = $post_id;
						break;
					}
				}
			}
			if(isset($type_flug) && !$type_flug) {
				$prev_translates[] = $post_id;
			}
			update_post_meta($english_post, 'translates', $prev_translates);
		}
	}
}

add_filter('wp_insert_post_data', 'wmb_none_english_comments');
function wmb_none_english_comments($data) {
    if($data['post_type'] == 'non-english') {
        $data['comment_status'] = 'open';
    }
    return $data;
}

add_action('save_post', 'wmb_save_none_english', 10, 2);
function wmb_save_none_english($post_id, $post) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$language = isset( $_GET['language'] ) ? sanitize_text_field( $_GET['language'] ) : '';
	$english_post = isset( $_GET['english_post'] ) ? sanitize_text_field( $_GET['english_post'] ) : '';

	if( $language ) {
		$t = wp_set_post_terms($post_id, $language, 'languages', false);
	}
	if($english_post) {
		$t = update_post_meta($post_id, 'english_post', $english_post);
	} 
}

add_action('trashed_post', 'wmb_del_lang_tags', 10);
function wmb_del_lang_tags($post_id) {
	if( !current_user_can('delete_posts') ) {
		return;
	}

	$post = get_post($post_id);

	if($post->post_type == 'non-english') {
		$english_post = get_post_meta($post_id, 'english_post', true);
		$translates = get_post_meta($english_post, 'translates', true);
		$key = array_search($post_id, $translates);
		unset($translates[$key]);
		$translates = array_values($translates);
		update_post_meta($english_post, 'translates', $translates);
	}
}

add_filter('post_type_link', 'wmb_filter_post_type_link', 10, 3);
add_filter('post_link', 'wmb_filter_post_type_link', 10, 3);
function wmb_filter_post_type_link( $link, $post = 0 ){
    if ( ! $post || '' == $link ) return $link;
    
    if ( ( 'non-english' == $post->post_type) && ! in_array( $post->post_status, array('draft', 'pending', 'auto-draft'), true ) ) {

        $unixtime = strtotime($post->post_date);
        $date = explode(" ",date('Y m d H i s', $unixtime));
        $date =  $date[0].'/'.$date[1].'/'. $date[2];

        $languages = get_the_terms($post->ID, 'languages');
        if ( $languages ) {
            usort($languages, '_usort_terms_by_ID'); // order by ID
            $language = $languages[0]->slug;
        } else {
        	$language = 'no'; // in case the post doesn't have a language selected
        }
        $link = home_url() . '/' .$language. '/' . $date . '/'. $post->post_name;
     }
     return $link;
}

add_action( 'template_redirect', 'wmb_hide_language_switcher' );
function wmb_hide_language_switcher() {
	global $icl_language_switcher;
	if( ! is_single() ) {
		remove_filter( 'the_content', array(&$icl_language_switcher, 'post_availability'), 100 );
	}
}