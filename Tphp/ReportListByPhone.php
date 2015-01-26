<?php
    include "mysql_connect.php";

	$strSqlCommand = "SELECT ic1.incident_id, ic1.phoneNumber, ic1.JSONValue ,ic2.incident_title, ic2.incident_date, ic2.form_id FROM incident_phone ic1
        LEFT JOIN incident ic2 ON ic1.incident_id = ic2.id
        WHERE phoneNumber = '".$_GET['phone']."'";

	$result = mysql_query($strSqlCommand);

	$reportAry = array();
	$FieldList = array();

	while($rowDetail = @mysql_fetch_array($result) )
	{
		$FieldList = [];

		array_push($FieldList, $rowDetail[2]);

		array_push($reportAry,array("id"=>$rowDetail[0], "phoneNumber"=>$rowDetail[1],"reportJSON"=> $FieldList, "reportTitle"=>$rowDetail[3], "reportDate"=>$rowDetail[4], "formID"=>$rowDetail[5] ));
	}

	$Items = array( 'ReportList' =>$reportAry  );

	echo json_encode($Items);

?>
