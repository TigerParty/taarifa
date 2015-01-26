<?php
// Test Variables - assign values accordingly:
#$to = 447788161913;
#$from = 07555444111;
#message = "this is my gorram message";

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
?>
