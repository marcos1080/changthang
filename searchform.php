<?php
/*
	Search form for Chang theme.
	Used by the Search widget.
*/
?>

<form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
	<div class="chang-search-wrapper border">
		<input type="search" class="search-field" placeholder="Search …" value="" name="s" title="Search for:" />
		<input type="image" class="search-submit" src="<?php echo get_template_directory_uri()."/widgets/search_widget/icons/search_black.png"; ?>" alt="Submit" />
	</div>
</form>