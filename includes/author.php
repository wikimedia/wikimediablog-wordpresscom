<?php

# Display links to author, supports multiple authors
function wmb_author_links( $return = false ) {
	ob_start();

	if ( function_exists( 'coauthors_posts_links' ) ) {
		$between = '<br/>';
		coauthors_posts_links($between, $between);
	} else {
		the_author_link();
	}

	$html = ob_get_clean();
	$html = str_replace('%%%', '', $html);

	if ( $return ) {
		return $html;
	}

	echo wp_kses_post( $html );
}

# Change the author link
add_filter( 'get_the_author_user_url', 'wmb_get_the_author_url', 10, 2 );
function wmb_get_the_author_url( $url, $user_id ) {
	return get_author_posts_url( $user_id );
}

# Add the author type after each author name
add_filter( 'coauthors_posts_link', 'wmb_coauthors_posts_link', 10, 2 );
function wmb_coauthors_posts_link( $args, $author ) {
	if ( ! is_object( $author ) || ! ( is_single() && get_post_type() === 'post' ) ) {
		return $args;
	}

	if ( $author->description === 'staff' ) {
		$type = 'Wikimedia Foundation';
	} elseif ( $author->description !== '' ) {
		$type = wp_kses( $author->description, array() );
	} else {
		$type = '';
	}

	if ( $type ) {
		$args['after_html'] = ', ' . $type . ' %%%';
	}

	return $args;
}