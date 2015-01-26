<?php defined('SYSPATH') or die('No direct script access.');
/**
* FrontlineSMS HTTP Post Controller
* Gets HTTP Post data from a FrontlineSMS Installation
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   FrontlineSMS Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 
   @ohNO!	Massively modified based off core modules by fligg. here be dragons. on acid.
*/

class esendex_Controller extends Controller
{
	function index()
	{
		$xmlstr = file_get_contents('php://input');
		$messagein = new SimpleXMLElement($xmlstr);
		$message_from 	= $messagein->From;
		$message_to	= $messagein->To;
		$message_txt	= $messagein->MessageText;


		sms::add($message_from, "$message_txt");
		
		
	}
}
