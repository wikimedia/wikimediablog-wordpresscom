<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="search-form">
	<label>
		<span class="screen-reader-text"><?php esc_attr_e( 'Search for:', 'wmb' ); ?></span>
		
		<input type="search" title="<?php esc_attr_e( 'Search for:', 'wmb' ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" class="search-field" />
	</label>
	
	<input type="submit" value="<?php esc_attr_e( 'Search', 'wmb' ); ?>" class="search-submit" />
</form><!-- /.search-form -->