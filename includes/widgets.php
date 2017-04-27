<?php

// Initialize the registered custom widgets
add_action( 'widgets_init', 'wmb_register_widgets' );
function wmb_register_widgets() {
	// register the custom available widgets
	register_widget( 'WMB_Social_Links' );
	register_widget( 'WMB_Subscribe_Widget' );
	register_widget( 'WMB_Category_Posts' );
	register_widget( 'WMB_Most_Viewed_Posts' );
	register_widget( 'WMB_Custom_Quote' );
	register_widget( 'WMB_Archives' );
}

/*
* Social Links Widget
*/
class WMB_Social_Links extends WP_Widget {

	function __construct() {
		parent::__construct( 'WMB_Social_Links', 'WikiMedia - Social Links', array(
			'description' => 'Displays a widget with social links, managed from Appearance -> Options.',
			'classname' => 'widget_connect'
		) );
	}

	function form( $instance ) {
		$fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'label' => __( 'Title', 'wmb' ),
			),
		);
		wmb_widget_render_fields( $fields, $instance, $this );
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function widget( $args, $instance ) {
		// output the content of the widget
		$before_widget = $args['before_widget'];
		$after_widget = $args['after_widget'];

		echo wp_kses_post( $before_widget );

		if ( $instance['title'] ) {
			echo wp_kses_post( $args['before_title'] );
			echo esc_html( $instance['title'] );
			echo wp_kses_post( $args['after_title'] );
		}

		echo '<ul>';

		$options = get_option( 'wmb_options' );
		$socials = array(
			'facebook' => 'facebook',
			'instagram' => 'instagram',
			'twitter' => 'twitter',
			'linkedin' => 'linkedin',
			'rss' => 'rss',
		);

		foreach ($socials as $classname => $option_name) {
			$option_key = $option_name . '_link';
			if ( empty( $options[ 'social' ][ $option_key ] ) ) {
				continue;
			}
			?>
			<li>
				<a href="<?php echo esc_url( $options[ 'social' ][ $option_key ] ); ?>" target="_blank">
					<i class="fa fa-<?php echo esc_attr( $classname ); ?>"></i>
				</a>
			</li>
			<?php
		}

		echo '</ul>';

		echo wp_kses_post( $after_widget );
	}

}

/*
* Subscribe Widget
*/
class WMB_Subscribe_Widget extends WP_Widget {

	function __construct() {
		// (constructor) Instantiate the parent object
		parent::__construct( 'WMB_Subscribe_Widget', 'WikiMedia - Subscribe', array( 'description' => 'Subscribe to the blog via MailChimp' ) );
	}

	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
		}
		else {
			$title = __( 'Get Our E-mail Updates', 'wmb' );
		}
		?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e( 'Title:', 'wmb' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		// process widget options to be saved
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	function widget( $args, $instance ) {
		// output the content of the widget
		$before_title = $args['before_title'];
		$after_title = $args['after_title'];
		$before_widget = $args['before_widget'];
		$after_widget = $args['after_widget'];

		$title = apply_filters( 'widget_title', $instance['title'] );
		echo wp_kses_post( $before_widget );

		if ( !empty( $title ) ) {
			echo wp_kses_post( $before_title ) . esc_html( $title ) . wp_kses_post( $after_title );
		}

		?>

		<!-- Begin MailChimp Signup Form -->
		<link href="https://cdn-images.mailchimp.com/embedcode/classic-081711.css" rel="stylesheet" type="text/css">

		<div id="mc_embed_signup">
			<form action="https://wikimediafoundation.us11.list-manage.com/subscribe/post?u=7e010456c3e448b30d8703345&amp;id=246cd15c56" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
			    <div id="mc_embed_signup_scroll">
					<div class="mc-field-group email">
						<input type="email" placeholder="Your email address" name="EMAIL" class="email" id="mce-EMAIL">
					</div>

					<div class="clear"></div>

					<div class="mc-field-group input-group">
					    <ul>
					    	<li><input type="radio" value="1" name="group[4037]" id="mce-group[4037]-4037-0" checked="CHECKED"><label for="mce-group[4037]-4037-0">Daily update</label></li>
							<li><input type="radio" value="2" name="group[4037]" id="mce-group[4037]-4037-1"><label for="mce-group[4037]-4037-1">Weekly update</label></li>
						</ul>
					</div>

					<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button">

					<div id="mce-responses" class="clear">
						<div class="response" id="mce-error-response" style="display:none"></div>
						<div class="response" id="mce-success-response" style="display:none"></div>
					</div>
					<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
				    <div style="position: absolute; left: -5000px;"><input type="text" name="b_7e010456c3e448b30d8703345_246cd15c56" tabindex="-1" value=""></div>

			    </div>
			</form>
		</div>
		<script src='https://s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script>
		<script>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
		<script src='<?php echo esc_url( get_template_directory_uri() ); ?>/js/subscribe.js'></script>
		<!--End mc_embed_signup-->
		<?php
		echo wp_kses_post( $after_widget );
	}

}

/*
* Category Posts Widget
*/
class WMB_Category_Posts extends WP_Widget {

	function __construct() {
		parent::__construct('WMB_Category_Posts', 'WikiMedia - Category Posts', array(
			'description' => 'Displays a widget with most recent posts from a certain category.',
			'classname' => 'widget_community'
		));
	}

	function form( $instance ) {
		$fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'label' => __( 'Title', 'wmb' ),
			),
			array(
				'type' => 'text',
				'name' => 'posts_count',
				'label' => __( 'Posts count', 'wmb' ),
			),
			array(
				'type' => 'select',
				'name' => 'post_category',
				'label' => __( 'Category', 'wmb' ),
				'options' => get_terms( 'category', array(
					'hide_empty' => 0,
					'fields' => 'id=>name',
				) )
			),
			array(
				'type' => 'text',
				'name' => 'more_link_text',
				'label' => __( 'More Link Text', 'wmb' ),
			),
		);
		wmb_widget_render_fields( $fields, $instance, $this );
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function widget( $args, $instance ) {
		// output the content of the widget
		$before_widget = $args['before_widget'];
		$after_widget = $args['after_widget'];

		echo wp_kses_post( $before_widget );

		if ( $instance['title'] ) {
			echo wp_kses_post( $args['before_title'] );
			echo esc_html( $instance['title'] );
			echo wp_kses_post( $args['after_title'] );
		}

		$total_posts = !empty( $instance['posts_count'] ) ? absint( $instance['posts_count'] ) : 2;
		if ( ! $total_posts ) {
			$total_posts = 2;
		}
		$query_args = array(
			'post_type' => 'post',
			'posts_per_page' => $total_posts,
			'cat' => $instance['post_category'],
		);
		$posts = new WP_Query( $query_args );

		if ( $posts->have_posts() ) {
			?>
			<ul>
				<?php while( $posts->have_posts() ) : $posts->the_post(); ?>
					<li>
						<a href="<?php the_permalink(); ?>" class="image">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'category_posts_widget' ) ?>
							<?php else : ?>
								<span class="placeholder-image"></span>
							<?php endif ?>
						</a>

						<h5>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h5>
					</li>
				<?php endwhile; ?>
			</ul>

			<?php

			if ( !empty( $instance['more_link_text'] ) ) {
				?>
				<p class="link-more">
					<?php
						//VIP: always check return value before passing it to another function
						$term_link = wpcom_vip_get_term_link( (int) $instance['post_category'] );
						if ( is_wp_error( $term_link ) ) {
							$term_link = '';
						}
					?>
					<a href="<?php echo esc_url( $term_link ); ?>"><?php echo esc_html( $instance['more_link_text'] ); ?></a>
				</p>
				<?php
			}

			wp_reset_postdata();
		}

		echo wp_kses_post( $after_widget );
	}

}

/*
* Most Viewed Posts
*/
class WMB_Most_Viewed_Posts extends WP_Widget {

	function __construct() {
		parent::__construct('WMB_Most_Viewed_Posts', 'WikiMedia - Most Viewed Posts', array(
			'description' => 'Displays a widget with most viewed posts within a certain number of days.',
			'classname' => 'widget_most_viewed'
		));
	}

	function form( $instance ) {
		$fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'label' => __( 'Title', 'wmb' ),
			),
			array(
				'type' => 'text',
				'name' => 'posts_count',
				'label' => __( 'Posts count', 'wmb' ),
			),
			array(
				'type' => 'text',
				'name' => 'days_count',
				'label' => __( 'Number of days', 'wmb' ),
			),
			array(
				'type' => 'text',
				'name' => 'more_link_url',
				'label' => __( 'More Link URL', 'wmb' ),
			),
			array(
				'type' => 'text',
				'name' => 'more_link_text',
				'label' => __( 'More Link Text', 'wmb' ),
			),
		);
		wmb_widget_render_fields( $fields, $instance, $this );
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function widget( $args, $instance ) {
		// output the content of the widget
		$before_widget = $args['before_widget'];
		$after_widget = $args['after_widget'];

		echo wp_kses_post( $before_widget );

		if ( $instance['title'] ) {
			echo wp_kses_post( $args['before_title'] );
			echo esc_html( $instance['title'] );
			echo wp_kses_post( $args['after_title'] );
		}

		$total_days = !empty( $instance['days_count'] ) ? absint( $instance['days_count'] ) : 30;
		if ( ! $total_days ) {
			$total_days = 30;
		}

		$total_posts = !empty( $instance['posts_count'] ) ? absint( $instance['posts_count'] ) : 3;
		if ( ! $total_posts ) {
			$total_posts = 3;
		}

		$posts = wpcom_vip_top_posts_array( $total_days, $total_posts );

		if ( $posts ) {
			?>
			<ul>
				<?php foreach ( $posts as $p ): ?>
					<li>
						<h4>
							<a href="<?php echo esc_url( get_permalink( $p['post_id'] ) ); ?>"><?php echo esc_html( apply_filters( 'the_title', $p['post_title'] ) ); ?></a>
						</h4>

						<?php echo wp_kses_post( wp_trim_words( get_post_field( 'post_excerpt', $p['post_id'] ), 6, '...' ) ); ?>
					</li>
				<?php endforeach ?>
			</ul>

			<?php

			if ( !empty( $instance['more_link_text'] ) && !empty( $instance['more_link_url'] ) ) {
				?>
				<p class="link-more">
					<a href="<?php echo esc_url( $instance['more_link_url'] ); ?>"><?php echo esc_html( $instance['more_link_text'] ); ?></a>
				</p>
				<?php
			}

			wp_reset_postdata();
		}

		echo wp_kses_post( $after_widget );
	}

}

/*
* Custom Quote
*/
class WMB_Custom_Quote extends WP_Widget {

	function __construct() {
		parent::__construct('WMB_Custom_Quote', 'WikiMedia - Custom Quote', array(
			'description' => 'Displays a widget with a custom quote.',
			'classname' => 'widget_quote'
		));
	}

	function form( $instance ) {
		$fields = array(
			array(
				'type' => 'textarea',
				'name' => 'quote',
				'label' => __( 'Quote', 'wmb' ),
			),
			array(
				'type' => 'textarea',
				'name' => 'author',
				'label' => __( 'Author', 'wmb' ),
			),
		);
		wmb_widget_render_fields( $fields, $instance, $this );
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function widget( $args, $instance ) {
		// output the content of the widget
		$before_widget = $args['before_widget'];
		$after_widget = $args['after_widget'];

		echo wp_kses_post( $before_widget );

		?>
		<div class="box">
			<?php if ( ! empty( $instance['quote'] ) ): ?>
				<div class="entry">
					<?php echo wp_kses_post( apply_filters( 'the_content', $instance['quote'] ) ); ?>
				</div><!-- /.entry -->
			<?php endif ?>

			<?php if ( !empty( $instance['author'] ) ): ?>
				<p class="meta">
					<?php echo wp_kses_post( nl2br( $instance['author'] ) ); ?>
				</p>
			<?php endif ?>
		</div><!-- /.box -->
		<?php

		echo wp_kses_post( $after_widget );
	}

}

/*
* Custom Archives
*/
class WMB_Archives extends WP_Widget {

	function __construct() {
		parent::__construct('WMB_Archives', 'WikiMedia - Archives', array(
			'description' => 'Displays a widget with a custom archives.',
			'classname' => 'widget_archive'
		));
	}

	function form( $instance ) {
		$fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'label' => __( 'Title', 'wmb' ),
			),
		);
		wmb_widget_render_fields( $fields, $instance, $this );
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function widget( $args, $instance ) {
		// output the content of the widget
		$before_widget = $args['before_widget'];
		$after_widget = $args['after_widget'];

		echo wp_kses_post( $before_widget );

		if ( $instance['title'] ) {
			echo wp_kses_post( $args['before_title'] );
			echo esc_html( $instance['title'] );
			echo wp_kses_post( $args['after_title'] );
		}

		echo '<ul>';

		wp_get_archives( array(
			'type' => 'monthly',
			'limit' => 5,
			'show_post_count' => true,
		) );

		$total_posts = wp_count_posts();
		if ( ! empty( $total_posts->publish ) ) {
			$total_posts = $total_posts->publish;
		} else {
			$total_posts = '';
		}

		echo '<li class="older-posts"><a href="#">' . esc_html__( 'Older Posts', 'wmb' ) . '</a>&nbsp;(' . esc_html( $total_posts ) . ')</li>';

		wp_get_archives( array(
			'type' => 'yearly',
			'show_post_count' => true,
		) );

		echo '</ul>';

		echo wp_kses_post( $after_widget );
	}

}
