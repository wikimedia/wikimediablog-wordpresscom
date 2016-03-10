<?php
$banner = get_post_meta( get_the_ID(), 'post_banner', 1 );
if ( ! $banner ) {
	echo '<div class="featured-image">';
	echo '<span class="image" style="background-image: url(' . esc_url( wmb_get_post_default_featured_image() ) . ');" ></span>';
	get_template_part( 'templates/share-post' );
	echo '</div>';
	return;
}

$size = ! empty( $banner['size'] ) ? $banner['size'] : 'full';
if ($size == 'half') {
	$size = 'post_half';
} else {
	$size = 'post';
}

?>
<div class="featured-image">
	<?php wmb_featured_image_with_background( $banner['image'], $size ); ?>

	<div class="container">
		<div class="shell">
			<?php get_template_part( 'templates/share-post' ); ?>

			<?php wmb_featured_image_credits( $banner['image'] ); ?>
		</div><!-- /.shell -->
	</div><!-- /.container -->
</div><!-- /.featured-image -->