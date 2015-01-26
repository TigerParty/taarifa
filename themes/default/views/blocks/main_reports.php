<?php blocks::open("reports");?>
<div style="border-bottom:1px solid #ccc;padding:15px 0 5px 10px;"><?php blocks::title(Kohana::lang('ui_main.reports_listed'));?></div>
<div style="padding:10px;">
<table class="table-list">
	<thead>
		<tr>
			<th scope="col" class="title"><span style="color:#ccc;"><?php echo Kohana::lang('ui_main.title'); ?></span></th>
			<th scope="col" class="location"><span style="color:#ccc;"><?php echo Kohana::lang('ui_main.location'); ?></span></th>
			<th scope="col" class="date"><span style="color:#ccc;"><?php echo Kohana::lang('ui_main.date'); ?></span></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ($total_items == 0)
		{
			?>
			<tr><td colspan="3"><?php echo Kohana::lang('ui_main.no_reports'); ?></td></tr>
			<?php
		}
		foreach ($incidents as $incident)
		{
			
			$incident_enable = $incident->enable;
			if($incident_enable == 1){
			
			
			
			$incident_id = $incident->id;
			$incident_title = text::limit_chars($incident->incident_title, 40, '...', True);
			$incident_date = $incident->incident_date;
			$incident_date = date('M j Y', strtotime($incident->incident_date));
			$incident_location = $incident->location->location_name;
		?>
		<tr>
			<td><a href="<?php echo url::site() . 'reports/view/' . $incident_id; ?>"> <?php echo $incident_title ?></a></td>
			<td><?php echo $incident_location ?></td>
			<td><?php echo $incident_date; ?></td>
		</tr>
		<?php
			}
		}
		?>
	</tbody>
</table>
<a class="more" href="<?php echo url::site() . 'reports/' ?>"><?php echo Kohana::lang('ui_main.view_more'); ?></a>
<div style="clear:both;"></div>
<?php blocks::close();?>
</div>