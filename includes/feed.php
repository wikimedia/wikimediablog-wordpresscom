<?php

/**
 * Co-authors in RSS and other feeds
 * /wp-includes/feed-rss2.php uses the_author(), so we selectively filter the_author value
 */
add_filter( 'the_author', 'wmb_coauthors_in_rss' );
function wmb_coauthors_in_rss( $the_author ) {
	if ( ! is_feed() || ! function_exists( 'coauthors' ) ) {
		return $the_author;
	}

	return coauthors( null, null, null, null, false );
}

/**
 * Custom RSS feeds for daily and weekly digest
 * to access the feeds use /feed/?show_custom_feed=daily_digest and /feed/?show_custom_feed=weekly_digest
 */

# Add custom variable which we will use to identify our feeds.
add_action( 'query_vars', 'wmb_add_custom_feed_vars' );
function wmb_add_custom_feed_vars( $query_vars ) {
    $query_vars[] = 'show_custom_feed';
    return $query_vars;
}

# Set default term for email_update taxonomy on publish
if ( current_user_can( 'edit_posts' ) ) {
	add_action( 'save_post', 'wmb_set_default_object_terms', 100, 2 );
}

function wmb_set_default_object_terms( $post_id, $post ) {
	if ( 'publish' === $post->post_status && $post->post_type === 'post' ) {
		$taxonomy = 'email_update';
		$default = 'show-in-email-updates';

		$term_objects = get_the_terms( $post_id, $taxonomy );
        if ( $term_objects && ! is_wp_error( $term_objects) ) {
                $terms = wp_list_pluck( $term_objects, 'term_id' );
        }

		if ( empty( $terms ) ) {
			wp_set_object_terms( $post_id, $default, $taxonomy );
		}
	}
}

# Recognize feeds and parameters and initialize actions accordingly.
add_filter( 'pre_get_posts', 'wmb_change_custom_feed' );
function wmb_change_custom_feed( $query ) {
    if ( $query->is_feed() && 'daily_digest' == $query->get( 'show_custom_feed' ) || 'weekly_digest' == $query->get( 'show_custom_feed' ) ) {
        add_filter( 'the_excerpt_rss', 'wmb_alter_rss_excerpt' );

		set_query_var('tax_query', array(
			array(
				'taxonomy' => 'email_update',
				'field' => 'slug',
				'terms' => array( 'show-in-email-updates' ),
			)
		));

 		if ( 'weekly_digest' == $query->get( 'show_custom_feed' ) ) {
 			set_query_var( 'orderby', array(
 				'menu_order' => 'ASC',
 				'date' => 'DESC'
 			));
 			add_filter( 'posts_where', 'wmb_posts_since_last_monday' );
        }
    }

    return $query;
}

/**
 * Custom excerpts for RSS feeds for Mailchimp.
 * We'll put the entire HTML into the excerpt field:
 * a) so we can have it all in one place (and not have to modify it within MailChimp), and
 * b) because MailChimp limits which fields we can pull in from RSS
 */
function wmb_alter_rss_excerpt( $excerpt ) {
	return wmb_display_email_post( $excerpt );
}

function wmb_display_email_post( $excerpt = '' ) {
	global $post;

	// replace excerpt with feature post content if entered
	$featured_post_content = get_post_meta( $post->ID, '_featured_post_content', true );
	if ( $featured_post_content ) {
		$excerpt = $featured_post_content;
	} elseif ( ! $excerpt ) {
		$excerpt = get_the_excerpt();
	}

	$output = '<div style="padding:30px 25px 15px;">';
		$output .= '<div><a style="color:#000; text-decoration:none; font-family:georgia, serif; font-size:20px;" href="' . esc_url( get_permalink( $post->ID ) ) . '">' . esc_html( get_the_title( $post->ID ) ) . '</a></div>';
		$output .= '<div>By ' . coauthors( null, null, null, null, false ) . '</div>';
	$output .= '</div>';

	if ( has_post_thumbnail( $post->ID ) ) {
		$thumbnail_id    = get_post_thumbnail_id( $post->ID );
		$thumbnail_image = get_post( $thumbnail_id );
		$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

		$output .= '<a href="' . esc_attr( get_permalink( $post->ID ) ) . '"><img width="570" src="' . esc_url( $thumbnail_src[0] ) . '"></a>';
		$output .= '<div style="padding:15px 25px 30px;">';
		$output .= '<p style="font-size:12px;"><i>' . strip_tags( wp_kses_post( $thumbnail_image->post_excerpt ) ) . "</i></p>\n";
	} else {
		$output .= '<div style="padding:15px 25px 30px;">';
	}

	$output .= '<p>' . nl2br( wp_kses_post( $excerpt ) ) . '</p>';

	$output .= '<a href="' . esc_url( get_permalink( $post->ID ) ) . '" style="color:#fff; display:inline-block; font-weight:500; font-size:16px; line-height:28px; width:auto; white-space:nowrap; min-height:28px;'
		. 'margin:12px 5px 30px 0;padding:0 22px; text-decoration:none; text-align:center; border:0; vertical-align:top; background-color:#36c;">'
		. '<span style="display:inline; font-family:arial, sans-serif; font-weight:500; font-style:normal; font-size:16px; line-height:28px; border:0; background-color:#36c;color:#fff;">'
		. 'View it on the blog</span></a></div>';

    return $output;
}

function wmb_email_digest() {
	global $post;
	$type = sanitize_text_field( $_GET['email_digest'] ); ?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="UTF-8">
	</head>
		<body>

			<table width="580" style="margin:0 auto; background:#c8ccd1; color:#000; font-family:arial, sans-serif; font-size:14px;" cellspacing="0" cellpadding="0">
				<tr>
					<td height="34">&nbsp;</td>
				</tr>
				<tr>
					<td style="background:#eaecf0;">
						<img src="https://wikimediablog.files.wordpress.com/2015/06/wikimedia.png" style="float:right; margin:10px 30px 0 0;" height="40" width="40">
						<div style="font-family:arial, sans-serif; font-size:16px; margin:10px 0 10px 15px;">
							<div><?php echo $type == 'weekly' ? 'Weekly' : 'Daily'; ?> update</div>
							<div style="margin-top:5px;"><b><?php echo $type == 'weekly' ? 'This week' : 'Today'; ?> on the Wikimedia blog:</b></div>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div style="margin:0 5px; background:#fff;">
							<?php
							$args = array();

							if ( $type == 'weekly' ) {
								// get posts from the last week (since Monday) that aren't checked "Don't show in email updates", and order by Order
								$args = array(
									'posts_per_page' => 15,
									'post_type' => 'post',
									'tax_query' => array(
										array(
											'taxonomy' => 'email_update',
											'field' => 'slug',
											'terms' => array( 'not-in-email-updates' ),
											'operator' => 'NOT IN'
										)
									),
									'orderby' => array(
						 				'menu_order' => 'ASC',
						 				'date' => 'DESC'
						 			)
								);

								add_filter( 'posts_where', 'wmb_posts_since_last_monday' );
							} elseif ( $type == 'daily' ) {
								$args = array(
									'posts_per_page' => 1
								);
							}

							$the_query = new WP_Query( $args );

							if ( $the_query->have_posts() ) {
								while ( $the_query->have_posts() ) {
									$the_query->the_post();
									echo wmb_display_email_post();
								}
							}

							?>
							<div style="padding:0 25px 15px;">
								<div style="font-size:18px; width:60%;">More stories on the
									<a style="color:#000; font-weight:bold; text-decoration:none;" href="https://blog.wikimedia.org">blog</a>.
								</div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td height="34">&nbsp;</td>
				</tr>
			</table>

		</body>
	</html>
<?php die;
}

if ( isset( $_GET['email_digest'] ) ) {
	if ( is_user_logged_in() ) {
		add_action( 'init', 'wmb_email_digest', 100 );
	} else {
		die( 'You must be logged in to access this.' );
	}
}
