<?php
    include "mysql_connect.php";

    header("Content-type: text/javascript");

    $_GET['Field'] = "12";
    $nRange = 2;

    // Get Max Min Time for Report
    $strSqlCommand = "	SELECT MAX( form_response ), MIN( form_response )
        FROM form_response
        WHERE form_field_id = ".$_GET['Field'];

    $resultField = mysql_query($strSqlCommand);
    $rowField = @mysql_fetch_array($resultField);

    for( $i = $rowField[1]-$nRange ; $i <= $rowField[0] ; $i+= $nRange)
    {
        // Step 2.
        // get Count of Category:
        $strSqlCommand = "	SELECT Count(*)
        FROM form_response
        WHERE form_field_id = ".$_GET['Field']."
        AND form_response > ".$i * $nRange."
        AND form_response <= ".($i+1) * $nRange;

        $resultCount = mysql_query($strSqlCommand);
        $rowCount = @mysql_fetch_array($resultCount);
        $Items["data"][ $i ] = array( "id" => $i, "cate_name" => $i * $nRange . " ~ ".($i+1) * $nRange."" , "counter" => $rowCount[0]);
    }
    var_dump( $Items["data"] );

?>

