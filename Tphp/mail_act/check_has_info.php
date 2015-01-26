<?php
include("../mysql_connect.php");
	
$incident_id=  $_GET['incident_id'];
$every =$_GET['every'];	
		



$count=getDataCount($incident_id);

if($count==0){
		
		
}else{
	print_r($result);
	//while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	//	printf("ID: %s  Name: %s", $row["id"], $row["incident_id"]);
	//}
}	


function getDataCount($id){
		$strSqlCommand = "SELECT * FROM mail_info Where incident_id='".$id."';";
		$result = mysql_query($strSqlCommand);
		return mysql_num_rows($result);
	}
	
function addData($id,$every){
		$strSqlCommand = "INSERT INTO mail_info (incident_id,,...)
				VALUES (value1, value2, value3,...)";
		$result = mysql_query($strSqlCommand);
		return mysql_num_rows($result);
	}

echo $count;
											

?>