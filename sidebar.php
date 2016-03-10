<?php
$sidebar = wmb_custom_sidebar();
if ( ! $sidebar ) {
	$sidebar = 'default-sidebar';
}

if ( ! is_active_sidebar( $sidebar ) ) {
	return;
}
?>

<div class="sidebar">
	<ul class="widgets">
		<?php dynamic_sidebar( $sidebar ); ?>
	</ul>
</div><!-- /.sidebar -->