<?php 
get_header(); 
the_post();
?>

<?php get_template_part( 'templates/featured-image' ); ?>

<div class="main shell cf">
	<div class="content">
		<article class="article article-single">
			<header class="article-head">
				<p class="article-category"><?php the_category( ', ' ); ?></p>

				<?php the_title( '<h2 class="article-title">', '</h2>' ); ?>
				
				<div class="article-meta">
					<?php get_template_part( 'templates/post-meta' ); ?>
				</div><!-- /.article-meta -->

				<div class="article-description">
					<?php 
					remove_filter( 'the_excerpt', 'wmb_the_excerpt' );
					the_excerpt();
					add_filter( 'the_excerpt', 'wmb_the_excerpt' );
					?>
				</div><!-- /.article-description -->
				
			</header><!-- /.article-head -->
			
			<div class="article-body">
				<div class="article-entry">
					<?php 
					global $post; 
					wmb_language_block_for_single($post);
					
					the_content(); 
					?>
				</div><!-- /.article-entry -->
			</div><!-- /.article-body -->
			
			<footer class="article-foot">
				<?php comments_template(); ?>
			</footer><!-- /.article-foot -->
		</article><!-- /.article -->
	</div><!-- /.content -->

	<?php get_sidebar(); ?>
</div><!-- /.main -->

<?php get_footer();