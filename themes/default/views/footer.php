			</div>
		</div>
		<!-- / main body -->

	</div>
	<!-- / wrapper -->
	</div>
	
	<!-- footer -->
	<div id="footer" class="clearingfix">
 
		<div id="underfooter"></div>
				
		<!-- footer content -->
		<div class="rapidxwpr floatholder">
 
			<!-- footer credits -->
			<div class="footer-credits">
				<!-- <a href="http://taarifa.org/"><img src="<?php echo url::file_loc('img'); ?>media/img/Taarifa_Logo.png" alt="Ushahidi" style="vertical-align:middle" /></a> -->
			</div>
			<!-- / footer credits -->
		
			<!-- footer menu -->
			<div class="footermenu">
				<ul class="clearingfix">
					<li><a class="item1" href="<?php echo url::site(); ?>"><?php echo Kohana::lang('ui_main.home'); ?></a></li>
					<li><a href="<?php echo url::site()."reports/submit"; ?>"><?php echo Kohana::lang('ui_main.submit'); ?></a></li>
					<?php
					if(Kohana::config('settings.allow_alerts'))
					{ 
					?>
						<li><a href="<?php echo url::site()."alerts"; ?>"><?php echo Kohana::lang('ui_main.alerts'); ?></a></li>
					<?php
					}
					?>
					<li><a href="<?php echo url::site()."contact"; ?>"><?php echo Kohana::lang('ui_main.contact'); ?></a></li>
					<li><a style="border-right:1px solid #000000" href="http://www.surveymonkey.com/s/SM7LWGB"><?php echo Kohana::lang('ui_main.feedback'); ?></a></li>
                    
					<?php
					// Action::nav_main_bottom - Add items to the bottom links
					Event::run('ushahidi_action.nav_main_bottom');
					?>
				</ul>
				<?php if($site_copyright_statement != '') { ?>
      		<p><?php echo $site_copyright_statement; ?></p>
      	<?php } ?>
			</div>
			<!-- / footer menu -->
 

 
		</div>
		<!-- / footer content -->
 
	</div>
	<!-- / footer -->
 
	<?php echo $ushahidi_stats; ?>
	<?php echo $google_analytics; ?>
	<?php echo $cdn_gradual_upgrade; ?>
	
	<!-- Task Scheduler --><script type="text/javascript">$(document).ready(function(){$('#schedulerholder').html('<img src="<?php echo url::base(); ?>scheduler" />');});</script><div id="schedulerholder"></div><!-- End Task Scheduler -->
 
	<?php
	// Action::main_footer - Add items before the </body> tag
	Event::run('ushahidi_action.main_footer');
	?>
</body>
</html>
