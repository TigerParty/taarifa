<!-- main body -->
<div id="main" class="clearingfix">
	<div id="mainmiddle" class="floatbox withright">

	<?php if($site_message != '') { ?>
		<div class="green-box">
			<h3><?php echo $site_message; ?></h3>
		</div>
	<?php } ?>

		<!-- right column -->
		<div id="right" class="clearingfix">
	
			<!-- category filters -->
			<div class="cat-filters clearingfix">
				<strong><?php echo Kohana::lang('ui_main.category_filter');?> <span style="font-size:100%">[<a href="javascript:toggleLayer('category_switch_link', 'category_switch')" id="category_switch_link"><?php echo Kohana::lang('ui_main.hide'); ?></a>]</span></strong>
			</div>
			
            <div style="width:289px;">
			<ul id="category_switch" class="category-filters">
				<li><div class="active" id="cat_0" ><a href="javascript:void(0)" id="squ_0" class="swatch" data="<?php echo "#".$default_map_all;?>" style="border:1px solid <?php echo "#".$default_map_all;?>"></a><span class="category-title"><?php echo Kohana::lang('ui_main.all_categories');?></span></div></li>
				<?php 
//TTP comment this is Category Tree
					foreach ($categories as $category => $category_info)
					{
						$category_title = $category_info[0];
						$category_color = $category_info[1];
						$category_image = '';
						$color_css = 'href="javascript:void(0)" class="swatch" id="squ_'. $category .'" style="border:1px solid #'.$category_color.'" data="#'.$category_color.'" ';
						if($category_info[2] != NULL) {
							$category_image = html::image(array(
								'src'=>$category_info[2],
								'style'=>'float:left;padding-right:5px;'
								));
							$color_css = '';
						}
						echo '<li><div id="cat_'. $category .'"><a href="javascript:void(0)" '.$color_css.' id="squ_'. $category .'" >'.$category_image.'</a><span class="category-title">'.$category_title.'</span>
									<a href="javascript:void(0)" id="down_'. $category .'" style="float:right"><img src="'.url::file_loc('img').'media/img/DownArrow.png" /></a></div>';
						// Get Children
						echo '<div class="hide" id="child_'. $category .'">';
                                   if( sizeof($category_info[3]) != 0)
                                   {
                                   		echo '<ul>';
                                        foreach ($category_info[3] as $child => $child_info)
                                        {
                                        	$style_px = 40;
                                            $child_title = $child_info[0];
                                            $child_color = $child_info[1];
                                            $child_image = '';
                                            $color_css = 'href="javascript:void(0)" class="swatch" style="border:1px solid #'.$child_color.'" data="#'.$child_color.'"';
                                            if($child_info[2] != NULL && file_exists(Kohana::config('upload.relative_directory').'/'.$child_info[2])) {
                                                $child_image = html::image(array(
                                                	'src'=>Kohana::config('upload.relative_directory').'/'.$child_info[2],
                                                	'style'=>'float:left;padding-right:5px;'
                                                ));
                                            	$color_css = '';
                                         	}

                                         	echo '<li><div id="cat_'. $child .'" style="padding-left:20px;"><a '.$color_css.' id="squ_'. $child .'">'.$child_image.'</a><span class="category-title">'.$child_title.'</span>
                                         			<a href="javascript:void(0)" id="down_'. $child .'" style="float:right"><img src="'.url::file_loc('img').'media/img/DownArrow.png" /></a></div>';
                                         	
                                         	echo '<div class="hide" id="child_'. $child .'">';
                                         	echo category::mainview_category($child,$style_px);
	                                        echo '</div></li>';
	                                    }
	                                    echo '</ul>';
                                    }
						echo '</div></li>';
					}
				  ?>
			</ul>
            </div>
			<!-- / category filters -->
			
			<?php
			if ($layers)
			{
				?>
				<!-- Layers (KML/KMZ) -->
				<div class="cat-filters clearingfix" style="margin-top:20px;">
					<strong><?php echo Kohana::lang('ui_main.layers_filter');?> <span>[<a href="javascript:toggleLayer('kml_switch_link', 'kml_switch')" id="kml_switch_link"><?php echo Kohana::lang('ui_main.hide'); ?></a>]</span></strong>
				</div>
				<ul id="kml_switch" class="category-filters">
					<?php
					foreach ($layers as $layer => $layer_info)
					{
						$layer_name = $layer_info[0];
						$layer_color = $layer_info[1];
						$layer_url = $layer_info[2];
						$layer_file = $layer_info[3];
						$layer_link = (!$layer_url) ?
							url::base().Kohana::config('upload.relative_directory').'/'.$layer_file :
							$layer_url;
						echo '<li><a href="#" id="layer_'. $layer .'"
						onclick="switchLayer(\''.$layer.'\',\''.$layer_link.'\',\''.$layer_color.'\'); return false;"><div class="swatch" style="background-color:#'.$layer_color.'"></div>
						<div>'.$layer_name.'</div></a></li>';
					}
					?>
				</ul>
				<!-- /Layers -->
				<?php
			}
			?>
			
			
			<?php
			if ($shares)
			{
				?>
				<!-- Layers (Other Ushahidi Layers) -->
				<div class="cat-filters clearingfix" style="margin-top:20px;">
					<strong><?php echo Kohana::lang('ui_main.other_ushahidi_instances');?> <span>[<a href="javascript:toggleLayer('sharing_switch_link', 'sharing_switch')" id="sharing_switch_link"><?php echo Kohana::lang('ui_main.hide'); ?></a>]</span></strong>
				</div>
				<ul id="sharing_switch" class="category-filters">
					<?php
					foreach ($shares as $share => $share_info)
					{
						$sharing_name = $share_info[0];
						$sharing_color = $share_info[1];
						echo '<li><a href="#" id="share_'. $share .'"><div class="swatch" style="background-color:#'.$sharing_color.'"></div>
						<div>'.$sharing_name.'</div></a></li>';
					}
					?>
				</ul>
				<!-- /Layers -->
				<?php
			}
			?>
			
			
			<br />
		
			<!-- additional content -->
			<?php
			if (Kohana::config('settings.allow_reports'))
			{
				?>
				<div class="additional-content">
                	<div id="report">
                        <div class="title"><h5 style="margin-bottom:0"><?php echo Kohana::lang('ui_main.how_to_report'); ?></h5></div>
                        <div class="content">
                        
                            <?php if (!empty($phone_array)) 
                            { ?><li><?php echo Kohana::lang('ui_main.report_option_1')." "; ?> <?php foreach ($phone_array as $phone) {
                                echo "<strong>". $phone ."</strong>";
                                if ($phone != end($phone_array)) {
                                    echo " or ";
                                }
                            } ?></li>
							
							<?php } ?>
                            
                            <?php if (!empty($report_email)) 
                            { ?><?php echo Kohana::lang('ui_main.report_option_2')." "; ?> <br/><a href="mailto:<?php echo $report_email?>"><?php echo $report_email?></a>
							<?php } ?>
                            
                            <?php if (!empty($twitter_hashtag_array)) 
                                        { ?><li><?php echo Kohana::lang('ui_main.report_option_3')." "; ?> <?php foreach ($twitter_hashtag_array as $twitter_hashtag) {
                            echo "<strong>". $twitter_hashtag ."</strong>";
                            if ($twitter_hashtag != end($twitter_hashtag_array)) {
                                echo " or ";
                            }
                            } ?></li><?php
                            } ?><br/><span class="middle"><img src="<?php echo url::file_loc('img'); ?>media/img/Or.png" style="margin:10px 0" border="0"></span><br/><a href="<?php echo url::site() . 'reports/submit/'; ?>"><?php echo Kohana::lang('ui_main.report_option_4'); ?></a>
                        
    				   </div>
                       </div>
                   </div>
			<?php } ?>
			<?php if ($allow_feed == 1): ?>
			<div class="holder">
				<div class="feed">
					<h2 style="color:#000;font-size:110%"><?php echo Kohana::lang('ui_main.alerts_rss'); ?></h2>
					<div class="holder">
						<div class="box">
							<a href="<?php echo url::site(); ?>feed/"><img src="<?php echo url::file_loc('img'); ?>media/img/icon-feed.png" style="vertical-align: middle;" border="0"></a>&nbsp;<strong style="font-size:11px;font-weight: bold;"><a href="<?php echo url::site(); ?>feed/"><?php echo url::site(); ?>feed/</a></strong>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>


			<!-- / additional content -->
			
			<?php
			// Action::main_sidebar - Add Items to the Entry Page Sidebar
			Event::run('ushahidi_action.main_sidebar');
			?>
	
		</div>
		<!-- / right column -->
	
		<!-- content column -->
		<div id="content" class="clearingfix">
			<div class="floatbox">

				<!-- filters -->
				<div class="filters clearingfix">
					<div style="float:left; width: 100%">
						<strong><?php echo Kohana::lang('ui_main.filters'); ?></strong>
						<ul>
							<li><a id="media_0" class="active" href="#"><span><?php echo Kohana::lang('ui_main.reports'); ?></span></a></li>
							<li><a id="media_4" href="#"><span><?php echo Kohana::lang('ui_main.news'); ?></span></a></li>
							<li><a id="media_1" href="#"><span><?php echo Kohana::lang('ui_main.pictures'); ?></span></a></li>
							<li><a id="media_2" href="#"><span><?php echo Kohana::lang('ui_main.video'); ?></span></a></li>
							<li><a id="media_0" href="#"><span><?php echo Kohana::lang('ui_main.all'); ?></span></a></li>
						</ul>
					</div>


					<?php
					// Action::main_filters - Add items to the main_filters
					Event::run('ushahidi_action.map_main_filters');
					?>
				</div>
				<!-- / filters -->

				<?php								
				// Map and Timeline Blocks
				echo $div_map;
//TTP comment this is Chart output
				echo $div_timeline;
				 			
				?>
			</div>
		</div>
		<!-- / content column -->
		
	</div>
</div>
<!-- / main body -->
<?echo $div_msg_table;?>
<!-- content -->
<div class="content-container">

	<!-- content blocks -->
	<div class="content-blocks clearingfix">
		<ul class="content-column">
			<?php blocks::render(); ?>
		</ul>
	</div>
	<!-- /content blocks -->

</div>
<!-- content -->