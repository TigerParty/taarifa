<?php
//performs install/uninstall methods for the Filter Plugin

class Filter_Install {
	
	//constructor to load the shared database library
	public function __construct()
	{
		$this->db = new Database();
	}	

	//creates the required database tables for the Filter module
	public function run_install()
	{
		//create the database tables and include the table_prefix
		$this->db->query('CREATE TABLE IF NOT EXISTS `'.Kohana::config('database.default.table_prefix').'filter` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`constituency_name` VARCHAR(50) NOT NULL,
				PRIMARY KEY (id)
			);');
		
		$this->db->query('CREATE TABLE IF NOT EXISTS `'.Kohana::config('database.default.table_prefix').'filter_incident_constituency` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`constituency_id` INT UNSIGNED NOT NULL,
				`incident_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (id)
			);');

		//Dump the constituency names from bundled SQL dump file
		$db_insert = fopen (dirname(dirname(__FILE__)).'/sql/filter.sql', 'r');

		$rows = fread ($db_insert, filesize (dirname(dirname(__FILE__)).'/sql/filter.sql'));

		//split by ; to get the sql statement for inserting each row
		$rows = explode(';\n',$rows);

		foreach($rows as $query) {
			$this->db->query($query);
		}
	}	

	 /**
	 * Deletes the database tables for the actionable module
	 */
	 public function uninstall()
	 {
	 	$this->db->query('DROP TABLE `'.Kohana::config('database.default.table_prefix').'filter');

		$this->db->query('DROP TABLE `'.Kohana::config('database.default.table_prefix').'filter_incident_constituency');
	 }
}
?>
