<div class="report_row">
	<h4>Constituency</h4>
	<?
	print form::open();
	// The 'standard' option will be the default selection
	print form::dropdown('constituency_id',$filters_list, 1);
	print form::submit('filter', 'Filter');
	print form::close();
	?>
</div>
