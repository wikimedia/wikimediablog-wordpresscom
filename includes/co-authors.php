<?php 

# Remove the default meta box for adding a Co-Author
global $coauthors_plus;
remove_action( 'add_meta_boxes', array( $coauthors_plus, 'add_coauthors_box' ) );

# Remove the default handler for AJAX search
remove_action( 'wp_ajax_coauthors_ajax_suggest', array( $coauthors_plus, 'ajax_suggest' ) );

# Custom actions on admin init
add_action( 'admin_init', 'wmb_co_authors_admin_init' );
function wmb_co_authors_admin_init() {
	global $coauthors_plus;

	# Enqueue scripts and styles in the administration
	add_action( 'admin_enqueue_scripts', 'wmb_coauthors_enqueue_scripts' );
}

# Enqueue scripts and styles in the admin
function wmb_coauthors_enqueue_scripts() {
	global $pagenow, $post, $coauthors_plus;

	# Make sure we don't enqueue scripts if they're unnecessary
	if ( ! $coauthors_plus->is_valid_page() || ! $coauthors_plus->is_post_type_enabled() || ! $coauthors_plus->current_user_can_set_authors() ) {
		return;
	}

	# Dequeue and deregister the default main Co-Authors script
	wp_dequeue_script( 'co-authors-plus-js' );
	wp_deregister_script( 'co-authors-plus-js' );

	# Enqueue our own Co-Authors script
	wp_enqueue_script( 'co-authors-plus-js', get_bloginfo('stylesheet_directory') . '/js/co-authors-plus.js', array( 'jquery', 'suggest' ), COAUTHORS_PLUS_VERSION, true );

	# Localize some JS strings
	$js_strings = array(
		'edit_label' => __( 'Edit', 'co-authors-plus' ),
		'delete_label' => __( 'Remove', 'co-authors-plus' ),
		'confirm_delete' => __( 'Are you sure you want to remove this author?', 'co-authors-plus' ),
		'input_box_title' => __( 'Click to change this author, or drag to change their position', 'co-authors-plus' ),
		'search_box_text' => __( 'Search for an author', 'co-authors-plus' ),
		'help_text' => __( 'Click on an author to change them. Drag to change their order. Click on <strong>Remove</strong> to remove them.', 'co-authors-plus' ),
	);
	wp_localize_script( 'co-authors-plus-js', 'coAuthorsPlusStrings', $js_strings );

	# Additional Co-Authors styles
	wp_enqueue_style( 'co-authors-plus-additional', get_bloginfo('stylesheet_directory') . '/css/co-authors-plus.css' );
}

# Register our own custom Co-Authors meta box
add_action( 'add_meta_boxes', 'wmb_add_coauthors_box' );
function wmb_add_coauthors_box() {
	global $coauthors_plus;

	if ( $coauthors_plus->is_post_type_enabled() && $coauthors_plus->current_user_can_set_authors() ) {
		add_meta_box( $coauthors_plus->coauthors_meta_box_name, apply_filters( 'coauthors_meta_box_title', __( 'Authors', 'co-authors-plus' ) ), 'wmb_add_coauthors_box_content', get_post_type(), apply_filters( 'coauthors_meta_box_context', 'normal' ), apply_filters( 'coauthors_meta_box_priority', 'high' ) );
	}
}

# The content of the custom Co-Authors meta box
function wmb_add_coauthors_box_content( $post ) {
	global $post, $coauthors_plus, $current_screen;

	$post_id = $post->ID;

	$default_user = apply_filters( 'coauthors_default_author', wp_get_current_user() );

	# $post_id and $post->post_author are always set when a new post is created due to auto draft,
	# and the else case below was always able to properly assign users based on wp_posts.post_author,
	# but that's not possible with force_guest_authors = true.
	if ( ! $post_id || 0 === $post_id || ( ! $post->post_author && ! $coauthors_plus->force_guest_authors ) || ( 'post' === $current_screen->base && 'add' === $current_screen->action ) ) {
		$coauthors = array();

		# If guest authors is enabled, try to find a guest author attached to this user ID
		if ( $coauthors_plus->is_guest_authors_enabled() ) {
			$coauthor = $coauthors_plus->guest_authors->get_guest_author_by( 'linked_account', $default_user->user_login );
			if ( $coauthor ) {
				$coauthors[] = $coauthor;
			}
		}

		# If the above block was skipped, or if it failed to find a guest author, use the current
		# logged in user, so long as force_guest_authors is false. If force_guest_authors = true, we are
		# OK with having an empty authoring box.
		if ( ! $coauthors_plus->force_guest_authors && empty( $coauthors ) ) {
			if ( is_array( $default_user ) ) {
				$coauthors = $default_user;
			} else {
				$coauthors[] = $default_user;
			}
		}
	} else {
		$coauthors = get_coauthors();
	}

	$count = 0;
	if ( ! empty( $coauthors ) ) :
		?>
		<div id="coauthors-readonly" class="hide-if-js">
			<ul>
			<?php
			foreach ( $coauthors as $coauthor ) :
				$count++;
				?>
				<li>
					<?php echo get_avatar( $coauthor->user_email, $coauthors_plus->gravatar_size ); ?>
					<span id="<?php echo esc_attr( 'coauthor-readonly-' . $count ); ?>" class="coauthor-tag">
						<input type="text" name="coauthorsinput[]" readonly="readonly" value="<?php echo esc_attr( $coauthor->display_name ); ?>" />
						<input type="text" name="coauthors[]" value="<?php echo esc_attr( $coauthor->user_login ); ?>" />
						<input type="text" name="coauthorsemails[]" value="<?php echo esc_attr( $coauthor->user_email ); ?>" />
						<input type="text" name="coauthorsnicenames[]" value="<?php echo esc_attr( $coauthor->user_nicename ); ?>" />
						<input type="text" name="coauthorsaffiliations[]" value="<?php echo esc_attr( get_post_meta( $post_id, '_co_author_affiliation_' . $coauthor->ID, 1 ) ); ?>" />
					</span>
				</li>
				<?php
			endforeach;
			?>
			</ul>
			<div class="clear"></div>
			<p><?php wp_kses( __( '<strong>Note:</strong> To edit post authors, please enable javascript or use a javascript-capable browser', 'co-authors-plus' ), array( 'strong' => array() ) ); ?></p>
		</div>
		<?php
	endif;
	?>

	<div id="coauthors-edit" class="hide-if-no-js">
		<p><?php wp_kses( __( 'Click on an author to change them. Drag to change their order. Click on <strong>Remove</strong> to remove them.', 'co-authors-plus' ), array( 'strong' => array() ) ); ?></p>
	</div>

	<?php wp_nonce_field( 'coauthors-edit', 'coauthors-nonce' ); ?>

	<?php
}

# Main function that handles search-as-you-type for adding authors
add_action( 'wp_ajax_coauthors_ajax_suggest', 'wmb_coauthors_ajax_suggest' );
function wmb_coauthors_ajax_suggest() {
	global $coauthors_plus;

	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'coauthors-search' ) ) {
		die();
	}

	if ( empty( $_GET['q'] ) ) {
		die();
	}

	$search = sanitize_text_field( strtolower( $_GET['q'] ) );
	$ignore = array_map( 'sanitize_text_field', explode( ',', $_GET['existing_authors'] ) );

	$authors = $coauthors_plus->search_authors( $search, $ignore );
	parse_str( parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_QUERY ), $params );
	$post_id = isset( $params['post'] ) ? absint( $params['post'] ) : 0;

	foreach ( $authors as $author ) {
		$affiliation = get_post_meta( $post_id, '_co_author_affiliation_' . $author->ID, 1 );
		echo esc_html( $author->ID . ' | ' . $author->user_login . ' | ' . $author->display_name . ' | ' . $author->user_email . ' | ' . $author->user_nicename . ' | ' . $affiliation ) . "\n";
	}

	die();

}

# Save the affiliation for each Co-Author to this post
add_action( 'save_post', 'wmb_coauthors_update_post', 10, 2 );
function wmb_coauthors_update_post( $post_id, $post ) {
	global $coauthors_plus;

	$coauthors = ! empty( $_POST['coauthors'] ) ? $_POST['coauthors'] : array();
	$affiliations = ! empty( $_POST['coauthors_affiliations'] ) ? $_POST['coauthors_affiliations'] : array();
	foreach ( $affiliations as $key => $affiliation ) {
		$username = isset( $coauthors[ $key ] ) ? $coauthors[ $key ] : '';
		$user = $coauthors_plus->get_coauthor_by( 'user_nicename', $username );
		if ( ! $user ) {
			continue;
		}

		update_post_meta( $post_id, '_co_author_affiliation_' . $user->ID, $affiliation );
	}
}

# Render the affiliation on author links in the frontend
add_filter( 'coauthors_posts_link', 'wmb_coauthors_author_affiliation', 12, 2 );
function wmb_coauthors_author_affiliation( $args, $author ) {
	global $post;

	$affiliation = get_post_meta( $post->ID, '_co_author_affiliation_' . $author->ID, 1 );
	if ( strlen( $affiliation ) > 0 ) {
		$args['after_html'] .= ', ' . $affiliation;
	}

	return $args;
}