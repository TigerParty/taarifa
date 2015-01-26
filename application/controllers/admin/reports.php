<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Reports Controller.
 * This controller will take care of adding and editing reports in the Admin section.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Admin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

include "application/config/mysql_connect.php";
include "locationcode.php";

class Reports_Controller extends Admin_Controller {
	public function __construct()
	{
		parent::__construct();

		$this->template->this_page = 'reports';
	}


	/**
	 * Lists the reports.
	 *
	 * @param int $page
	 */
	public function index($page = 1)
	{
		// If user doesn't have access, redirect to dashboard
		if ( ! admin::permissions($this->user, "reports_view"))
		{
			url::redirect(url::site().'admin/dashboard');
		}

		$this->template->content = new View('admin/reports');
		$this->template->content->title = Kohana::lang('ui_admin.reports');
		
		// To 
		$params = array('all_reports' => TRUE);

		if (isset($_GET['status']))
		{
            $status = strtolower($_GET['status']);
            switch ($status) {
              case 'v':
                array_push($params, 'i.incident_verified = 0');
                break;
              case 't':
                array_push($params, 'i.incident_verified = 1 AND i.incident_status < 3');
                break;
              case 'f':
                array_push($params, 'i.incident_status = 3');
                break;
              case 'd':
                array_push($params, 'i.incident_status = 4');
                break;
              case 'e':
                array_push($params, 'i.incident_status = 5');
                break;
              case 'all':
                //$status = 'all';
                //array_push($params, 'i.incident_status <> 5');
                break;
            }
		} 
        else $status = 'all';
		
		// Get Search Keywords (If Any)
		if (isset($_GET['k']))
		{
			//	Brute force input sanitization
			
			// Phase 1 - Strip the search string of all non-word characters 
			$keyword_raw = (isset($_GET['k']))? preg_replace('#/\w+/#', '', $_GET['k']) : "";
			
			// Strip any HTML tags that may have been missed in Phase 1
			$keyword_raw = strip_tags($keyword_raw);
			
			// Phase 3 - Invoke Kohana's XSS cleaning mechanism just incase an outlier wasn't caught
			// in the first 2 steps
			$keyword_raw = $this->input->xss_clean($keyword_raw);
			
			$filter = " AND (".$this->_get_searchstring($keyword_raw).")";
		}
		else
		{
			$keyword_raw = "";
		}

		// Check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		
		if ($_POST)
		{
			$post = Validation::factory($_POST);

			 //	Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('incident_id.*','required','numeric');

			if ($post->validate())
			{
				// Approve Action
				if ($post->action == 'a')		
				{
					foreach($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						if ($update->loaded == TRUE) 
						{
							$update->incident_active =($update->incident_active == 0) ? '1' : '0'; 

							// Tag this as a report that needs to be sent out as an alert
							if ($update->incident_alert_status != '2')
							{ 
								// 2 = report that has had an alert sent
								$update->incident_alert_status = '1';
							}

							$update->save();

							$verify = new Verify_Model();
							$verify->incident_id = $item;
							$verify->verified_status = '1';
							
							// Record 'Verified By' Action
							$verify->user_id = $_SESSION['auth_user']->id;			
							$verify->verified_date = date("Y-m-d H:i:s",time());
							$verify->save();

							// Action::report_approve - Approve a Report
							Event::run('ushahidi_action.report_approve', $update);
						}
					}
					$form_action = strtoupper(Kohana::lang('ui_admin.approved'));
				}
				// Unapprove Action
				elseif ($post->action == 'u')	
				{
					foreach ($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						if ($update->loaded == TRUE)
						{
							$update->incident_active = '0';

							// If Alert hasn't been sent yet, disable it
							if ($update->incident_alert_status == '1')
							{
								$update->incident_alert_status = '0';
							}

							$update->save();

							$verify = new Verify_Model();
							$verify->incident_id = $item;
							$verify->verified_status = '0';
							
							// Record 'Verified By' Action
							$verify->user_id = $_SESSION['auth_user']->id;			
							$verify->verified_date = date("Y-m-d H:i:s",time());
							$verify->save();

							// Action::report_unapprove - Unapprove a Report
							Event::run('ushahidi_action.report_unapprove', $update);
						}
					}
					$form_action = strtoupper(Kohana::lang('ui_admin.unapproved'));
				}
				// Verify Action
				elseif ($post->action == 'v')	
				{
					foreach ($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						$verify = new Verify_Model();
						if ($update->loaded == TRUE)
						{
							if ($update->incident_verified == '1')
							{
								$update->incident_verified = '0';
								$verify->verified_status = '0';
							}
							else
							{
								$update->incident_verified = '1';
								$verify->verified_status = '2';
							}
							$update->save();

							$verify->incident_id = $item;
							// Record 'Verified By' Action
							$verify->user_id = $_SESSION['auth_user']->id;			
							$verify->verified_date = date("Y-m-d H:i:s",time());
							$verify->save();
						}
					}
					
					// Set the form action
					$form_action = strtoupper(Kohana::lang('ui_admin.verified_unverified'));
				}
				
				//Delete Action
				elseif ($post->action == 'd')	
				{
					foreach($post->incident_id as $item)
					{
						$update = new Incident_Model($item);
						if ($update->loaded == TRUE)
						{
							$incident_id = $update->id;
							$location_id = $update->location_id;
							$update->delete();

							// Delete Location
							ORM::factory('location')->where('id',$location_id)->delete_all();

							// Delete Categories
							ORM::factory('incident_category')->where('incident_id',$incident_id)->delete_all();

							// Delete Translations
							ORM::factory('incident_lang')->where('incident_id',$incident_id)->delete_all();

							// Delete Photos From Directory
							foreach (ORM::factory('media')->where('incident_id',$incident_id)->where('media_type', 1) as $photo)
							{
								deletePhoto($photo->id);
							}

							// Delete Media
							ORM::factory('media')->where('incident_id',$incident_id)->delete_all();

							// Delete Sender
							ORM::factory('incident_person')->where('incident_id',$incident_id)->delete_all();

							// Delete relationship to SMS message
							$updatemessage = ORM::factory('message')->where('incident_id',$incident_id)->find();
							if ($updatemessage->loaded == TRUE)
							{
								$updatemessage->incident_id = 0;
								$updatemessage->save();
							}

							// Delete Comments
							ORM::factory('comment')->where('incident_id',$incident_id)->delete_all();
							
							// Delete form responses
							ORM::factory('form_response')->where('incident_id', $incident_id)->delete_all();
							
							// Delete form mail_info
													
								$strSqlCommand  = "DELETE FROM mail_info WHERE incident_id=".$incident_id;
								mysql_query($strSqlCommand);									
	
							// Action::report_delete - Deleted a Report
							Event::run('ushahidi_action.report_delete', $incident_id);
						}
					}
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
				}
				$form_saved = TRUE;
			}
			else
			{
				$form_error = TRUE;
			}

		}

		$all_reports = Incident_Model::get_incidents($params);
		
		// Pagination
		$pagination = new Pagination(array(
			'query_string'	 => 'page',
			'items_per_page' => $this->items_per_page,
			'total_items'	 => $all_reports->count()
			)
		);
		Event::run('ushahidi_filter.pagination',$pagination);
		
		// Get the paginated reports
		$incidents = Incident_Model::get_incidents($params, $pagination);
	
		Event::run('ushahidi_filter.filter_incidents',$incidents);
		$this->template->content->countries = Country_Model::get_countries_list();
		$this->template->content->incidents = $incidents;
		$this->template->content->pagination = $pagination;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;

		// Total Reports
		$this->template->content->total_items = $pagination->total_items;

		// Status Tab
		$this->template->content->status = $status;

		// Javascript Header
		$this->template->js = new View('admin/reports_js');
	}
	
	//testtest
	public function group()
	{
		// If user doesn't have access, redirect to dashboard
		if ( ! admin::permissions($this->user, "reports_upload"))
		{
			url::redirect(url::site().'admin/dashboard');
		}

		$this->template->content = new View('admin/reports_group');
		$this->template->content->title = Kohana::lang('ui_admin.reports_group');	
		
	}
	//testtest
	
	/**
	 * Edit a report
	 * @param bool|int $id The id no. of the report
	 * @param bool|string $saved
	 */
	public function edit($id = FALSE, $saved = FALSE)
	{
		$db = new Database();
		
		// If user doesn't have access, redirect to dashboard
		if ( ! admin::permissions($this->user, "reports_edit"))
		{
			url::redirect(url::site().'admin/dashboard');
		}

		$this->template->content = new View('admin/reports_edit');
		$this->template->content->title = Kohana::lang('ui_admin.create_report');

		// setup and initialize form field names
		$form = array
		(
			'location_id' => '',
			'form_id' => '',
			'locale' => '',
			'incident_title' => '',
			'incident_description' => '',
			'incident_date' => '',
			'incident_hour' => '',
			'incident_minute' => '',
			'incident_ampm' => '',
			'incident_status' => '',
			'phone_number' => '',
			'latitude' => '',
			'longitude' => '',
			'geometry' => array(),
			'location_name' => '',
			'country_id' => '',
			'country_name' =>'',
			'incident_category' => array(),
			'incident_news' => array(),
			'incident_video' => array(),
			'incident_photo' => array(),
			'person_first' => '',
			'person_last' => '',
			'person_email' => '',
			'custom_field' => array(),
			'incident_active' => '',
			'incident_verified' => '',
			'incident_zoom' => '',
			'ggroup_id' => ''
		);

		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = ($saved == 'saved');

		// Initialize Default Values
		$form['locale'] = Kohana::config('locale.language');
		//$form['latitude'] = Kohana::config('settings.default_lat');
		//$form['longitude'] = Kohana::config('settings.default_lon');
		$form['incident_date'] = date("m/d/Y",time());
		$form['incident_hour'] = date('h');
		$form['incident_minute'] = date('i');
		$form['incident_ampm'] = date('a');
		$form['country_id'] = Kohana::config('settings.default_country');
		
		// initialize custom field array
    $form['custom_field'] = customforms::get_custom_form_fields($id,'',true);

		// Locale (Language) Array
		$this->template->content->locale_array = Kohana::config('locale.all_languages');

		// Create Categories
		$this->template->content->categories = Category_Model::get_categories();
		$this->template->content->new_categories_form = $this->_new_categories_form_arr();

		// Time formatting
		$this->template->content->hour_array = $this->_hour_array();
		$this->template->content->minute_array = $this->_minute_array();
		$this->template->content->ampm_array = $this->_ampm_array();
		
		$this->template->content->stroke_width_array = $this->_stroke_width_array();

		// Get Countries
		$countries = array();
		foreach (ORM::factory('country')->orderby('country')->find_all() as $country)
		{
			// Create a list of all countries
			$this_country = $country->country;
			if (strlen($this_country) > 35)
			{
				$this_country = substr($this_country, 0, 35) . "...";
			}
			$countries[$country->id] = $this_country;
		}
		
		// Initialize Default Value for Hidden Field Country Name, just incase Reverse Geo coding yields no result
		$form['country_name'] = $countries[$form['country_id']];
		
		$this->template->content->countries = $countries;

		//GET custom forms
		$forms = array();
		foreach (ORM::factory('form')->where('form_active',1)->find_all() as $custom_forms)
		{
			$forms[$custom_forms->id] = $custom_forms->form_title;
		}
		
		$this->template->content->forms = $forms;
		
		//GET custom forms
		$ggroup = array();
		foreach (ORM::factory('GGroup')->find_all() as $custom_forms)
		{
			$ggroup[$custom_forms->id] = $custom_forms->GGroup_Name;
		}
		
		$this->template->content->ggroup = $ggroup;
		
		// Get the incident media
		$incident_media =  Incident_Model::is_valid_incident($id)
			? ORM::factory('incident', $id)->media
			: FALSE;
		
		$this->template->content->incident_media = $incident_media;

		// Are we creating this report from SMS/Email/Twitter?
		// If so retrieve message
		if ( isset($_GET['mid']) AND intval($_GET['mid']) > 0 ) {

			$message_id = intval($_GET['mid']);
			$service_id = "";
			$message = ORM::factory('message', $message_id);

			if ($message->loaded AND $message->message_type == 1)
			{
				$service_id = $message->reporter->service_id;

				// Has a report already been created for this Message?
				if ($message->incident_id != 0) {
					
					// Redirect to report
					url::redirect('admin/reports/edit/'. $message->incident_id);
				}

				$this->template->content->show_messages = true;
				$incident_description = $message->message;
				if ( ! empty($message->message_detail))
				{
					$form['incident_title'] = $message->message;
					$incident_description = $message->message_detail;
				}
				
				$form['incident_description'] = $incident_description;
				$form['incident_date'] = date('m/d/Y', strtotime($message->message_date));
				$form['incident_hour'] = date('h', strtotime($message->message_date));
				$form['incident_minute'] = date('i', strtotime($message->message_date));
				$form['incident_ampm'] = date('a', strtotime($message->message_date));
				$form['person_first'] = $message->reporter->reporter_first;
				$form['person_last'] = $message->reporter->reporter_last;

				// Does the sender of this message have a location?
				if ($message->reporter->location->loaded)
				{
					$form['location_id'] = $message->reporter->location->id;
					$form['latitude'] = $message->reporter->location->latitude;
					$form['longitude'] = $message->reporter->location->longitude;
					$form['location_name'] = $message->reporter->location->location_name;
				}else{
					//see if the message contains a hash followed by a 10 digit number
					$regex_pattern = "/#\d{10}/";
					preg_match_all($regex_pattern,$incident_description,$matches,1);
					if(count($matches[0]) == 1){
						//extract the number
						$id = substr($matches[0][0],1);
						//convert to a location code
						$pc = new LocationCode();
						$coords = $pc->id2coords($id);
						$form['latitude'] = $coords[0];
						$form['longitude'] = $coords[1];
					}
				}

				//Events to manipulate an already known location

				Event::run('ushahidi_action.location_from',$message_from = $message->message_from);
				//filter location name
				Event::run('ushahidi_filter.location_name',$form['location_name']);
				//filter //location find
				Event::run('ushahidi_filter.location_find',$form['location_find']);


				// Retrieve Last 5 Messages From this account
				$this->template->content->all_messages = ORM::factory('message')
					->where('reporter_id', $message->reporter_id)
					->orderby('message_date', 'desc')
					->limit(5)
					->find_all();
					
			}
			else
			{
				$message_id = "";
				$this->template->content->show_messages = false;
			}
		}
		else
		{
			$this->template->content->show_messages = false;
		}

		// Are we creating this report from a Newsfeed?
		if ( isset($_GET['fid']) AND intval($_GET['fid']) > 0 )
		{
			$feed_item_id = intval($_GET['fid']);
			$feed_item = ORM::factory('feed_item', $feed_item_id);

			if ($feed_item->loaded)
			{
				// Has a report already been created for this Feed item?
				if ($feed_item->incident_id != 0)
				{
					// Redirect to report
					url::redirect('admin/reports/edit/'. $feed_item->incident_id);
				}

				$form['incident_title'] = $feed_item->item_title;
				$form['incident_description'] = $feed_item->item_description;
				$form['incident_date'] = date('m/d/Y', strtotime($feed_item->item_date));
				$form['incident_hour'] = date('h', strtotime($feed_item->item_date));
				$form['incident_minute'] = date('i', strtotime($feed_item->item_date));
				$form['incident_ampm'] = date('a', strtotime($feed_item->item_date));

				// News Link
				$form['incident_news'][0] = $feed_item->item_link;

				// Does this newsfeed have a geolocation?
				if ($feed_item->location_id)
				{
					$form['location_id'] = $feed_item->location_id;
					$form['latitude'] = $feed_item->location->latitude;
					$form['longitude'] = $feed_item->location->longitude;
					$form['location_name'] = $feed_item->location->location_name;
				}
			}
			else
			{
				$feed_item_id = "";
			}
		}

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = array_merge($_POST, $_FILES);
			
			// Check if the service id exists
			if (isset($service_id) AND intval($service_id) > 0)
			{
				$post = array_merge($post, array('service_id' => $service_id));
			}
			
			// Check if the incident id is valid an add it to the post data
			if (Incident_Model::is_valid_incident($id))
			{
				$post = array_merge($post, array('incident_id' => $id));
			}
			
			/**
			 * NOTES - E.Kala July 27, 2011
			 *
			 * Previously, the $post parameter for this event was a Validation
			 * object. Now it's an array (i.e. the raw data without any validation rules applied to them). 
			 * As such, all plugins making use of this event shall have to be updated
			 */
			
			// Action::report_submit_admin - Report Posted
			Event::run('ushahidi_action.report_submit_admin', $post);

			// Validate
			if (reports::validate($post, TRUE))
			{
				// Yes! everything is valid
				$location_id = $post->location_id;
				
				// STEP 1: SAVE LOCATION
				$location = new Location_Model($location_id);
				reports::save_location($post, $location);

				// STEP 2: SAVE INCIDENT
				$incident = new Incident_Model($id);
				reports::save_report($post, $incident, $location->id);
				
				// STEP 2b: Record Approval/Verification Action
				$verify = new Verify_Model();
				reports::verify_approve($post, $verify, $incident);
				
				// STEP 2c: SAVE INCIDENT GEOMETRIES
				reports::save_report_geometry($post, $incident);

				// STEP 3: SAVE CATEGORIES
				reports::save_category($post, $incident);

				// STEP 4: SAVE MEDIA
				reports::save_media($post, $incident);

				// STEP 5: SAVE PERSONAL INFORMATION
				reports::save_personal_info($post, $incident);

				// STEP 6a: SAVE LINK TO REPORTER MESSAGE
				// We're creating a report from a message with this option
				if (isset($message_id) AND intval($message_id) > 0)
				{
					$savemessage = ORM::factory('message', $message_id);
					if ($savemessage->loaded)
					{
						$savemessage->incident_id = $incident->id;
						$savemessage->save();

						// Does Message Have Attachments?
						// Add Attachments
						$attachments = ORM::factory("media")
							->where("message_id", $savemessage->id)
							->find_all();
						foreach ($attachments AS $attachment)
						{
							$attachment->incident_id = $incident->id;
							$attachment->save();
						}
					}
				}

				// STEP 6b: SAVE LINK TO NEWS FEED
				// We're creating a report from a newsfeed with this option
				if (isset($feed_item_id) AND intval($feed_item_id) > 0)
				{
					$savefeed = ORM::factory('feed_item', $feed_item_id);
					if ($savefeed->loaded)
					{
						$savefeed->incident_id = $incident->id;
						$savefeed->location_id = $location->id;
						$savefeed->save();
					}
				}

				// STEP 7: SAVE CUSTOM FORM FIELDS
				reports::save_custom_fields($post, $incident);

				// Action::report_edit - Edited a Report
				Event::run('ushahidi_action.report_edit', $incident);


				// SAVE AND CLOSE?
				if ($post->save == 1)		
				{
					// Save but don't close
					/*
					if(isset($_POST["incident_title"]))  //Add for Title=Group function
					{
						$test=$_POST["incident_title"];
						
    						$strSqlCommand  = "INSERT INTO `uganda`.`GGroup` (`GGroup_Name`) VALUES ('$test');";
							mysql_query($strSqlCommand);
							
					}*/
					
					if(isset($_POST['fo']))
					{
						$test=$_POST['fo'];
            			//echo $test.'!';
    					//$strSqlCommand1="INSERT INTO `db2`.`GGGroup` (`GGGroup_Name`) VALUES ('".$test."')";
						//echo "DELETE FROM GGroup WHERE GGroup_Name='".$test."'";
						//mysql_query($strSqlCommand1);
						//echo "<meta content='0.00000000000000000001' http-equiv='Refresh'/>";
						$strSqlCommand2 = "UPDATE `db`.`incident` SET  `ggroup_id` = '".$test."' where `incident`.`id` = ". $incident->id;
						$result2 = mysql_query($strSqlCommand2);
					}
					else{
					//$strSqlCommand7 = "SELECT max(id) FROM incident";
					//$result7 = mysql_query($strSqlCommand7);
					//$row = mysql_fetch_array ($result7);
					//$rowadd = $row['max(id)']+1;
					//echo $row['max(id)'];
					//$strSqlCommand2 = "UPDATE  `db2`.`incident` SET  `ggroup_id` =  '5' WHERE  `incident`.`id` = ".$result1;
					
					$strSqlCommand2 = "UPDATE `db`.`incident` SET  `ggroup_id` = '0' where `incident`.`id` = ". $incident->id;
					$result2 = mysql_query($strSqlCommand2);
					}
					/*
					//Compare GGroup_Name Let report to put in right position
					if(isset($_POST['incident_title']))
					{
						$test=$_POST['incident_title'];
						
						$strSqlCommandCompare="SELECT `id` FROM `GGroup` WHERE `GGroup_Name`='".$test."'";
						$resultCompare = mysql_query($strSqlCommandCompare);
						while ($Analysis = mysql_fetch_array ($resultCompare)) {
							$PutInGroupId = $Analysis['id'];
            				$strSqlCommand100 = "UPDATE `uganda`.`incident` SET  `ggroup_id` = '".$PutInGroupId."' where `incident`.`id` = ". $incident->id;
							$result100 = mysql_query($strSqlCommand100);
						}
					}*/
					
					url::redirect('admin/reports/edit/'. $incident->id .'/saved');
				}
				else						
				{
					//let save and close button can record group id
					if(isset($_POST['fo']))
					{
						$test=$_POST['fo'];
						$strSqlCommand2 = "UPDATE `db`.`incident` SET  `ggroup_id` = '".$test."' where `incident`.`id` = ". $incident->id;
						$result2 = mysql_query($strSqlCommand2);
					}
					else{
						$strSqlCommand2 = "UPDATE `db`.`incident` SET  `ggroup_id` = '0' where `incident`.`id` = ". $incident->id;
						$result2 = mysql_query($strSqlCommand2);
					}
					// Save and close
					url::redirect('admin/reports/');
				}
				
			}
			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('report'));
				$form_error = TRUE;
			}
		}
		else
		{
			if (Incident_Model::is_valid_incident($id))
			{
				// Retrieve Current Incident
				$incident = ORM::factory('incident', $id);
				if ($incident->loaded == true)
				{
					// Retrieve Categories
					$incident_category = array();
					foreach($incident->incident_category as $category)
					{
						$incident_category[] = $category->category_id;
					}

					// Retrieve Media
					$incident_news = array();
					$incident_video = array();
					$incident_photo = array();
					foreach($incident->media as $media)
					{
						if ($media->media_type == 4)
						{
							$incident_news[] = $media->media_link;
						}
						elseif ($media->media_type == 2)
						{
							$incident_video[] = $media->media_link;
						}
						elseif ($media->media_type == 1)
						{
							$incident_photo[] = $media->media_link;
						}
					}
					
					
					// Get Geometries via SQL query as ORM can't handle Spatial Data
					$sql = "SELECT AsText(geometry) as geometry, geometry_label, 
						geometry_comment, geometry_color, geometry_strokewidth 
						FROM ".Kohana::config('database.default.table_prefix')."geometry 
						WHERE incident_id=".$id;
					$query = $db->query($sql);
					foreach ( $query as $item )
					{
						$geometry = array(
								"geometry" => $item->geometry,
								"label" => $item->geometry_label,
								"comment" => $item->geometry_comment,
								"color" => $item->geometry_color,
								"strokewidth" => $item->geometry_strokewidth
							);
						$form['geometry'][] = json_encode($geometry);
					}
					
					// Combine Everything
					$incident_arr = array(
						'location_id' => $incident->location->id,
						'form_id' => $incident->form_id,
						'locale' => $incident->locale,
						'incident_title' => $incident->incident_title,
						'incident_description' => $incident->incident_description,
						'incident_date' => date('m/d/Y', strtotime($incident->incident_date)),
						'incident_hour' => date('h', strtotime($incident->incident_date)),
						'incident_minute' => date('i', strtotime($incident->incident_date)),
						'incident_ampm' => date('a', strtotime($incident->incident_date)),
						'incident_status' => $incident->incident_status,
						'phone_number' => $incident->get_message_from(),
						'latitude' => $incident->location->latitude,
						'longitude' => $incident->location->longitude,
						'location_name' => $incident->location->location_name,
						'country_id' => $incident->location->country_id,
						'incident_category' => $incident_category,
						'incident_news' => $incident_news,
						'incident_video' => $incident_video,
						'incident_photo' => $incident_photo,
						'person_first' => $incident->incident_person->person_first,
						'person_last' => $incident->incident_person->person_last,
						'person_email' => $incident->incident_person->person_email,
						'custom_field' => customforms::get_custom_form_fields($id,$incident->form_id,true),
						'incident_active' => $incident->incident_active,
						'incident_verified' => $incident->incident_verified,
						'incident_zoom' => $incident->incident_zoom
					);

					// Merge To Form Array For Display
					$form = arr::overwrite($form, $incident_arr);
				}
				else
				{
					// Redirect
					url::redirect('admin/reports/');
				}

			}
		}
		
		$this->template->content->id = $id;
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		
		// Retrieve Custom Form Fields Structure
		$this->template->content->custom_forms = new View('reports_submit_custom_forms');
		$disp_custom_fields = customforms::get_custom_form_fields($id, $form['form_id'], FALSE, "view");
		$custom_field_mismatch = customforms::get_edit_mismatch($form['form_id']);
        $this->template->content->custom_forms->disp_custom_fields = $disp_custom_fields;
		$this->template->content->custom_forms->custom_field_mismatch = $custom_field_mismatch;
		$this->template->content->custom_forms->form = $form;

		// Retrieve Previous & Next Records
		$previous = ORM::factory('incident')->where('id < ', $id)->orderby('id','desc')->find();
		$previous_url = ($previous->loaded ?
				url::base().'admin/reports/edit/'.$previous->id :
				url::base().'admin/reports/');
		$next = ORM::factory('incident')->where('id > ', $id)->orderby('id','desc')->find();
		$next_url = ($next->loaded ?
				url::base().'admin/reports/edit/'.$next->id :
				url::base().'admin/reports/');
		$this->template->content->previous_url = $previous_url;
		$this->template->content->next_url = $next_url;

		// Javascript Header
		$this->template->map_enabled = TRUE;
		$this->template->colorpicker_enabled = TRUE;
		$this->template->treeview_enabled = TRUE;
		$this->template->json2_enabled = TRUE;
		
		$this->template->js = new View('reports_submit_edit_js');
		$this->template->js->edit_mode = TRUE;
		$this->template->js->default_map = Kohana::config('settings.default_map');
		$this->template->js->default_zoom = Kohana::config('settings.default_zoom');
		
		if ( ! $form['latitude'] OR !$form['latitude'])
		{
			$this->template->js->latitude = Kohana::config('settings.default_lat');
			$this->template->js->longitude = Kohana::config('settings.default_lon');
		}
		else
		{
			$this->template->js->latitude = $form['latitude'];
			$this->template->js->longitude = $form['longitude'];
		}
		
		$this->template->js->incident_zoom = $form['incident_zoom'];
		$this->template->js->geometries = $form['geometry'];
		
		// Inline Javascript
		$this->template->content->date_picker_js = $this->_date_picker_js();
		$this->template->content->color_picker_js = $this->_color_picker_js();
		$this->template->content->new_category_toggle_js = $this->_new_category_toggle_js();
		
		// Pack Javascript
		$myPacker = new javascriptpacker($this->template->js , 'Normal', false, false);
		$this->template->js = $myPacker->pack();
	}

	/**
	 * Download Reports in CSV format
	 */
	public function download()
	{
		// If user doesn't have access, redirect to dashboard
		if ( ! admin::permissions($this->user, "reports_download"))
		{
			url::redirect(url::site().'admin/dashboard');
		}

		$this->template->content = new View('admin/reports_download');
		$this->template->content->title = Kohana::lang('ui_admin.download_reports');

		$form = array(
			'data_point'   => '',
			'data_include' => '',
			'from_date'	   => '',
			'to_date'	   => ''
		);
		
		$errors = $form;
		$form_error = FALSE;

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			 //	Add some filters
			$post->pre_filter('trim', TRUE);

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// Retrieve reports
        $form_id = $_POST['form_id'];
        Kohana::log('error', 'Form id = '. $form_id);
        $incidents = ORM::factory('incident')->where('form_id',$form_id)
          ->orderby('incident_dateadd', 'desc')->find_all();

        $column_names = array('id', 'incident_title', 'incident_date', 'incident_description',
                              'incident_active', 'incident_verified', 'incident_rating', 'incident_mode',
                              'incident_datemodify');
        $sub_models = array('location' => array('location_name', 'latitude', 'longitude'));
        $cat_names = Category_Model::get_category_names();

				$header = '';
        foreach($column_names as $column){
          $header .= $column . ',';
        }
        foreach(array_values($sub_models) as $values){
          foreach($values as $value){
            $header .= $value . ',';
          }
        }
        foreach($cat_names as $c){
          $header .= $c . ',';
        }
        foreach(ORM::factory('form_field')->where('form_id',$form_id)
          ->orderby('id', 'desc')->find_all() as $custom_field){
          $header .= $custom_field->field_name . ',';
        }
        $header = rtrim($header, ',') . "\n";

        $body = '';
        foreach($incidents as $incident){
          // Basic properties
          $body .= $this->_get_cs_list($incident, $column_names) . ',';
          //sub objects
          foreach(array_keys($sub_models) as $sub_model){
            $columns = $sub_models[$sub_model];
            $body .= $this->_get_cs_list($incident->$sub_model, $columns) . ',';
          }
          // categories
          foreach($cat_names as $c){
            $found = False;
            foreach($incident->incident_category as $incident_category){
              $found = $found | $incident_category->category->category_title == $c;
            }
            $body .= $this->_csv_text($found) . ',';
          }
          //custom fields
          $custom_fields = ORM::factory('form_response')->where('incident_id',$incident->id)
            ->orderby('form_field_id','desc')->find_all();
          foreach($custom_fields as $custom_field)
          {
            $body .= $this->_csv_text($custom_field->form_response) . ',';
          }	
          $body = rtrim($body, ',') . "\n";
        }

        $report_csv = $header . $body;

				// Output to browser
				header("Content-type: text/x-csv");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-Disposition: attachment; filename=" . time() . ".csv");
				header("Content-Length: " . strlen($report_csv));
				echo $report_csv;
				exit;
			}
			
			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('report'));
				$form_error = TRUE;
			}
		}

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;

		// Javascript Header
		$this->template->js = new View('admin/reports_download_js');
		$this->template->js->calendar_img = url::base() . "media/img/icon-calendar.gif";
	}

	public function upload()
	{
		// If user doesn't have access, redirect to dashboard
		if ( ! admin::permissions($this->user, "reports_upload"))
		{
			url::redirect(url::site().'admin/dashboard');
		}

		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->template->content = new View('admin/reports_upload');
			$this->template->content->title = 'Upload Reports';
			$this->template->content->form_error = false;
		}
		
		if ($_SERVER['REQUEST_METHOD']=='POST')
		{
			$errors = array();
			$notices = array();
			
			if (!$_FILES['csvfile']['error']) 
			{
				if (file_exists($_FILES['csvfile']['tmp_name']))
				{
					if($filehandle = fopen($_FILES['csvfile']['tmp_name'], 'r'))
					{
						$importer = new ReportsImporter;
						
						if ($importer->import($filehandle))
						{
							$this->template->content = new View('admin/reports_upload_success');
							$this->template->content->title = 'Upload Reports';
							$this->template->content->rowcount = $importer->totalrows;
							$this->template->content->imported = $importer->importedrows;
							$this->template->content->notices = $importer->notices;
						}
						else
						{
							$errors = $importer->errors;
						}
					}
					else
					{
						$errors[] = Kohana::lang('ui_admin.file_open_error');
					}
				} 
				
				// File exists?
				else
				{
					$errors[] = Kohana::lang('ui_admin.file_not_found_upload');
				}
			} 
			
			// Upload errors?
			else
			{
				$errors[] = $_FILES['csvfile']['error'];
			}

			if(count($errors))
			{
				$this->template->content = new View('admin/reports_upload');
				$this->template->content->title = Kohana::lang('ui_admin.upload_reports');
				$this->template->content->errors = $errors;
				$this->template->content->form_error = 1;
			}
		}
	}

	/**
	* Translate a report
	* @param bool|int $id The id no. of the report
	* @param bool|string $saved
	*/

	public function translate( $id = false, $saved = FALSE)
	{
		$this->template->content = new View('admin/reports_translate');
		$this->template->content->title = Kohana::lang('ui_admin.translate_reports');

		// Which incident are we adding this translation for?
		if (isset($_GET['iid']) && !empty($_GET['iid']))
		{
			$incident_id = (int) $_GET['iid'];
			$incident = ORM::factory('incident', $incident_id);
			
			if ($incident->loaded == true)
			{
				$orig_locale = $incident->locale;
				$this->template->content->orig_title = $incident->incident_title;
				$this->template->content->orig_description = $incident->incident_description;
			}
			else
			{
				// Redirect
				url::redirect('admin/reports/');
			}
		}
		else
		{
			// Redirect
			url::redirect('admin/reports/');
		}


		// Setup and initialize form field names
		$form = array(
			'locale'	  => '',
			'incident_title'	  => '',
			'incident_description'	  => ''
		);
		
		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		
		$form_saved = ($saved == 'saved')? TRUE : FALSE;

		// Locale (Language) Array
		$this->template->content->locale_array = Kohana::config('locale.all_languages');

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			 //	Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('locale','required','alpha_dash','length[5]');
			$post->add_rules('incident_title','required', 'length[3,200]');
			$post->add_rules('incident_description','required');
			$post->add_callbacks('locale', array($this,'translate_exists_chk'));

			if ($orig_locale == $_POST['locale'])
			{
				// The original report and the translation are the same language!
				$post->add_error('locale','locale');
			}

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// SAVE INCIDENT TRANSLATION
				$incident_l = new Incident_Lang_Model($id);
				$incident_l->incident_id = $incident_id;
				$incident_l->locale = $post->locale;
				$incident_l->incident_title = $post->incident_title;
				$incident_l->incident_description = $post->incident_description;
				$incident_l->save();


				// SAVE AND CLOSE?
				// Save but don't close
				if ($post->save == 1)		
				{
					url::redirect('admin/reports/translate/'. $incident_l->id .'/saved/?iid=' . $incident_id);
				}
				
				// Save and close
				else						
				{
					url::redirect('admin/reports/');
				}
			}

			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('report'));
				$form_error = TRUE;
			}
		}
		else
		{
			if ($id)
			{
				// Retrieve Current Incident
				$incident_l = ORM::factory('incident_lang', $id)->where('incident_id', $incident_id)->find();
				if ($incident_l->loaded == true)
				{
					$form['locale'] = $incident_l->locale;
					$form['incident_title'] = $incident_l->incident_title;
					$form['incident_description'] = $incident_l->incident_description;
				}
				else
				{
					// Redirect
					url::redirect('admin/reports/');
				}

			}
		}

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;

		// Javascript Header
		$this->template->js = new View('admin/reports_translate_js');
	}
	
	/*
	 * Should be in simple groups but it isn't.
	 */
	public function remove_group() {
    $this->auto_render = FALSE;
		$this->template = '';
		json_encode('moo');
		if($_POST) {
		  // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);
			$post->pre_filter('trim', TRUE);
			// Add rules
			$post->add_rules('incident_id', 'required');
			$post->add_rules('group_id', 'required');
			
			if($post->validate()) {
			  $incident = ORM::factory('simplegroups_groups_incident')
  			  ->where(array('incident_id' => $post['incident_id'], 'simplegroups_groups_id' => $post['group_id']))
  			  ->delete_all();
			}
		}
  }

  /*
	 * Change report status, based on drop-down list
	 * Currently only works for post
	 */
  public function change_status($form_id = 0, $incident_id = 0, $status = 0) {
    $this->auto_render = FALSE;
		$this->template = "";
		
    if($_POST) {
      // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);
			$post->pre_filter('trim', TRUE);
			// Add rules
			$post->add_rules('incident_id', 'required');
			$post->add_rules('status', 'required');
			
			if($post->validate()) {
			  $incident = ORM::factory('incident', $post['incident_id']);
			  // Only if the incident exists
			  if($incident->id) {
			    $incident->incident_status = $post['status'];
			    $incident->incident_verified = ($post['status'] > 1);
			    $incident->incident_active = !($post['status'] == 5);
			    $incident->save();
			  }
			}
    }
  }


	/**
	* Save newly added dynamic categories
	*/
	public function save_category()
	{
		$this->auto_render = FALSE;
		$this->template = "";

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

			 //	Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('category_title','required', 'length[3,200]');
			$post->add_rules('category_description','required');
			$post->add_rules('category_color','required', 'length[6,6]');


			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// SAVE Category
				$category = new Category_Model();
				$category->category_title = $post->category_title;
				$category->category_description = $post->category_description;
				$category->category_color = $post->category_color;
				$category->save();
				$form_saved = TRUE;

				echo json_encode(array("status"=>"saved", "id"=>$category->id));
			}
			else

			{
				echo json_encode(array("status"=>"error"));
			}
		}
		else
		{
			echo json_encode(array("status"=>"error"));
		}
	}

	/**
	* Delete Photo
	* @param int $id The unique id of the photo to be deleted
	*/
	public function deletePhoto ($id)
	{
		$this->auto_render = FALSE;
		$this->template = "";

		if ($id)
		{
			$photo = ORM::factory('media', $id);
			$photo_large = $photo->media_link;
			$photo_medium = $photo->media_medium;
			$photo_thumb = $photo->media_thumb;
			
			if (file_exists(Kohana::config('upload.directory', TRUE).$photo_large))
			{
				unlink(Kohana::config('upload.directory', TRUE).$photo_large);
			}
			elseif (Kohana::config("cdn.cdn_store_dynamic_content") AND valid::url($photo_large))
			{
				cdn::delete($photo_large);
			}
			
			if (file_exists(Kohana::config('upload.directory', TRUE).$photo_medium))
			{
				unlink(Kohana::config('upload.directory', TRUE).$photo_medium);
			}
			elseif (Kohana::config("cdn.cdn_store_dynamic_content") AND valid::url($photo_medium))
			{
				cdn::delete($photo_medium);
			}

			if (file_exists(Kohana::config('upload.directory', TRUE).$photo_thumb))
			{
				unlink(Kohana::config('upload.directory', TRUE).$photo_thumb);
			}
			elseif (Kohana::config("cdn.cdn_store_dynamic_content") AND valid::url($photo_thumb))
			{
				cdn::delete($photo_thumb);
			}

			// Finally Remove from DB
			$photo->delete();
		}
	}

	/* private functions */

	// Dynamic categories form fields
	private function _new_categories_form_arr()
	{
		return array(
			'category_name' => '',
			'category_description' => '',
			'category_color' => '',
		);
	}

	// Time functions
	private function _hour_array()
	{
		for ($i=1; $i <= 12 ; $i++)
		{
			// Add Leading Zero
			$hour_array[sprintf("%02d", $i)] = sprintf("%02d", $i);		
		}
		return $hour_array;
	}

	private function _minute_array()
	{
		for ($j=0; $j <= 59 ; $j++)
		{
			// Add Leading Zero
			$minute_array[sprintf("%02d", $j)] = sprintf("%02d", $j);	
		}

		return $minute_array;
	}

	private function _ampm_array()
	{
		return $ampm_array = array('pm'=>Kohana::lang('ui_admin.pm'),'am'=>Kohana::lang('ui_admin.am'));
	}
	
	private function _stroke_width_array()
	{
		for ($i = 0.5; $i <= 8 ; $i += 0.5)
		{
			$stroke_width_array["$i"] = $i;
		}
		
		return $stroke_width_array;
	}

	// Javascript functions
	private function _color_picker_js()
	{
		 return "<script type=\"text/javascript\">
					$(document).ready(function() {
					$('#category_color').ColorPicker({
							onSubmit: function(hsb, hex, rgb) {
								$('#category_color').val(hex);
							},
							onChange: function(hsb, hex, rgb) {
								$('#category_color').val(hex);
							},
							onBeforeShow: function () {
								$(this).ColorPickerSetColor(this.value);
							}
						})
					.bind('keyup', function(){
						$(this).ColorPickerSetColor(this.value);
					});
					});
				</script>";
	}

	private function _date_picker_js()
	{
		return "<script type=\"text/javascript\">
				$(document).ready(function() {
				$(\"#incident_date\").datepicker({
				showOn: \"both\",
				buttonImage: \"" . url::base() . "media/img/icon-calendar.gif\",
				buttonImageOnly: true
				});
				});
			</script>";
	}


	private function _new_category_toggle_js()
	{
		return "<script type=\"text/javascript\">
				$(document).ready(function() {
				$('a#category_toggle').click(function() {
				$('#category_add').toggle(400);
				return false;
				});
				});
			</script>";
	}


	/**
	 * Checks if translation for this report & locale exists
	 * @param Validation $post $_POST variable with validation rules
	 * @param int $iid The unique incident_id of the original report
	 */
	public function translate_exists_chk(Validation $post)
	{
		// If add->rules validation found any errors, get me out of here!
		if (array_key_exists('locale', $post->errors()))
			return;

		$iid = (isset($_GET['iid']) AND intval($_GTE['iid'] > 0))? intval($_GET['iid']) : 0;
		
		// Load translation
		$translate = ORM::factory('incident_lang')
						->where('incident_id',$iid)
						->where('locale',$post->locale)
						->find();
		
		if ($translate->loaded)
		{
			$post->add_error( 'locale', 'exists');
		}
		else
		{
			// Not found
			return;
		}
	}

	/**
	 * Creates a SQL string from search keywords
	 */
	private function _get_searchstring($keyword_raw)
	{
		$or = '';
		$where_string = '';


		// Stop words that we won't search for
		// Add words as needed!!
		$stop_words = array('the', 'and', 'a', 'to', 'of', 'in', 'i', 'is', 'that', 'it',
		'on', 'you', 'this', 'for', 'but', 'with', 'are', 'have', 'be',
		'at', 'or', 'as', 'was', 'so', 'if', 'out', 'not');

		$keywords = explode(' ', $keyword_raw);
		
		if (is_array($keywords) AND !empty($keywords))
		{
			array_change_key_case($keywords, CASE_LOWER);
			$i = 0;
			
			foreach ($keywords as $value)
			{
				if (!in_array($value,$stop_words) AND !empty($value))
				{
					$chunk = $this->db->escape_str($value);
					if ($i > 0)
					{
						$or = ' OR ';
					}
					$where_string = $where_string
									.$or
									."incident_title LIKE '%$chunk%' OR incident_description LIKE '%$chunk%'  OR location_name LIKE '%$chunk%'";
					$i++;
				}
			}
		}
		
		// Return
		return $where_string;
	}

	private function _csv_text($text)
	{
		return '"' . stripslashes(htmlspecialchars($text)) . '"';
	}

  private function _get_cs_list($object, $columns)
    //returns the values of the object specified in $columns seperated by commata
  {
    $csv = '';
    foreach($columns as $column){
      $csv .= $this->_csv_text($object->$column) . ','; 
    }
    return rtrim($csv, ',');
  }

  public function get_custom_forms(){
		$custom_forms = array();
		foreach (ORM::factory('form')->where('form_active',1)->find_all() as $custom_form)
		{
			$custom_forms[$custom_form->id] = $custom_form->form_title;
		}
    return $custom_forms;
  }

}
