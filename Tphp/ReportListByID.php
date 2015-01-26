<?php
    include "mysql_connect.php";

	$strSqlCommand = "SELECT incident_id, JSONValue FROM incident_phone WHERE incident_id = '".$_GET['id']."'";

	$result = mysql_query($strSqlCommand);

	$reportAry = array();

	while($rowDetail = @mysql_fetch_array($result) )
	{
		$Items = $rowDetail[1];
	}

	echo $Items;

?>
