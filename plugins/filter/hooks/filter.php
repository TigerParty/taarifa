<?php defined('SYSPATH') or die('No direct script access.');
class filter {

	//Registers the main event add method
	public function __construct()
	{
		//Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
	}

	//Adds all the events to the main Ushahidi application
	public function add()
	{
		//only add the events if we are on the controller
		if ( Router::$controller == 'reports' )
		{
			switch (Router::$method)
			{
				case 'index':
					//Add the filter view to the reports page(front end)
					Event::add('ushahidi_action.reports_block', array($this, '_add_filter_front_view'));
					
					//Add filter to return the incidents by constituency
					Event::add('ushahidi_filter.filter_constituency', array($this, '_filter_constituency'));
					
					//Add filter to return pagination object
					Event::add('ushahidi_filter.pagination', array($this, '_pagination'));
					
					Event::add('ushahidi_filter.total_reports', array($this, '_total_reports'));
					
					Event::add('ushahidi_filter.oldest_timestamp', array($this, '_oldest_timestamp'));
					break;

				case 'submit':
					//Add the filter view to the submit reports page
					Event::add('ushahidi_action.reports_block', array($this, '_add_filter_front_view'));
					
					//Get the incident_id from submit
					Event::add('ushahidi_action.incident_constituency', array($this, '_add_incident_constituency'));
					break;
			}
		}
		if ( Router::$controller == 'admin/reports')
		{
			switch (Router::$method)
			{
				case 'index':
					
					Event::add('ushahidi_action.reports_block', array($this, '_add_filter_admin_view'));
					break;
			}
		}
	}

	//Return oldest timestamp
	public function _oldest_timestamp()
	{
		$oldest_timestamp = Event::$data;
		
		$db = new Database;
		if ($_POST)
		{
			$oldest_timestamp = $db->query("select max(i.incident_date) from incident as i, filter_incident_constituency as icon where i.incident_active = 1 and i.id = icon.incident_id");
			//$oldest_timestamp =  strtotime($oldest_timestamp);
			Event::$data =  $oldest_timestamp;
			echo Kohana::debug($oldest_timestamp);
		}
		else
		{
			$oldest_timestamp = $db->query("select max(incident_date) from incident where incident_active = 1");
			//$oldest_timestamp =  strtotime($oldest_timestamp);
			Event::$data =  $oldest_timestamp;
			echo Kohana::debug($oldest_timestamp);
		}
	}

	//Return total reports filtered
	public function _total_reports()
	{
		$total_reports = Event::$data;

		$db = new Database;
		if ($_POST)
		{
			$total_reports = $db->query("select * from incident as i, filter_incident_constituency as icon where i.id = icon.incident_id")->count();
			Event::$data = $total_reports;
		}
		else
		{
			$total_reports = $db->query("select * from incident where incident_active = 1")->count();
			Event::$data = $total_reports;
		}
	}

	//Return pagination object
	public function _pagination()
	{
		$pagination = Event::$data;
		
		//Check, has the form been submitted
		if ($_POST)
		{
			$constituency_id = $_POST['constituency_id'];

			$db = new Database;

			$filter = ( isset($_GET['c']) && !empty($_GET['c']) && $_GET['c']!=0 )
				? " AND ( c.id='".$_GET['c']."' OR 
					c.parent_id='".$_GET['c']."' )  "
				: " AND 1 = 1";	

			// Pagination
			$pagination = new Pagination(array(
					'query_string' => 'page',
					'items_per_page' => (int) Kohana::config('settings.items_per_page'),
					'total_items' => $db->query("SELECT DISTINCT i.* FROM `".Kohana::config('database.default.table_prefix')."incident` AS i JOIN `".Kohana::config('database.default.table_prefix')."incident_category` AS ic ON (i.`id` = ic.`incident_id`) JOIN `".Kohana::config('database.default.table_prefix')."filter_incident_constituency` AS icons ON (i.`id` = icons.`incident_id` AND icons.constituency_id = ".$constituency_id.") JOIN `".Kohana::config('database.default.table_prefix')."category` AS c ON (c.`id` = ic.`category_id`) JOIN `".Kohana::config('database.default.table_prefix')."location` AS l ON (i.`location_id` = l.`id`) WHERE `incident_active` = '1' $filter")->count()
					));

			Event::$data = $pagination;
		}
	}
		
	//Returns incidents filtered by constituency
	public function _filter_constituency()
	{
		$incidents = Event::$data;
		
		// Check, has the form been submitted
		if ($_POST)
		{
			//echo Kohana::debug($_POST);
			$constituency_id = $_POST['constituency_id'];
			
			$db = new Database;

			$filter = ( isset($_GET['c']) && !empty($_GET['c']) && $_GET['c']!=0 )
				? " AND ( c.id='".$_GET['c']."' OR 
					c.parent_id='".$_GET['c']."' )  "
				: " AND 1 = 1";	

			// Pagination
			$pagination = new Pagination(array(
					'query_string' => 'page',
					'items_per_page' => (int) Kohana::config('settings.items_per_page'),
					'total_items' => $db->query("SELECT DISTINCT i.* FROM `".Kohana::config('database.default.table_prefix')."incident` AS i JOIN `".Kohana::config('database.default.table_prefix')."incident_category` AS ic ON (i.`id` = ic.`incident_id`) JOIN `".Kohana::config('database.default.table_prefix')."filter_incident_constituency` AS icons ON (i.`id` = icons.`incident_id` AND icons.constituency_id = ".$constituency_id.") JOIN `".Kohana::config('database.default.table_prefix')."category` AS c ON (c.`id` = ic.`category_id`) JOIN `".Kohana::config('database.default.table_prefix')."location` AS l ON (i.`location_id` = l.`id`) WHERE `incident_active` = '1' $filter")->count()
					));

			// Incidents
			$incidents = $db->query("SELECT DISTINCT i.*, l.`location_name` FROM `".Kohana::config('database.default.table_prefix')."incident` AS i JOIN `".Kohana::config('database.default.table_prefix')."incident_category` AS ic ON (i.`id` = ic.`incident_id`) JOIN `".Kohana::config('database.default.table_prefix')."filter_incident_constituency` AS icons ON (i.`id` = icons.`incident_id` AND icons.constituency_id = ".$constituency_id.") JOIN  `".Kohana::config('database.default.table_prefix')."category` AS c ON (c.`id` = ic.`category_id`) JOIN `".Kohana::config('database.default.table_prefix')."location` AS l ON (i.`location_id` = l.`id`) WHERE `incident_active` = '1' $filter ORDER BY incident_date DESC LIMIT ".Kohana::config('settings.items_per_page')." OFFSET ".$pagination->sql_offset);
			
			Event::$data = $incidents;
		}
	}
	
	//Adds incident_id, constituency_id to table
	public function _add_incident_constituency()
	{
		//Capture the incident_id
		$incident_id = Event::$data;
		
		$incident_constituency = new Incident_Constituency_Model();
		$incident_constituency->constituency_id = $constituency_id;
		$incident_constituency->incident_id = $incident_id;
		$incident_constituency->save();
	}
	
	//Adds the Filter front end view
	public function _add_filter_front_view()
	{
		$filters = ORM::factory('filter')
					->find_all();

		$filter_view = View::factory('filter');
		
		$filters_list = array();

		foreach ($filters as $filter)
		{
			$filters_list[$filter->id] = $filter->constituency_name;
		}

		$filter_view->filters_list = $filters_list;

		$filter_view->render(TRUE);
	}

	public function _add_filter_admin_view()
	{
		$filters = ORM::factory('filter')->find_all();

		$filter_view = View::factory('admin/filter');

		$filters_list = array();

		foreach ($filters as $filter)
		{
			$filters_list[$filter->id] = $filter->constituency_name;
		}

		$filter_view->filters_list = $filters_list;

		$filter_view->render(TRUE);
	}
}
new filter;
?>
