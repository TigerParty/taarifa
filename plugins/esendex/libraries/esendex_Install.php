<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Performs install/uninstall methods for the Esendex Plugin
 *
 * @package    Esendex Plugin
 * @author     Daniel Fligg
 * @copyright  (c) 2011 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 *
 * @notes	Vastly modified by fligg based on the base plugins to support esendex sms api
 */
class Esendex_Install {
	
	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db =  new Database();
	}

	/**
	 * Creates the required columns for the Esendex Plugin
	 */
	public function run_install()
	{
		
		// ****************************************
		// DATABASE STUFF
		// Settings Table
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."esendex`
			(
				id int(11) unsigned NOT NULL AUTO_INCREMENT,
				esendex_secret varchar(100) DEFAULT NULL,
				esendex_api varchar(100) DEFAULT NULL,
				esendex_username varchar(100) DEFAULT NULL,
				esendex_password varchar(100) DEFAULT NULL,
				PRIMARY KEY (`id`)
			);
		");
		
		// Our messages table
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".Kohana::config('database.default.table_prefix')."esendex_messages`
			(
				myid int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Internal ID used for indexing messages',
				esendex_fullid  varchar(50) NOT NULL COMMENT 'Full Esendex message ID',
				esendex_originator varchar(100) NOT NULL COMMENT 'Who sent us the SMS',
				esendex_recipient varchar(100) NOT NULL COMMENT 'SMS Sent To (useful for account tracking I guess)',
				esendex_body varchar(100) NOT NULL COMMENT 'message body',
				esendex_receivedat varchar(100) NOT NULL COMMENT 'ReceivedAT Esendexes stamping',
				esendex_type varchar(20) DEFAULT NULL COMMENT 'SMS Type Field',
				esendex_indexid varchar(15) NOT NULL COMMENT 'esendex IndexID -used for requesting messages after',
				PRIMARY KEY (`myid`)
			);
		");
		// ****************************************
	}

	/**
	 * Drops the Esendex Tables
	 */
	public function uninstall()
	{
		$this->db->query("
			DROP TABLE ".Kohana::config('database.default.table_prefix')."esendex;
			DROP TABLE ".Kohana::config('database.default.table_prefix')."esendex_messages;
			");
	}
}
