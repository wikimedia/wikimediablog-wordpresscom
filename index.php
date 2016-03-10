<?php get_header(); ?>

	<?php 
	if ( is_home() ) {
		get_template_part( 'templates/featured-posts-slider' );
	}
	?>

	<div class="main shell cf">
		<div class="content">
			<?php wmb_the_title( '<h2 class="article-title main-title">', '</h2>' ); ?>

			<?php get_template_part( 'loop' ); ?>
		</div><!-- /.content -->

		<?php get_sidebar(); ?>
	</div><!-- /.main -->

<?php get_footer();