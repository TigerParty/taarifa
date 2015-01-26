<?php
include("../mysql_connect.php");
	
$incident_id=  $_GET['incident_id'];
	
$query = "select * from mail_info where incident_id='".$incident_id."';";
												   	
$mails= mysql_query($query );
$row=@mysql_fetch_array($mails,MYSQL_ASSOC);
$json=json_encode($row);									
echo $json;
?>