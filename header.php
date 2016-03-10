<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" id="viewport" />
	
	<link rel="shortcut icon" href="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/favicon.ico" />
	<link rel="pingback" href="<?php echo esc_attr( get_bloginfo( 'pingback_url' ) ); ?>" />

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

	<div class="wrapper">
		<header class="header">
			<div class="shell cf">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo notext"><?php echo esc_attr( get_bloginfo( 'name' ) ); ?></a>
				
				<a href="#" class="nav-toggle">
					<span></span>
				</a>
				
				<div class="wrap">
					<div class="header-search collapse">
						<?php get_search_form(); ?>
					</div><!-- /.header-search -->
					
					<?php if ( has_nav_menu( 'main-menu' ) ): ?>
						<nav class="nav">
							<?php
							wp_nav_menu( array(
								'theme_location' => 'main-menu',
								'fallback_cb' => '',
								'container' => '',
							) );
							?>
						</nav><!-- /.nav -->
					<?php endif; ?>
				</div><!-- /.wrap -->
			</div><!-- /.shell -->
		</header><!-- /.header -->