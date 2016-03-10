<?php
/**
 * Get the main title, based on the current view.
 *
 * @return string The current title.
 */
function wmb_get_title() {
	$title = '';

	if ( is_home() || ( is_single() && get_post_type() == 'post' ) ) {
		$blog_page_id = get_option( 'page_for_posts' );
		if ( $blog_page_id && $blog_page = get_post( $blog_page_id ) ) {
			$title = $blog_page->post_title;
		}
	} elseif ( is_search() ) {
		$title = sprintf( __( 'Search Results', 'wmb' ), get_search_query() );
	} elseif ( is_category() ) {
		$title = sprintf( __( '%s', 'wmb' ), single_cat_title( '', false ) );
	} elseif ( is_tag() ) {
		$title = sprintf( __( '%s', 'wmb' ), single_tag_title( '', false ) );
	} elseif ( is_day() ) {
		$title = sprintf( __( '%s', 'wmb' ), get_the_time( 'F jS, Y' ) );
	} elseif ( is_month() ) {
		$title = sprintf( __( '%s', 'wmb' ), get_the_time( 'F, Y' ) );
	} elseif ( is_year() ) {
		$title = sprintf( __( '%s', 'wmb' ), get_the_time( 'Y' ) );
	} elseif ( is_author() ) {
		$title = sprintf( __( '%s', 'wmb' ), get_the_author() );
	} elseif ( is_archive() ) {
		$title = __( 'Archives', 'wmb' );
	} elseif ( is_404() ) {
		$title = __( 'Error 404 - Not Found', 'wmb' );
	} elseif ( is_page() ) {
		$title = get_the_title();
	}

	/**
	 * Filter the current main title
	 *
	 * @param string $title The current main title
	 */
	$title = apply_filters('wmb_get_title', $title);

	return $title;
}

/**
 * Display the current main title, based on the current view.
 *
 * @uses wmb_get_title()
 * 
 * @param string $before Optional HTML before the title
 * @param string $after  Optional HTML after the title
 */
function wmb_the_title( $before = '', $after = '' ) {

	/**
	 * Filter the HTML that is displayed before the title
	 *
	 * @param string $before The HTML that is displayed before the title
	 */
	$before = apply_filters('wmb_get_title_before', $before);

	/**
	 * Filter the HTML that is displayed after the title
	 *
	 * @param string $after The HTML that is displayed after the title
	 */
	$after = apply_filters('wmb_get_title_after', $after);

	/**
	 * Get the current main title
	 */
	$title = wmb_get_title();

	/**
	 * Filter the current main title before it is displayed
	 *
	 * @param string $title The current main title
	 */
	$title = apply_filters('wmb_the_title', $title);

	/**
	 * If we have a title, display it along with its wrappers
	 */
	if ($title) {
		echo wp_kses( $before . $title . $after, wp_kses_allowed_html( 'post' ) );
	}

}
