<?php 

# Modify user capabilities on init
add_action( 'after_switch_theme', 'wmb_modify_author_caps' );
function wmb_modify_author_caps() {
	wpcom_vip_remove_role_caps( 'author', array( 'publish_posts' ) );
	wpcom_vip_add_role_caps( 'author', array( 'edit_others_posts' ) );
}

# Returns currently logged in user's ID or NULL if the user is not logged in
function wmb_is_logged_in() {
	get_currentuserinfo();
	global $user_ID;
	return $user_ID;
}


# Returns the currently logged in user's object
function wmb_get_current_user() {
	global $userdata;
	get_currentuserinfo();
	return $userdata;
}


# Redirects if the current user is not logged in. Be careful with the $redirect -
# may cause infinite redirection loop if the redirect requires login as well
function wmb_require_login($redirect = '') {
	if (!wmb_is_logged_in()) {
		$redirect = ($redirect) ? $redirect : home_url('/');
		wp_safe_redirect( $redirect );
		exit;
	}
}
