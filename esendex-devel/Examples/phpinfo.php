<?php

$myFile = "/home/caz/Hosted/water/htdocs/Ushahidi_Web/esendex/Examples/phpinfofromserver.html";
$xmlstr = file_get_contents('php://input');

#phpinfo();
#PHPInfo2File($myFile);

$messagein = new SimpleXMLElement($xmlstr);

print "messageID: $messagein->Id[0]\n";
print "from: $message_from 	= $messagein->From[0]\n";
print "message_to: $messagein->To[0]\n";
print message_txt: $messagein->MessageText[0]\n";


function PHPInfo2File($target_file){
 
    ob_start();
    phpinfo();
    $info = ob_get_contents();
    ob_end_clean();
 	 
   $xml = file_get_contents('php://input');
    $fp = fopen($target_file, "w+");

	fwrite($fp, $xml);
    fclose($fp);
}

