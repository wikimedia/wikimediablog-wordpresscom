<?php
/**
 * Truncates a string to a certain word count.
 * @param  string  $input Text to be shortalized. Any HTML will be stripped.
 * @param  integer $words_limit number of words to return
 * @param  string $end the suffix of the shortalized text
 * @return string
 */
function wmb_shortalize($input, $words_limit = 15, $end = '...') {
	$input = strip_tags($input);
	$words_limit = abs(intval($words_limit));

	if ($words_limit == 0) {
		return $input;
	}

	$words = str_word_count($input, 2, '0123456789');
	if (count($words) <= $words_limit + 1) {
		return $input;
	}
	
	$loop_counter = 0;
	foreach ($words as $word_position => $word) {
		$loop_counter++;
		if ($loop_counter==$words_limit + 1) {
			return substr($input, 0, $word_position) . $end;
		}
	}
}

/**
 * Shortcut for get_post_meta. 
 * @param  string $key 
 * @param  integer $id required if the function is not called in loop context
 * @return string custom field if it exist
 */
function wmb_get_meta($key, $id = null) {
	if (!isset($id)) {
		global $post;
		if (empty($post->ID)) {
			return null;
		}
		$id = $post->ID;
	}
	return get_post_meta($id, $key, true);
}

/**
 * Retrieve a variable from either POST or GET
 * @param  string $key the name of the requested parameter
 * @return the requested parameter value
 */
function wmb_request_param($key = '') {
	$value = false;
	if (!$key) {
		return $value;
	}

	if ( isset($_POST[$key]) ) {
		$value = sanitize_text_field( $_POST[$key] );
	} elseif ( isset($_GET[$key]) ) {
		$value = sanitize_text_field( $_GET[$key] );
	}

	return $value;
}
