<?php defined('SYSPATH') or die('No direct script access.');

/**   -MASSIVELY HACKED BY FLIGG. SORRY!
 * Helper library for the Clickatell API
 *
 * @package     Clickatell SMS 
 * @category    Plugins
 * @author      Ushahidi Team
 * @copyright   (c) 2008-2011 Ushahidi Team
 * @license     http://www.gnu.org/copyleft/lesser.html GNU Less Public General License (LGPL)
 */
include_once( "EsendexSendService.php" ); 

class Clickatell_Sms_Provider implements Sms_Provider_Core {
	
	/**
	 * Sends a text message (SMS) using the Clickatell API
	 *
	 * @param string $to
	 * @param string $from
	 * @param string $to
	 */
	public function send($to = NULL, $from = NULL, $message = NULL)
	{
		//hardcode our stuff
// Test Variables - assign values accordingly:
$username = "the@midnightoker.co.uk";			// Your Username (normally an email address).
$password = "NDX2573";			// Your Password.
$accountReference = "EX0084087";		// Your Account Reference (either your virtual mobile number, or EX account number).
$originator = "Taarifa";		// An alias that the message appears to come from (alphanumeric characters only, and must be less than 11 characters).
#$recipients = "";		// The mobile number(s) to send the message to (comma-separated).
#$body = "";			// The body of the message to send (must be less than 160 characters).
$type = "Text";			// The type of the message in the body (e.g. Text, SmartMessage, Binary or Unicode).
$validityPeriod = 0;		// The amount of time in hours until the message expires if it cannot be delivered.
$result;			// The result of a service request.
$messageIDs = array();		// A single or comma-separated list of sent message IDs.
$messageStatus;			// The status of a sent message.

		// Instantiate the service with login credentials.
		$sendService = new EsendexSendService( $username, $password, $accountReference );

		$result = $sendService->SendMessageFull( $originator, $to, $message, $type, $validityPeriod );

		return true;



/**
		// Get Current Clickatell Settings
		$clickatell = ORM::factory("clickatell", 1)->find();
		
		if ($clickatell->loaded)
		{
			// Create Clickatell Object
			$new_sms = new Clickatell_API();
			$new_sms->api_id = $clickatell->clickatell_api;
			$new_sms->user = $clickatell->clickatell_username;
			$new_sms->password = $clickatell->clickatell_password;
			$new_sms->use_ssl = false;
			$new_sms->sms();
			$response = $new_sms->send($to, $from, $message);
			
			// Message Went Through??
			return ($response == "OK")? TRUE : $response;
		}
		
		return "Clickatell Is Not Set Up! -even worse, this is fligg's clicktell bodge so you should never see this.";

*/

	}
	
}
