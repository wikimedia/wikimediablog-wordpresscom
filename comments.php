<?php if( ! wmb_comments_restrict_access() ) return; ?>

<div class="comments" id="comments">
	<div class="comments-head">
		<h3><span><?php wmb_the_custom_comment_count(); ?> on</span> <?php the_title(); ?></h3>
	</div>

	<?php wmb_comments_render_list('wmb_render_comment');	?>

	<div class="form comment-form">
		<?php 
		if ( $post->post_type=='non-english' ) : 
			$english_post_id = get_post_meta($post->ID, 'english_post', true); ?>
			<div id="respond" class="comment-respond">
				<h3 id="reply-title" class="comment-reply-title"><a href="<?php echo esc_url( get_permalink( $english_post_id ) . $post->ID . '/' ); ?>#respond">Leave a Reply</a></h3>
			</div> 
		<?php else :
			$comment_form_args = array( 'title_reply'=>__('Leave a Reply') );

			if ( get_query_var( 'wmb_post_id' ) ) {
				$comment_form_args['comment_notes_after'] = '<input type="hidden" name="wmb_post_id" value="' . esc_attr( get_query_var( 'wmb_post_id' ) ) . '">';
			} else {
				$comment_form_args['comment_notes_after'] = '';
			}
			comment_form( $comment_form_args );
		endif; ?>
	</div>
</div>
<!-- /.comments -->