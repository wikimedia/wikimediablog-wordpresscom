<?php

# Comments per page
define('DEFAULT_COMMENTS_PER_PAGE', (int) get_option('comments_per_page'));

# Add additional fields to the comment edit screen
add_action( 'add_meta_boxes_comment', 'wmb_add_comments_fields' );
function wmb_add_comments_fields( $comment ) {
	$excerpt  = get_comment_meta( $comment->comment_ID, '_comment_excerpt', true );
	$featured = get_comment_meta( $comment->comment_ID, '_comment_featured', true ) == 'yes';
	?>
	<div class="postbox">
		<h3>Featured Comment</h3>
		<div class="inside">
			<table class="form-table">
				<tr>
					<th>Featured Comment?</th>
					<td>
						<label>
							<input type="checkbox" name="comment_featured" <?php checked( true, $featured ); ?> />
							Featured
						</label>
					</td>
				</tr>

				<tr>
					<th>Excerpt</th>
					<td>
						<?php wp_editor( $excerpt, 'comment_excerpt' ) ?>									
					</td>
				</tr>
			</table>
		</div>		
	</div>
	<?php
}

# Save comments data before redirect
add_filter( 'comment_edit_redirect', 'wmb_save_comment_fields' );
function wmb_save_comment_fields( $param ) {
	$id = absint( $_POST[ 'comment_ID' ] );
	if ( ! $id ) {
		return;
	}

	$featured = isset( $_POST[ 'comment_featured' ] ) ? 'yes' : 'no';
	$excerpt  = sanitize_text_field( $_POST[ 'comment_excerpt' ] );

	update_comment_meta( $id, '_comment_featured', $featured );
	update_comment_meta( $id, '_comment_excerpt', $excerpt );

	return $param;
}

# Renders a single comments; Called for each comment
function wmb_render_comment($comment, $args, $depth, $page) {
	$GLOBALS['comment'] = $comment;
	?>
	<div <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<div id="comment-<?php comment_ID(); ?>" class="<?php echo esc_attr($depth > 1 ? 'reply' : 'entry') ?>">
			<p><?php comment_author_link() ?> <a class="comment-link" href="<?php echo esc_url( get_comment_link( null, array('page'=> $page) ) ); ?>"><?php echo esc_html(human_time_diff( get_comment_time('U'), current_time('timestamp'))) ?></a></p>
			<?php comment_text() ?>

			<div class="tools">
				<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
				<a href="http://twitter.com/intent/tweet?text=<?php echo esc_attr(urlencode(get_comment_text())) ?>" target="_blank" class="share">Share</a>
			</div>
		</div>
	<?php
}

# Restricts direct access to the comments.php and checks whether the comments are password protected.
function wmb_comments_restrict_access() {
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) {
		echo '<p class="nocomments">This post is password protected. Enter the password to view comments.</p>';
		return false;
	}

	return true;
}

# End comment wrapper
function wmb_comments_end_comment() {
	echo '</div>';
}

# Renders all current comments
function wmb_comments_render_list( $callback ) {
	global $post; ?>

		<div class="commentList" id="commentList">
			
			<?php 
				$page = max(1, get_query_var('cpage'));
				$limit = DEFAULT_COMMENTS_PER_PAGE;
				$offset = ($page * $limit) - $limit;
				
				$post_id = wmb_get_english_post_id( get_the_ID() );

				$param = array(
					'post_id' => $post_id,
				    'status'=>'approve',
				    'type' => 'comment',
				    'offset'=>$offset,
				    'number'=>$limit,
				);
				$total_comments = get_comments(array('post_id' => $post_id, 'type' => 'comment', 'status'=>'approve'));
				$pages = ceil(count($total_comments)/DEFAULT_COMMENTS_PER_PAGE);
				$comments = get_comments($param);

				wmb_list_comments($comments, $page);
			?>
		</div>
		
		<?php if($pages > 1) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<input type="hidden" name="post_id" id="post_id" value="<?php echo esc_attr($post_id); ?>">
				<input type="hidden" name="offset" id="offset" value="<?php echo esc_attr($offset); ?>">
				<input type="hidden" name="pages" id="pages" value="<?php echo esc_attr($pages); ?>">
				<input type="hidden" name="page" id="page" value="<?php echo esc_attr($page); ?>">
				<div class="more" id="more">More Comments</div>
			</div>
		<?php endif; ?>
		<?php if ( comments_open() ) : ?>
			<!-- If comments are open, but there are no comments. -->
		<?php else : // comments are closed ?>
			<p class="nocomments">Comments are closed.</p>
		<?php endif; ?>
	<?php
}

# Comments ajax pagination
add_action('wp_ajax_get_more_comments', 'wmb_ajax_get_more_comments');
add_action('wp_ajax_nopriv_get_more_comments', 'wmb_ajax_get_more_comments');
function wmb_ajax_get_more_comments() {
	if ( ! isset( $_GET['post_id'] ) || ! isset( $_GET['offset'] ) ) {
		die;
	}

	$post_id = absint( $_GET['post_id'] );
	$offset = absint( $_GET['offset'] );
	if ( ! $post_id || ! $offset ) {
		die;
	}

	$offset += DEFAULT_COMMENTS_PER_PAGE;
	$param = array(
		'post_id' => $post_id,
		'status' => 'approve',
	    'type' => 'comment',
		'offset' => $offset,
		'number' => DEFAULT_COMMENTS_PER_PAGE,
	);
	$comments = get_comments($param);
	$page = ($offset / DEFAULT_COMMENTS_PER_PAGE) + 1;
	wmb_list_comments( $comments, $page );
	die;
}

# List a page of comments
function wmb_list_comments( $comments, $page ) {
	$args = array(
		'max_depth'=> 5,
		'respond_id'=> 'respond',
		'reply_text'=> 'Reply',
		'login_text'=> 'Log in to leave a comment',
	);
	foreach ( $comments as $comment ) {
		wmb_render_comment($comment, $args, '', $page);
		wmb_comments_end_comment();
	} 
}

# A modified version of get_page_of_comment()
function wmb_get_page_of_comment( $comment_ID ) {
	// This function is copied from get_page_of_comment in wp-includes/comment.php
	// It's modified to count comments newer than this one (rather than older) since we're showing the most recent comments first
	global $wpdb;

	if ( !$comment = get_comment( $comment_ID ) )
		return;

	$args = array( 'type' => 'comment', 'page' => '', 'per_page' => DEFAULT_COMMENTS_PER_PAGE, 'max_depth' => '' );


	if ( '' === $args['max_depth'] ) {
		if ( get_option('thread_comments') )
			$args['max_depth'] = get_option('thread_comments_depth');
		else
			$args['max_depth'] = -1;
	}

	// Find this comment's top level parent if threading is enabled
	if ( $args['max_depth'] > 1 && 0 != $comment->comment_parent )
		return wmb_get_page_of_comment( $comment->comment_parent, $args );

	// check for a cached result
	$comment_pages = wp_cache_get( $comment->comment_post_ID, 'comment_pages' );
	if ( $comment_pages ) {
		$comment_pages_array = json_decode( $comment_pages );
		if ( is_array( $comment_pages_array ) && isset( $comment_pages_array[$comment_ID] ) )
			return $comment_pages_array[$comment_ID];
	}

	$allowedtypes = array(
		'comment' => '',
		'pingback' => 'pingback',
		'trackback' => 'trackback',
	);

	$comtypewhere = ( 'all' != $args['type'] && isset($allowedtypes[$args['type']]) ) ? " AND comment_type = '" . $allowedtypes[$args['type']] . "'" : '';

	// Count comments NEWER than this one
	$newercoms = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_parent = 0 AND comment_approved = '1' AND comment_date_gmt > '%s'" . $comtypewhere, $comment->comment_post_ID, $comment->comment_date_gmt ) );

	// No newer comments? Then it's page #1.
	if ( 0 == $newercoms )
		return 1;

	// Divide comments older than this one by comments per page to get this comment's page number
	$page_of_comment = ceil( ( $newercoms + 1 ) / $args['per_page'] );

	// cache the result per post
	if ( $comment_pages ) {
		// cached array already exists for this comment's post, but this comment doesn't, so we'll add it and update the cache
		$comment_pages_array[$comment_ID] = (int) $page_of_comment;
		$data = json_encode( $comment_pages_array );
		wp_cache_set( $comment->comment_post_ID, $data, 'comment_pages' );
	} else {
		// cached array doesn't exist for this post so we'll create a new one
		$comment_pages_array = array( $comment_ID => (int) $page_of_comment );
		$data = json_encode( $comment_pages_array );
		wp_cache_add( $comment->comment_post_ID, $data, 'comment_pages' );
	}
	
	return $page_of_comment;
}

# Clear the comment pages cache
add_action( 'comment_post', 'wmb_clear_comment_pages_cache', 0 );
function wmb_clear_comment_pages_cache( $comment_ID ) {
	// clear the "comment_pages" cache for this comment's post when a new comment is added to that post
	$comment = get_comment( $comment_ID );
	if ( $comment ) {
		wp_cache_delete( $comment->comment_post_ID, 'comment_pages' );
	}
}

# Fetch the number of comments, supports translations
function wmb_custom_comment_count() {
	global $id;
	$id = wmb_get_english_post_id($id);
	$comments = get_approved_comments($id);
	$comment_count = 0;
	foreach($comments as $comment){
		if($comment->comment_type == ''){
			$comment_count++;
		}
	}
	return $comment_count;
}

# Display the number of comments, supports translations
function wmb_the_custom_comment_count() {
	$comment_count = wmb_custom_comment_count();
	$label = ($comment_count != 1) ? ' Comments' : ' Comment';
	echo esc_html($comment_count . $label);
}

add_filter( 'get_comments_number', 'wmb_comment_count', 0 );
function wmb_comment_count( $count ) {
	return wmb_custom_comment_count();
}

function wmb_comment_captcha_enabled() {
	if ( is_user_logged_in() ) {
		return false;
	}

	$options = get_option('wmb_options');
	$question = ! empty( $options['discussion']['captcha_question'] ) ? $options['discussion']['captcha_question'] : '';
	$answer = ! empty( $options['discussion']['captcha_answer'] ) ? $options['discussion']['captcha_answer'] : '';
	
	if ( ! $question || ! $answer ) {
		return false;
	}

	return true;
}

add_action( 'comment_form_after_fields', 'wmb_comments_captcha_field', 1 );
function wmb_comments_captcha_field() {
	if ( ! wmb_comment_captcha_enabled() ) {
		return;
	}

	$options = get_option('wmb_options');
	$question = ! empty( $options['discussion']['captcha_question'] ) ? $options['discussion']['captcha_question'] : '';
	$answer = ! empty( $options['discussion']['captcha_answer'] ) ? $options['discussion']['captcha_answer'] : '';
	?>
	<p class="captcha-field">
		<label for="wmb_captcha_field"><?php echo esc_html( $question ); ?></label> <input id="wmb_captcha_field" name="wmb_captcha_field" value="" size="30" type="text">
	</p>
	<?php
}

add_filter( 'preprocess_comment', 'wmb_comments_captcha_process' );
function wmb_comments_captcha_process($comment) {
	if ( ! wmb_comment_captcha_enabled() ) {
		return $comment;
	}

	$options = get_option('wmb_options');
	$question = ! empty( $options['discussion']['captcha_question'] ) ? $options['discussion']['captcha_question'] : '';
	$answer = ! empty( $options['discussion']['captcha_answer'] ) ? $options['discussion']['captcha_answer'] : '';

	if ( $_POST['wmb_captcha_field'] != $answer ) {
		$msg = '<strong>' . esc_html__('ERROR:', 'wmb') . ' </strong>';
		$msg .= esc_html__('You did not answer the question correctly.', 'wmb');
		$msg .= ' <a href="javascript:window.history.go(-1);">' . esc_html__('Go back', 'wmb') . '</a>.';
		wp_die( $msg );
	}

	return $comment;
}

add_action( 'comment_post', 'wmb_comment_post', 10);
function wmb_comment_post() {
	// if the user was replying from a non-english post, redirect them back to it
	if ( isset( $_POST['wmb_post_id'] ) ) {
		$link = get_permalink( (int)$_POST['wmb_post_id'] );
		if ( $link ) {
			wp_safe_redirect( $link . "#comments" );
			exit();
		}
	}
}