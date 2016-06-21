<?php if ( have_posts() ) : ?>

	<ol class="articles" id="articles">
		
		<?php while ( have_posts() ): the_post(); ?>
			<?php
			if ( is_home() && is_main_query() && in_category( 'featured' ) ) {
				continue;
			}
			?>
			<li class="article">
				<header class="article-head">
					<p class="article-category"><?php wmb_the_post_category(); ?></p>

					<h4 class="article-title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h4><!-- /.article-title -->
				</header><!-- /.article-head -->
				
				<?php if ( has_post_thumbnail() ): ?>
					<div class="article-image">
						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'blog_listing' ); ?>
						</a>
					</div><!-- /.article-image -->
				<?php endif ?>
				
				<?php if ( $post->post_excerpt ): ?>
					<div class="article-body">
						<div class="article-entry">
							<?php the_excerpt(); ?>
						</div><!-- /.article-entry -->
					</div><!-- /.article-body -->
				<?php endif ?>
				
				<footer class="article-foot">
					<div class="article-meta">
						<p>
							By <?php wmb_author_links(); ?>
							<br />
							<?php the_time( 'F jS, Y' ); ?>

							<?php if ( comments_open() && get_comments_number() ): ?>
								| <?php comments_popup_link( '0 Comments', '1 Comment', '% Comments' ); ?>
							<?php endif ?>
						</p>
					</div><!-- /.article-meta -->
				</footer><!-- /.article-foot -->
			</li><!-- /.article -->
		<?php endwhile; ?>

	</ol>

<?php else : ?>

	<p class="tc">
		<?php if ( is_category() ) { // If this is a category archive
			echo esc_html( sprintf( __( "Sorry, but there aren't any posts in the %s category yet.", 'wmb' ), esc_html( single_cat_title( '', false ) ) ) );
		} else if ( is_date() ) { // If this is a date archive
			esc_attr_e( "Sorry, but there aren't any posts with this date.", 'wmb' );
		} else if ( is_author() ) { // If this is a category archive
			$userdata = get_user_by( 'id', get_queried_object_id() );
			echo esc_html( sprintf( __( "Sorry, but there aren't any posts by %s yet.", 'wmb' ), esc_html( $userdata->display_name ) ) );
		} else if ( is_search() ) { // If this is a search
			esc_attr_e( 'No posts found. Try a different search?', 'wmb' );
		} else {
			esc_attr_e( 'No posts found.', 'wmb' );
		} ?>
	</p>

<?php endif;