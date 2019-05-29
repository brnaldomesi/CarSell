<?php /* Static Name: Search */ ?>

<!-- BEGIN SEARCH FORM -->
<?php if ( of_get_option('g_search_box_id') == 'yes') { ?>
	<div class="search-form search-form__h clearfix">
		<form id="search-header" class="navbar-form pull-right" method="get" action="<?php echo home_url(); ?>/" accept-charset="utf-8">
			<input type="text" name="s" placeholder="" class="search-form_it">
			<input type="submit" value="Search" id="search-form_is" class="search-form_is">
		</form>
	</div>
<?php } ?>
<!-- END SEARCH FORM -->