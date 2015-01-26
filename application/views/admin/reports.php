<?php
include("application/config/mysql_connect.php");
/**
 * Reports view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
			<div class="bg">
				<h2>
					<?php admin::reports_subtabs("view"); ?>
				</h2>
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<ul class="tabset">
						<li>
							<a href="?status=all" <?php if (!in_array($status, array('v', 't', 'f', 'd', 'e'))) echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.show_all');?></a>
						</li>
						<li><a href="?status=v" <?php if ($status == 'v') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.awaiting_verification');?></a></li>
						<li><a href="?status=t" <?php if ($status == 't') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.awaiting_triage');?></a></li>
						<li><a href="?status=f" <?php if ($status == 'f') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.awaiting_fix');?></a></li>
                        <li><a href="?status=d" <?php if ($status == 'd') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.dispute_resolution');?></a></li>
                        <li><a href="?status=e" <?php if ($status == 'e') echo "class=\"active\""; ?>><?php echo Kohana::lang('ui_main.finished');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<ul>
							<li><a href="#" onclick="reportAction('v','<?php echo strtoupper(Kohana::lang('ui_admin.verify_unverify')); ?>', '');">
								<?php echo Kohana::lang('ui_admin.verify_unverify');?></a>
							</li>
							<li><a href="#" onclick="reportAction('d','<?php echo strtoupper(Kohana::lang('ui_main.delete')); ?>', '');">
								<?php echo Kohana::lang('ui_main.delete');?></a>
							</li>
						</ul>
					</div>
				</div>
				<?php if ($form_error): ?>
					<!-- red-box -->
					<div class="red-box">
						<h3><?php echo Kohana::lang('ui_main.error');?></h3>
						<ul><?php echo Kohana::lang('ui_main.select_one');?></ul>
					</div>
				<?php endif; ?>
				
				<?php if ($form_saved): ?>
					<!-- green-box -->
					<div class="green-box" id="submitStatus">
						<h3><?php echo Kohana::lang('ui_main.reports');?> <?php echo $form_action; ?> <a href="#" id="hideMessage" class="hide">hide this message</a></h3>
					</div>
				<?php endif; ?>
				
				<!-- report-table -->
				
				<?php print form::open(NULL, array('id' => 'reportMain', 'name' => 'reportMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="incident_id[]" id="incident_single" value="">
					<div class="table-holder">
                    
<!--  ================================================= Distinct Admin and MemberTwo =================================================  -->
<!--  ================================================= Distinct Admin and MemberTwo =================================================  -->
<!--  ================================================= Distinct Admin and MemberTwo =================================================  -->
<!--  ================================================= Distinct Admin and MemberTwo =================================================  -->
     
             <?php 
			 		//use for distinct membertwo and admin
					$compareuse = 1;
					$strSqlCommandRole_Id = "SELECT `role_id` FROM `roles_users` WHERE `user_id` = ".$this->user->id;
					$resultRole_Id = mysql_query($strSqlCommandRole_Id);
					while($rowRole_Id = mysql_fetch_array($resultRole_Id)){
						if($rowRole_Id['role_id'] == 6){
							$compareuse = 0;
						}
					}
			  		if($compareuse == 1){
			  ?>    
              
<!--  ================================================= For Admin =================================================  -->
<!--  ================================================= For Admin =================================================  -->
<!--  ================================================= For Admin =================================================  -->
<!--  ================================================= For Admin =================================================  -->
							
                	<tbody>

							<?php if ($total_items == 0): ?>
								<tr>
									<td colspan="4" class="col">
										<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
									</td>
								</tr>
							<?php endif; ?>
							<?php
								$pre_group_id = "";
								foreach ($incidents as $incident)
								{
									$incident_id = $incident->incident_id;
									
									$strSqlCommand8 = "SELECT * FROM `incident` WHERE `id`= ".$incident_id." ORDER BY `ggroup_id` DESC";
									$result8 = mysql_query($strSqlCommand8);
									$row8 = mysql_fetch_array ($result8);

									$sqlGroupName = "SELECT * FROM GGroup WHERE `id`=".$row8['ggroup_id'];
									$resultGroupName = mysql_query($sqlGroupName);
									$rowGroupName = mysql_fetch_array ($resultGroupName);
									if(strcmp($row8['ggroup_id'],$pre_group_id)!=0){
										if(strcmp($row8['ggroup_id'],'0')!=0){
											echo "<table class='table'>";
											echo "<thead>";
												echo "<tr>";
													echo "<th class='col-1'><input id='checkallincidents' type='checkbox' class='check-box' onclick='CheckAll( this.id, 'incident_id[]' )' /></th>";
													echo "<th class='col-2' style='width:350px;'>"; echo Kohana::lang('ui_main.group_details'); echo $rowGroupName['GGroup_Name']; echo "</th>";
													
				                                	echo "<th class='col-4' style='width:100px;text-align:center;'>"; echo Kohana::lang('ui_admin.actions'); echo "</th>";
													echo "<th class='col-3' style='text-align:center;'>"; echo Kohana::lang('ui_admin.group_photo'); echo "</th>";
				                                	
												echo "</tr>";
											echo "</thead>";
										}
										else{
											echo "<table class='table'>";
											echo "<thead>";
												echo "<tr>";
													echo "<th class='col-1'><input id='checkallincidents' type='checkbox' class='check-box' onclick='CheckAll( this.id, 'incident_id[]' )' /></th>";
													echo "<th class='col-2' style='width:350px;'>"; echo Kohana::lang('ui_main.group_details'); echo "No Group"; echo "</th>";
													
				                                	echo "<th class='col-4' style='width:100px;text-align:center;'>"; echo Kohana::lang('ui_admin.actions'); echo "</th>";
													echo "<th class='col-3' style='text-align:center;'>"; echo Kohana::lang('ui_admin.group_photo'); echo "</th>";
				                                	
												echo "</tr>";
											echo "</thead>";
										}
									}
									$pre_group_id = $row8['ggroup_id'];

									$incident_title = strip_tags($incident->incident_title);
									$incident_description = text::limit_chars(strip_tags($incident->incident_description), 150, "...", true);
									$incident_date = $incident->incident_date;
									$incident_date = date('Y-m-d', strtotime($incident->incident_date));
									
									// Mode of submission... WEB/SMS/EMAIL?
									$incident_mode = $incident->incident_mode;
									
									// Get the incident ORM
									$incident_orm = ORM::factory('incident', $incident_id);
									
									// Get the person submitting the report
									$incident_person = $incident_orm->incident_person;
									
									//XXX incident_Mode will be discontinued in favour of $service_id
									if ($incident_mode == 1)	// Submitted via WEB
									{
										$submit_mode = "WEB";
										// Who submitted the report?
										if ($incident_person->loaded)
										{
											// Report was submitted by a visitor
											$submit_by = $incident_person->person_first . " " . $incident_person->person_last;
										}
										else
										{
											if ($incident_orm->user_id)					// Report Was Submitted By Administrator
											{
												$submit_by = $incident_orm->user->name;
											}
											else
											{
												$submit_by = 'Unknown';
											}
										}
									}
									elseif ($incident_mode == 2) 	// Submitted via SMS
									{
										$submit_mode = "SMS";
										$submit_by = $incident_orm->message->message_from;
									}
									elseif ($incident_mode == 3) 	// Submitted via Email
									{
										$submit_mode = "EMAIL";
										$submit_by = $incident_orm->message->message_from;
									}
									elseif ($incident_mode == 4) 	// Submitted via Twitter
									{
										$submit_mode = "TWITTER";
										$submit_by = $incident_orm->message->message_from;
									}
									
									// Get the country name
									$country_name = ($incident->country_id != 0)
										? $countries[$incident->country_id] 
										: $countries[Kohana::config('settings.default_country')]; 
									
							
									// Retrieve Incident Categories
									$incident_category = "";
									foreach($incident_orm->incident_category as $category)
									{
										$incident_category .= "<a href=\"#\">" . $category->category->category_title . "</a>&nbsp;&nbsp;";
									}

									// Incident Status
									$incident_approved = $incident->incident_active;
									$incident_verified = $incident->incident_verified;
									
									// Get Edit Log
									$edit_count = $incident_orm->verify->count();
									$edit_css = ($edit_count == 0) ? "post-edit-log-gray" : "post-edit-log-blue";
									
									$edit_log  = "<div class=\"".$edit_css."\">"
										. "<a href=\"javascript:showLog('edit_log_".$incident_id."')\">".Kohana::lang('ui_admin.edit_log').":</a> (".$edit_count.")</div>"
										. "<div id=\"edit_log_".$incident_id."\" class=\"post-edit-log\"><ul>";
									
									foreach ($incident_orm->verify as $verify)
									{
										$edit_log .= "<li>".Kohana::lang('ui_admin.edited_by')." ".$verify->user->name." : ".$verify->verified_date."</li>";
									}
									$edit_log .= "</ul></div>";

									// Get Any Translations
									$i = 1;
									$incident_translation  = "<div class=\"post-trans-new\">"
											. "<a href=\"" . url::base() . 'admin/reports/translate/?iid='.$incident_id."\">"
											. strtoupper(Kohana::lang('ui_main.add_translation')).":</a></div>";
											
									foreach ($incident_orm->incident_lang as $translation)
									{
										$incident_translation .= "<div class=\"post-trans\">"
											. Kohana::lang('ui_main.translation'). $i . ": "
											. "<a href=\"" . url::base() . 'admin/reports/translate/'. $translation->id .'/?iid=' . $incident_id . "\">"
											. text::limit_chars($translation->incident_title, 150, "...", TRUE)
											. "</a>"
											. "</div>";
									}
									?>
									<tr>
										<td class="col-1">
											<input name="incident_id[]" id="incident" value="<?php echo $incident_id; ?>" type="checkbox" class="check-box"/>
										</td>
										<td class="col-2">
											<div class="post">
												<h4>
													<a href="<?php echo url::site() . 'admin/reports/edit/' . $incident_id; ?>" class="more">
														<?php echo $incident_title; ?>
													</a><p style="float:right"><?php echo $incident_date; ?></p>
												</h4>
												<p><?php echo $incident_description; ?>... 
													<a href="<?php echo url::base() . 'admin/reports/edit/' . $incident_id; ?>" class="more">
														<?php echo Kohana::lang('ui_main.more');?>
													</a>
												</p>
											</div>
											<ul class="info">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.location');?>: 
													<strong><?php echo $incident->location_name; ?></strong>, <strong><?php echo $country_name; ?></strong>
												</li>
												<li><?php echo Kohana::lang('ui_main.submitted_by');?> 
													<strong><?php echo $submit_by; ?></strong> via <strong><?php echo $submit_mode; ?></strong>
												</li>
											</ul>
											<ul class="links">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.categories');?>:<?php echo $incident_category; ?></li>
											</ul>
											<?php
											echo $edit_log;
											
											// Action::report_extra_admin - Add items to the report list in admin
											Event::run('ushahidi_action.report_extra_admin', $incident);
											?>
										</td>
                                        
										<td class="col-4">
											<ul>
												<li class="none-separator"><a href="#"<?php if ($incident_verified) echo " class=\"status_yes\"" ?> onclick="reportAction('v','VERIFY', '<?php echo $incident_id; ?>');"><?php echo Kohana::lang('ui_main.verify');?></a></li>
												<li><a href="#" class="del" onclick="reportAction('d','DELETE', '<?php echo $incident_id; ?>');"><?php echo Kohana::lang('ui_main.delete');?></a></li>
												
											</ul>
											<ul>
											  <li class="none-separator" style="width:140px; text-align: right"><h4>
												    <?php // Horrible hack to display status in 5 minutes
												      if($incident->incident_verified == 0) echo "Awaiting verification";
												      else if($incident->incident_status == 2 || $incident->incident_verified == 1) echo "Awaiting triage";
												      else if($incident->incident_status == 3) echo "Awaiting fix";
												      else if($incident->incident_status == 4) echo "Dispute resolution";
												    ?></h4>
												   <h4><a class='send_email' href="#" incident_id="<?php echo $incident_id;?>" onclick="setInfoToForm('<?php echo $incident_id;?>')">Send Email</a></h4>
												   Send reminders every
												  <select onchange="ajax_change_mail_send(this.value,'<?php echo $incident_id;?>')">
												   <?php	
												   	$query = "select every from mail_info where incident_id='".$incident_id."';";
												   	
													$mails= mysql_query($query );
												   	$row=@mysql_fetch_array($mails);
												   	$none="";
												   	$Day="";
												   	$Week="";
												   	$Month="";
												   	 var_dump($row['every']);
												   	 switch ($row['every']){
												   	 case '0':
												   	 	$none='selected';
												   	 break;
												   	 
												   	 case '1':
												   	 	$Day='selected';
												   	 break;
												   	 	
												   	 case '2':
												   	 	$Week="selected";
												   	 break;
												   	 
												   	 case '3':
												   	 	$Month="selected";
												   	 break;
												   	 }
												   ?>
													
													  <option value="0" <?php echo $none;?>>None</option>
													  <option value="1" <?php echo $Day;?>>Day</option>
													  <option value="2" <?php echo $Week;?>>Week</option>
													  <option value="3" <?php echo $Month;?>>Month</option>
													</select>
                                                    
                                               <?php 
														$strSqlCommand_choose_email_send = "SELECT email_send FROM incident WHERE id =".$incident_id;
														$SQLResult = mysql_query($strSqlCommand_choose_email_send);
														$ResultEach = mysql_fetch_array ($SQLResult);
														if($ResultEach['email_send'] == 0){
													?>
                                               	<div id="email_sent" style="color:red;display:none;">Email Send</div>
											   		<?php }else{?>
                                                  	<div id="email_sent" style="color:red;display:block;">Email Send</div>
                                              	<?php }?>

											</ul>
										</td>
                                        
                                    
                                    
                                    <td class="col-3" style="text-align:center;border:1px;background-color:silver;border-style:solid;border-color:silver;border-bottom-color:lightgray;border-bottom-width:2px;">
                                       <?php 
												$strSqlCommand9 = "SELECT * FROM `media` WHERE `incident_id`= ".$incident_id;
												$result9 = mysql_query($strSqlCommand9);
												$num_rows = mysql_num_rows(mysql_query($strSqlCommand9));
												
												if($num_rows > 0){
													$count=1;
													while($row9 = mysql_fetch_array ($result9)){
														if($count==1){
	                        								echo "<a class='photothumb' rel='lightbox-group".$incident_id."' href='http://".$_SERVER["SERVER_NAME"]."/media/uploads/".$row9['media_link']."'>";
	                        								echo "<img style='width:100%;width:103px\9;height:106px;' src='http://".$_SERVER["SERVER_NAME"]."/media/uploads/".$row9['media_link']."'/>";
	                        								echo "</a>";
															//echo "<br>";
														}
														else{
															echo "<a class='photothumb' rel='lightbox-group".$incident_id."' href='http://".$_SERVER["SERVER_NAME"]."/media/uploads/".$row9['media_link']."'>";
	                        								echo "<img style='display:none; src='http://".$_SERVER["SERVER_NAME"]."/media/uploads/".$row9['media_link']."'/>";
	                        								echo "</a>";
															//echo "<br>";
														}
														$count++;
													}
												}
												else{
													echo 'No Uploaded Photos';
												}
												?>	
														
                                    </td> 
									</tr>
									<?php
									}
								?>
							</tbody>
							<tfoot>
								<tr class="foot">
									<td colspan="4">
										<?php echo $pagination; ?>
									</td>
								</tr>
							</tfoot>
              <?php
                     echo "</table>";
					   echo "<br>";
					 	}
							//use for distinct membertwo and admin
							if($compareuse==0){
					   ?>
                       
<!--  ================================================= For MemberTwo =================================================  -->
<!--  ================================================= For MemberTwo =================================================  -->
<!--  ================================================= For MemberTwo =================================================  -->
<!--  ================================================= For MemberTwo =================================================  -->

                       		<?php 
					$strSqlCommandIncident = "SELECT `GGroup`.`GGroup_Name`,`GGroup`.`id` FROM `incident`,`GGroup` WHERE `incident`.`user_id`= ".$this->user->id." AND `incident`.`ggroup_id` = `GGroup`.`id` Group by `id`";
					$resultIncident = mysql_query($strSqlCommandIncident);
					while ($row18 = mysql_fetch_array ($resultIncident)) {
						
						
						echo "<table class='table'>";
							echo "<thead>";
								echo "<tr>";
									echo "<th class='col-1'><input id='checkallincidents' type='checkbox' class='check-box' onclick='CheckAll( this.id, 'incident_id[]' )' /></th>";
									echo "<th class='col-2' style='width:350px;'>"; echo Kohana::lang('ui_main.group_details'); echo " ".$row18['GGroup_Name']; echo "</th>";
									
                                	echo "<th class='col-4' style='width:100px;text-align:center;'>"; echo Kohana::lang('ui_admin.actions'); echo "</th>";
									echo "<th class='col-3' style='text-align:center;'>"; echo Kohana::lang('ui_admin.group_photo'); echo "</th>";
                                	
								echo "</tr>";
							echo "</thead>";
							
				?>
                	<tbody>
							<?php if ($total_items == 0): ?>
								<tr>
									<td colspan="4" class="col">
										<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
									</td>
								</tr>
							<?php endif; ?>
							<?php
								foreach ($incidents as $incident)
								{
									$incident_id = $incident->incident_id;
									
									//add user_id to distinct user power
									$strSqlCommand8 = "SELECT `ggroup_id`,`user_id` FROM `incident` WHERE `id`= ".$incident_id;
									$result8 = mysql_query($strSqlCommand8);
									$row8 = mysql_fetch_array ($result8);
									
									//add user_id to distinct user power
									if($row8['ggroup_id']==$row18['id'] && $row8['user_id'] == $this->user->id){
										
									$incident_title = strip_tags($incident->incident_title);
									$incident_description = text::limit_chars(strip_tags($incident->incident_description), 150, "...", true);
									$incident_date = $incident->incident_date;
									$incident_date = date('Y-m-d', strtotime($incident->incident_date));
									
									// Mode of submission... WEB/SMS/EMAIL?
									$incident_mode = $incident->incident_mode;
									
									// Get the incident ORM
									$incident_orm = ORM::factory('incident', $incident_id);
									
									// Get the person submitting the report
									$incident_person = $incident_orm->incident_person;
									
									//XXX incident_Mode will be discontinued in favour of $service_id
									if ($incident_mode == 1)	// Submitted via WEB
									{
										$submit_mode = "WEB";
										// Who submitted the report?
										if ($incident_person->loaded)
										{
											// Report was submitted by a visitor
											$submit_by = $incident_person->person_first . " " . $incident_person->person_last;
										}
										else
										{
											if ($incident_orm->user_id)					// Report Was Submitted By Administrator
											{
												$submit_by = $incident_orm->user->name;
											}
											else
											{
												$submit_by = 'Unknown';
											}
										}
									}
									elseif ($incident_mode == 2) 	// Submitted via SMS
									{
										$submit_mode = "SMS";
										$submit_by = $incident_orm->message->message_from;
									}
									elseif ($incident_mode == 3) 	// Submitted via Email
									{
										$submit_mode = "EMAIL";
										$submit_by = $incident_orm->message->message_from;
									}
									elseif ($incident_mode == 4) 	// Submitted via Twitter
									{
										$submit_mode = "TWITTER";
										$submit_by = $incident_orm->message->message_from;
									}
									
									// Get the country name
									$country_name = ($incident->country_id != 0)
										? $countries[$incident->country_id] 
										: $countries[Kohana::config('settings.default_country')]; 
									
							
									// Retrieve Incident Categories
									$incident_category = "";
									foreach($incident_orm->incident_category as $category)
									{
										$incident_category .= "<a href=\"#\">" . $category->category->category_title . "</a>&nbsp;&nbsp;";
									}

									// Incident Status
									$incident_approved = $incident->incident_active;
									$incident_verified = $incident->incident_verified;
									
									// Get Edit Log
									$edit_count = $incident_orm->verify->count();
									$edit_css = ($edit_count == 0) ? "post-edit-log-gray" : "post-edit-log-blue";
									
									$edit_log  = "<div class=\"".$edit_css."\">"
										. "<a href=\"javascript:showLog('edit_log_".$incident_id."')\">".Kohana::lang('ui_admin.edit_log').":</a> (".$edit_count.")</div>"
										. "<div id=\"edit_log_".$incident_id."\" class=\"post-edit-log\"><ul>";
									
									foreach ($incident_orm->verify as $verify)
									{
										$edit_log .= "<li>".Kohana::lang('ui_admin.edited_by')." ".$verify->user->name." : ".$verify->verified_date."</li>";
									}
									$edit_log .= "</ul></div>";

									// Get Any Translations
									$i = 1;
									$incident_translation  = "<div class=\"post-trans-new\">"
											. "<a href=\"" . url::base() . 'admin/reports/translate/?iid='.$incident_id."\">"
											. strtoupper(Kohana::lang('ui_main.add_translation')).":</a></div>";
											
									foreach ($incident_orm->incident_lang as $translation)
									{
										$incident_translation .= "<div class=\"post-trans\">"
											. Kohana::lang('ui_main.translation'). $i . ": "
											. "<a href=\"" . url::base() . 'admin/reports/translate/'. $translation->id .'/?iid=' . $incident_id . "\">"
											. text::limit_chars($translation->incident_title, 150, "...", TRUE)
											. "</a>"
											. "</div>";
									}
									?>
									<tr>
										<td class="col-1">
											<input name="incident_id[]" id="incident" value="<?php echo $incident_id; ?>" type="checkbox" class="check-box"/>
										</td>
										<td class="col-2">
											<div class="post">
												<h4>
													<a href="<?php echo url::site() . 'admin/reports/edit/' . $incident_id; ?>" class="more">
														<?php echo $incident_title; ?>
													</a><p style="float:right"><?php echo $incident_date; ?></p>
												</h4>
												<p><?php echo $incident_description; ?>... 
													<a href="<?php echo url::base() . 'admin/reports/edit/' . $incident_id; ?>" class="more">
														<?php echo Kohana::lang('ui_main.more');?>
													</a>
												</p>
											</div>
											<ul class="info">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.location');?>: 
													<strong><?php echo $incident->location_name; ?></strong>, <strong><?php echo $country_name; ?></strong>
												</li>
												<li><?php echo Kohana::lang('ui_main.submitted_by');?> 
													<strong><?php echo $submit_by; ?></strong> via <strong><?php echo $submit_mode; ?></strong>
												</li>
											</ul>
											<ul class="links">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.categories');?>:<?php echo $incident_category; ?></li>
											</ul>
											<?php
											echo $edit_log;
											
											// Action::report_extra_admin - Add items to the report list in admin
											Event::run('ushahidi_action.report_extra_admin', $incident);
											?>
										</td>
                                        
										<td class="col-4">
											<ul>
												<li class="none-separator"><a href="#"<?php if ($incident_verified) echo " class=\"status_yes\"" ?> onclick="reportAction('v','VERIFY', '<?php echo $incident_id; ?>');"><?php echo Kohana::lang('ui_main.verify');?></a></li>
												<li><a href="#" class="del" onclick="reportAction('d','DELETE', '<?php echo $incident_id; ?>');"><?php echo Kohana::lang('ui_main.delete');?></a></li>
												
											</ul>
											<ul>
											  <li class="none-separator" style="width:140px; text-align: right"><h4>
												    <?php // Horrible hack to display status in 5 minutes
												      if($incident->incident_verified == 0) echo "Awaiting verification";
												      else if($incident->incident_status == 2 || $incident->incident_verified == 1) echo "Awaiting triage";
												      else if($incident->incident_status == 3) echo "Awaiting fix";
												      else if($incident->incident_status == 4) echo "Dispute resolution";
												    ?></h4>
												   <h4><a class='send_email' href="#" incident_id="<?php echo $incident_id;?>" onclick="setInfoToForm('<?php echo $incident_id;?>')">Send Email</a></h4>
												   Send reminders every
												  <select onchange="ajax_change_mail_send(this.value,'<?php echo $incident_id;?>')">
												   <?php	
												   	$query = "select every from mail_info where incident_id='".$incident_id."';";
												   	
													$mails= mysql_query($query );
												   	$row=@mysql_fetch_array($mails);
												   	$none="";
												   	$Day="";
												   	$Week="";
												   	$Month="";
												   	 var_dump($row['every']);
												   	 switch ($row['every']){
												   	 case '0':
												   	 	$none='selected';
												   	 break;
												   	 
												   	 case '1':
												   	 	$Day='selected';
												   	 break;
												   	 	
												   	 case '2':
												   	 	$Week="selected";
												   	 break;
												   	 
												   	 case '3':
												   	 	$Month="selected";
												   	 break;
												   	 }
												   ?>
													
													  <option value="0" <?php echo $none;?>>None</option>
													  <option value="1" <?php echo $Day;?>>Day</option>
													  <option value="2" <?php echo $Week;?>>Week</option>
													  <option value="3" <?php echo $Month;?>>Month</option>
													</select>
                                               
                                               <?php 
														$strSqlCommand_choose_email_send = "SELECT email_send FROM incident WHERE id =".$incident_id;
														$SQLResult = mysql_query($strSqlCommand_choose_email_send);
														$ResultEach = mysql_fetch_array ($SQLResult);
														if($ResultEach['email_send'] == 0){
													?>
                                               	<div id="email_sent" style="color:red;display:none;">Email Send</div>
											   		<?php }else{?>
                                                  	<div id="email_sent" style="color:red;display:block;">Email Send</div>
                                              	<?php }?>

											</ul>
										</td>
                                        
                                    
                                    
                                    <td class="col-3" style="text-align:center;border:1px;background-color:silver;border-style:solid;border-color:silver;border-bottom-color:lightgray;border-bottom-width:2px;">
                                       <?php 
												$strSqlCommand9 = "SELECT * FROM `media` WHERE `incident_id`= ".$incident_id;
												$result9 = mysql_query($strSqlCommand9);
												$num_rows = mysql_num_rows(mysql_query($strSqlCommand9));
												
												if($num_rows > 0){
													$count=1;
													while($row9 = mysql_fetch_array ($result9)){
														if($count==1){
	                        								echo "<a class='photothumb' rel='lightbox-group".$incident_id."' href='http://".$_SERVER["SERVER_NAME"]."/media/uploads/".$row9['media_link']."'>";
	                        								echo "<img style='width:100%;width:103px\9;height:106px;' src='http://".$_SERVER["SERVER_NAME"]."/media/uploads/".$row9['media_link']."'/>";
	                        								echo "</a>";
															//echo "<br>";
														}
														else{
															echo "<a class='photothumb' rel='lightbox-group".$incident_id."' href='http://".$_SERVER["SERVER_NAME"]."/media/uploads/".$row9['media_link']."'>";
	                        								echo "<img style='display:none; src='http://".$_SERVER["SERVER_NAME"]."/media/uploads/".$row9['media_link']."'/>";
	                        								echo "</a>";
															//echo "<br>";
														}
														$count++;
													}
												}
												else{
													echo 'No Uploaded Photos';
												}
												?>	
														
                                    </td> 
									</tr>
									<?php
									}}
								?>
							</tbody>
              <?php
                     echo "</table>";
					   echo "<br>";
						}
					}
					   ?>
					</div>
				<?php print form::close(); ?>
				</div>
				<!--  ========================= mail form ============================ -->
								
				<div id='email_form' >
					
					<table style="text-align:cnter;width:300px;margin-left:auto; margin-right:auto;" border="0" >
						
						<tr>
						<td>
							
							
							<h1 style="color: rgb(0, 105, 155);">New Email</h1>
							<br>
							<br>
						</td>
						</tr>
						<tr>
						<td>
							<span>From:</span ><br>
							<span style="color:gray;">admin@ghdistrictsmonitor.org</span >
							<br>
							<br>
						</td>
						</tr>
						<tr>
						<td>
							<span>To:</span><br>
							<input id="mail_to" size="45px" type="text"></input>
							<br>
							<br>
						</td>
						</tr>
						<tr>
						<td>
							Subject:<br>
							
							<input id="mail_sub" size="45px" type="text"></input>
							<br>
							<br>
						</td>
						</tr>
						<tr>
						<td>
							<span>Message:</span><br>
							<textarea id="mail_msg" rows="4" cols="35px"></textarea >
						</td>
						</tr>
						<tr>
						<td style="text-align:right;">
						<br>
						<br>
						<br>    <div style="width:280px">
							<button id="form_submit" style="background-color:#eccfff"  onclick="ajax_add_new_email_info()" >SEND</button>&nbsp;<button id='block_close' style="background-color:#ffe3ff">CANCEL</button>
							<br>
							<br>
							<br>
							</div>
						</td>
						</tr>
					</table>
					
					<input id="check_has_email_input" type="hidden"></input>
					<input id="form_now_id" type="hidden" />
				</div>
				
<script type="text/javascript" src="/media/js/jquery.blockUI.js"></script>  			
<script>
$(document).ready(function() { 
$('#email_form').hide();

    
   $('#block_close').click(
   	function(){
   		 $.unblockUI(); 
   
   	}	
   
   ); 
   
   
    
}); 
function ajax_change_mail_send(val,id){
	ajax_hasEmailInfo(id);
	//alert(id);
	var check=$('#check_has_email_input').val();
	if(check=="0"){
			
		//alert('Please add info first.');
	
	}else{
		ajax_update_every(val,id);
		
	}
}

function ajax_hasEmailInfo(id){
	
	$.ajax({
		  type: "GET",
		  url: "/Tphp/mail_act/check_has_info.php",
		  data: {
			  incident_id:id
			  
			  },
		  success: function(data){
		  	var check="1";
		  	if(data==0){
		  	 check="0";	
		  	}
		  	$('#check_has_email_input').val(check);
		  	  
		  },
		  dataType: 'html',
		   async:false
		  
		  
		});

	
}

function setInfoToForm(id){
		ajax_hasEmailInfo(id);
		var check;
		$.unblockUI();   
		check=$('#check_has_email_input').val();
			
			$("#form_now_id").val(id);
			
			if(check=="0"){
					//alert("This report is no info in database,plz set it.");
					var val;
					val=$('#title_'+id).text();
					$('#mail_sub').val($.trim(val));
                    $('#mail_msg').val("Please view the report here<br> http://<?php echo $_SERVER["SERVER_NAME"]; ?>/index.php/reports/view/"+id);
					 blockUI();
					 //$('#form_submit').html("SEND");
					 $('#mail_to').val("");
					
			}else{
				//alert("You already saved Email info.");
				 blockUI();
				 //$('#form_submit').html("EDIT");
				  ajax_get_email_info();
				
			}
		
		
	}
	
	
	
function ajax_get_email_info(){
	var id=$("#form_now_id").val();
	
	$.ajax({
		  type: "GET",
		  url: "/Tphp/mail_act/get_mail_info.php",
		  data: {
			  incident_id:id
			  
			  },
		  success: function(data){
		   	$("#mail_to").val(data.mail_to);
			$("#mail_msg").val(data.msg);
			$("#mail_sub").val(data.sub);
		  },
		  dataType: 'json',
		  async:false
		  
		});


}	
	
//edit and add new one	
function ajax_add_new_email_info(){
	var id=$("#form_now_id").val();
	var mail_to=$("#mail_to").val();
	var msg=$("#mail_msg").val();
	var sub=$("#mail_sub").val();
	var type=$("#check_has_email_input").val();

	
	$.ajax({
		  type: "GET",
		  url: "/Tphp/mail_act/insert_and_edit_mail_info.php",
		  data: {
			  incident_id:id,
			  mail_to:mail_to,
			  msg:msg,
			  sub:sub,
			  type:type
			  },
		  success: function(data){
		  alert(data);
		  $.unblockUI();
		  window.location.reload();
		  },
		  dataType: 'html',
		  async:false
		  
		});


}

function ajax_update_every(val,id){
	
	$.ajax({
		  type: "GET",
		  url: "/Tphp/mail_act/update_mail_info.php",
		  data: {
			  incident_id:id,
			  every:val
			  
			  },
		  success: function(data){
		  	//alert(data);
		  },
		  dataType: 'html',
		  async:false
		  
		});

}





function blockUI(){
 $.blockUI({ 
        message: $('#email_form'),
        css: { 
		       
		        width:          '80%', 
		        top:            '23%', 
		        left:           '10%', 
		        cursor:         null,
		        border:         '10px solid #aaa' 
		        
		        
		    }
	}); 
}	
/*
function ValidEmail(emailtoCheck)
{
	
	var regExp = /^[^@^\s]+@[^\.@^\s]+(\.[^\.@^\s]+)+$/;
	if ( emailtoCheck.match(regExp) )
		return true;
	else
		return false;
}
*/
</script>			
