<?php
global $post;
$link = get_permalink();
$text = get_the_title() . ' - ' . $link;
$facebook_link = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $link );
$googleplus_link = 'https://plus.google.com/share?url=' . urlencode( $link );
$twitter_link = 'https://twitter.com/home?status=' . urlencode( html_entity_decode( $text, ENT_COMPAT, 'UTF-8' ) );
?>
<div class="share">
	<ul>
		<li class="label">
			<?php esc_attr_e( 'Share', 'wmb' ); ?>
		</li>

		<li>
			<a href="<?php echo esc_url( $facebook_link ); ?>" target="_blank">
				<i class="fa fa-facebook"></i>
			</a>
		</li>

		<li>
			<a href="<?php echo esc_url( $googleplus_link ); ?>" target="_blank">
				<i class="fa fa-google-plus"></i>
			</a>
		</li>

		<li>
			<a href="<?php echo esc_url( $twitter_link ); ?>" target="_blank">
				<i class="fa fa-twitter"></i>
			</a>
		</li>
	</ul>
</div><!-- /.share -->