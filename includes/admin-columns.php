<?php

# Add a column that shows if a post is featured or not
add_filter( 'manage_posts_columns', 'wmb_posts_columns', 100 );
function wmb_posts_columns( $columns ) {
	$new_columns = array();
	foreach( $columns as $key => $value ) {
		$new_columns[ $key ] = $value;
		if( $key == 'title' ) {
			$new_columns[ 'featured' ] = __( 'Featured Post', 'wmb' );
		}
	}
	return $new_columns;
}

# Show values for the custom column above
add_action( 'manage_posts_custom_column', 'wmb_posts_column', 10, 2 );
function wmb_posts_column( $column, $post_id ) {
	if( $column == 'featured' ) {
		$featured = wmb_is_post_featured( $post_id ) ? 'yes' : 'no';
		echo esc_html(ucwords( $featured ));
	}
}