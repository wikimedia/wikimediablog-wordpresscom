<?php

add_action( 'init', 'wmb_add_non_english_rewrite_rule' );
function wmb_add_non_english_rewrite_rule() {
	add_rewrite_rule( 
		'([^/]+)/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/?$', 
		'index.php?year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&name=$matches[5]&post_type=non-english', 
		'top'
	);
}

add_action( 'init', 'wmb_add_comment_post_id_rewrite_rule' );
function wmb_add_comment_post_id_rewrite_rule() {
	add_rewrite_rule( 
		'([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/([0-9]{1,7})/?$', 
		'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&wmb_post_id=$matches[5]', 
		'top'
	);
}