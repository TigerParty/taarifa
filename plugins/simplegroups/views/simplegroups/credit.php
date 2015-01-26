<?php $group_page = url::site()."simplegroups/index/".$group_id; ?>
<?php echo html::script('media/js/groups.js'); ?>

	<br/>
	<br/>
	
		<div id="group<?php echo $group_id ?>">
		<h5 style="font-size:14px;border-top:1px dotted #c0c2b8;"><?php echo Kohana::lang('simplegroups.credit_group');?></h5>
		<?php echo Kohana::lang('simplegroups.created');?><a style="float:none;font-size:14px;" href="<?php echo $group_page; ?>"> <?php echo $group_name; ?></a>
		<span class="delete_button" onClick="removeGroup('<?php echo $group_id ?>', '<?php $id = explode('/', url::current()); echo $id[3] ?>'); return false;">Remove assignment</span>
		<?php
			$thumb = $logo_file."_t.jpg";
			$prefix = url::base().Kohana::config('upload.relative_directory')."/groups";
			print "<br/><a style=\"float:none;\" href='$group_page'>";
			print "<img src=\"$prefix/$thumb\" alt=\"\"\>";
			print "</a>";
		?>
		<h5 style="font-size:14px;border-top:1px dotted #c0c2b8;"><?php echo Kohana::lang('simplegroups.categories_report');?></h5>
			<p>
			<?php
				foreach($categories as $category) 
				{

					// don't show hidden categoies
					if($category->category_visible == 0)
					{
						continue;
					}

				  if ($category->category_image_thumb)
					{
					?>
					<a href="<?php echo url::site()."simplegroups/groupmap/".$group_id; ?>">
						<span class="r_cat-box" style="background:transparent url(<?php echo url::base().Kohana::config('upload.relative_directory')."/".$category->category_image_thumb; ?>) 0 0 no-repeat;">&nbsp;</span> <?php echo $category->category_title; ?>
					</a>
					
					<?php 
					}
					else
					{
					?>
						<a href="<?php echo url::site()."simplegroups/groupmap/".$group_id; ?>">
					  		<span class="r_cat-box" style="background-color:#<?php echo $category->category_color; ?>">&nbsp;</span> <?php echo $category->category_title; ?>
					  	</a>
				  <?php
				  }
				}
			?>
			</p>
			</div>
