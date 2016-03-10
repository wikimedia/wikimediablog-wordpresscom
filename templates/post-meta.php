<p class="meta">
	By <?php wmb_author_links(); ?>
	
	<?php if ( ! is_single() ): ?>
		<span>|</span> 
	<?php else: ?>
		<br />
	<?php endif ?>
	 
	<?php the_time( 'F jS, Y' ); ?>
</p>