		<div class="footer">
			<div class="shell">
				<div class="footer-inner cf">
					<?php $options = get_option( 'wmb_options' ); ?>

					<?php if ( ! empty( $options['footer']['about_text'] ) || ! empty( $options['footer']['about_title'] ) ): ?>
						<div class="about-foundation">
							<?php if ( ! empty( $options['footer']['about_title'] ) ): ?>
								<h6><?php echo esc_html( apply_filters( 'the_title', $options['footer']['about_title'] ) ); ?></h6>
							<?php endif ?>

							<?php echo wp_kses_post( apply_filters( 'the_content', $options['footer']['about_text'] ) ); ?>
						</div><!-- /.about-foundation -->
					<?php endif ?>
					
					<?php foreach ( array( 'left', 'right' ) as $direction ): ?>
						<div class="column align<?php echo esc_attr( $direction ); ?>">
							<?php if ( 
								! empty( $options['footer'][ $direction . '_column_text' ] ) || 
								! empty( $options['footer'][ $direction . '_column_title' ] ) || 
								! empty( $options['footer'][ $direction . '_column_menu' ] ) 
							): ?>

								<?php if ( ! empty( $options['footer'][ $direction . '_column_title' ] ) ): ?>
									<h6><?php echo esc_html( apply_filters( 'the_title', $options['footer'][ $direction . '_column_title' ] ) ); ?></h6>
								<?php endif ?>

								<?php echo wp_kses_post( apply_filters( 'the_content', $options['footer'][ $direction . '_column_text' ] ) ); ?>

								<?php if ( ! empty( $options['footer'][ $direction . '_column_menu' ] ) ): ?>
									<?php wp_nav_menu( array(
										'menu' => $options['footer'][ $direction . '_column_menu' ],
										'fallback_cb' => '',
										'container' => '',
									) ); ?>
								<?php endif ?>

							<?php endif; ?>
						</div><!-- /.column -->						
					<?php endforeach ?>

					<?php if ( ! empty( $options['footer']['copyright_text'] ) ): ?>
						<div class="copyright">
							<?php echo wp_kses_post( apply_filters( 'the_content', $options['footer']['copyright_text'] ) ); ?>
						</div><!-- /.copyright -->
					<?php endif; ?>
				</div><!-- /.footer-inner -->
			</div><!-- /.shell -->
		</div><!-- /.footer -->
	</div><!-- /.wrapper -->

	<?php wp_footer(); ?>
</body>
</html>