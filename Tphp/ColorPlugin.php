<?php

    include "mysql_connect.php";

	$strSqlCommand = "SELECT tb0.incident_id, tb1.category_color, tb2.category_color FROM incident_category tb0
						LEFT JOIN category tb1 ON tb0.category_id = tb1.id
						LEFT JOIN category tb2 ON tb1.parent_id = tb2.id";
							
	$result = mysql_query($strSqlCommand);
//	$rowDetail = @mysql_fetch_array($result);	

$Items = array();
	while($rowDetail = @mysql_fetch_array($result) )	
	{
		$Items[ $rowDetail[0] ] = array( "id" => $rowDetail[0], "MyColor" => $rowDetail[1], "ParentColor" => $rowDetail[2]);
	}
	
	echo json_encode($Items);
	
?>	
	
