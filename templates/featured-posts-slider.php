<?php
add_filter( 'excerpt_length', 'wmb_excerpt_length_slider' );

$featured_posts = new WP_Query( array(
	'posts_per_page' => 1,
	'tax_query'      => array(
		array(
			'taxonomy' => 'category',
			'field'    => 'slug',
			'terms'    => 'featured'
		)
	)
) );

if ( ! $featured_posts->have_posts() ) {
	return;
}
?>
<div class="slider">
	<div class="slider-clip">
		<ul class="slides">

			<?php while ( $featured_posts->have_posts() ) : 
				$featured_posts->the_post(); 

				$data = get_post_meta( get_the_ID(), 'featured_post', 1 );
				if ( empty( $data['image'] ) ) {
					continue;
				}

				$size = ! empty( $data['size'] ) ? $data['size'] : 'full';
				?>

				<li class="slide">
					<?php wmb_featured_image_with_background( $data['image'], $size ); ?>

					<div class="entry">
						<p class="category"><?php wmb_the_post_category(); ?></p>

						<h3>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>

						<?php get_template_part( 'templates/post-meta' ); ?>
					</div><!-- /.entry -->

					<div class="container">
						<div class="shell">
							<?php get_template_part( 'templates/share-post' ); ?>
							
							<?php wmb_featured_image_credits( $data['image'] ); ?>
						</div><!-- /.shell -->
					</div><!-- /.container -->
				</li><!-- /.slide -->

			<?php endwhile; ?>

		</ul><!-- /.slides -->
	</div><!-- /.slider-clip -->
</div><!-- /.slider -->

<div class="slider-post-text shell cf">
	<?php 
	$i = 0;
	while ( $featured_posts->have_posts() ) : $featured_posts->the_post(); $i++; ?>
		<div class="text-container<?php echo $i == 1 ? ' visible' : ''; ?>">
			<?php the_excerpt(); ?>
		</div><!-- /.text-container -->
	<?php endwhile; ?>
</div>

<?php 
remove_filter( 'excerpt_length', 'wmb_excerpt_length_slider' ); 

wp_reset_postdata();