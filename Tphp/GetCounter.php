<?php
    header("Content-type: text/javascript");
    include "mysql_connect.php";

    //category: id, parent_id, category_title, category_color, category_visible = 1, category_trusted = 0
    //incident_category: incident_id, category_id
    //incident: id, form_id

    //form: id, form_active = 1
    //form_field:id, form_id, field_name, field_required
    //form_response: id, form_field_id, incident_id, form_respons
    //form_field_option: form_field_id, option_name = field_datatype, option_value = numeric

    // Search for each report
    if( $_GET['Type'] == 'Scatter' )
    {
        // Search For X Axis:


            $Items = array();

            $strSqlCommand = "SELECT `id` , `form_field_id` , `incident_id` , `form_response`
                              FROM `form_response`
                              WHERE `form_field_id`=".$_GET['Par'];

            $resultField = mysql_query($strSqlCommand);


            while($rowField = @mysql_fetch_array($resultField))
            {
                $Items["data"][$rowField[0]] = array( "id" => $rowField[0], "form_field_id" => $rowField[1], "form_response" => $rowField[3]);
            }
    }
    // Make Reports Data to be groups
    else
    {
        // Search For X Axis:

        // Search for Counter for each Time.
        if( !isset( $_GET['Par'] ) || $_GET['Par'] == -2 )
        {
            // Step 1.

            // Get Max Min Time for Report
            $strSqlCommand = "	SELECT  MIN( incident_date ),MAX( incident_date )
                                FROM incident
                                WHERE 1";

            $resultField = mysql_query($strSqlCommand);
            $rowField = @mysql_fetch_array($resultField);

            // Get Search Range Time
            switch( $_GET['Range'] )
            {
                case "Year":
                    $nStart = date('Y', strtotime( $rowField[0]) )."-01-01";
                    $nEnd =  (date('Y', strtotime( $rowField[1]) )+1)."-01-01";
                    $strRange = "year";
                    break;
                case "Month":
                    $nStart = date('Y-m-d', strtotime(date('Y-m', strtotime( $rowField[0]) )));
                    $nEnd =   date('Y-m-d', strtotime(date('Y-m', strtotime( $rowField[1]) ). "+1 month"));
                    $strRange = "month";
                    break;
                case "Day":
                    $nStart = date('Y-m-d', strtotime( $rowField[0]) );
                    $nEnd =   date('Y-m-d', strtotime(date('Y-m-d', strtotime( $rowField[1]) ). "+1 day"));
                    $strRange = "day";
                    break;
            }
            $Items = array();
            $Items[ 'tittle' ] = array( "tittle" => "Time");
            for( $Time = $nStart ; strtotime($Time) < strtotime($nEnd) ; $Time = $nextTime )
            {
                $nextTime = date('Y-m-d', strtotime($Time. "+1 ".$strRange ));

                // Step 2.
                // get Count of Category:
                $strSqlCommand = "	SELECT Count(*)
                                    FROM incident
                                    WHERE incident_date
                                    BETWEEN '".$Time."'
                                    AND '".$nextTime."'";

                $resultCount = mysql_query($strSqlCommand);

                $rowCount = @mysql_fetch_array($resultCount);

                if( $rowCount[0] > 0 )
                    $Items["data"][ strtotime($Time) ] = array( "id" => strtotime($Time), "cate_name" => $Time, "counter" => $rowCount[0]);
            }
        }
        // Search for Counter for each range of field.
        else if( !isset( $_GET['Par'] ) || $_GET['Par'] == -1 )
        {
            // Step 1.
            // Search all Category:
            $strSqlCommand = "	SELECT tbParent.id, tbParent.category_title, tbChild.id, tbChild.category_title
                                FROM category tbParent
                                LEFT JOIN category tbChild ON tbChild.parent_id = tbParent.id
                                WHERE tbParent.parent_id = 0
                                AND tbParent.category_trusted = 0
                                AND tbParent.category_visible = 1
                                AND tbParent.parent_id = 0
                                AND tbParent.category_trusted = 0
                                AND tbParent.category_visible = 1";

            $resultCate = mysql_query($strSqlCommand);

            $Items = array();
            $Items[ 'tittle' ] = array( "tittle" => "Category");
            while($rowCate = @mysql_fetch_array($resultCate) )
            {
                // Step 2.
                // get Count of Category:
                $strSqlCommand = "	SELECT Count(*)
                                    FROM incident_category";

                if( $rowCate[2] == NULL)
                {
                    $strSqlCommand .= " WHERE category_id = ". $rowCate[0];
                }
                else
                {
                    $strSqlCommand .= " WHERE category_id = ". $rowCate[2];
                }

                $resultCount = mysql_query($strSqlCommand);

                $rowCount = @mysql_fetch_array($resultCount);

                if( $rowCate[2] == NULL)
                    $Items["data"][ $rowCate[0] ] = array( "id" => $rowCate[0], "cate_name" => $rowCate[1], "counter" => $rowCount[0]);
                else
                    $Items["data"][ $rowCate[2] ] = array( "id" => $rowCate[2], "cate_name" => $rowCate[3], "counter" => $rowCount[0]);
            }
        }
        // Search for Counter for each range of field.
        else
        {
            // Step 1.
            // Get field Name
            $strSqlCommand = "	SELECT field_name
                                FROM form_field
                                WHERE id = ".$_GET['Par'];
            $resultName = mysql_query($strSqlCommand);

            $rowName = @mysql_fetch_array($resultName);

            $Items = array();
            $Items[ 'tittle' ] = array( "tittle" => $rowName[0]);

            // Search all Category:
            //-- nRange must > 0, or the for loop below won't stop!!!
            $nRange = ( (int)$_GET['Range']>0 ) ? (int)$_GET['Range'] : 1;

            if( strpos( $rowName['field_name'], "%" ) )
            {
                for( $i = 0 ; $i * $nRange < 100 ; $i++ )
                {
                    // Step 2.
                    // get Count of Category:
                    $strSqlCommand = "	SELECT Count(*)
                                        FROM form_response
                                        WHERE form_field_id = ".(int)$_GET['Par']."
                                        AND form_response > ".$i * $nRange."
                                        AND form_response <= ".($i+1) * $nRange;

                    $resultCount = mysql_query($strSqlCommand);
                    $rowCount = @mysql_fetch_array($resultCount);
                    $Items["data"][ $i ] = array( "id" => $i, "cate_name" => $i * $nRange . "% ~ ".($i+1) * $nRange."%" , "counter" => $rowCount[0]);
                }
            }
            else
            {
                $strSqlCommand = "	SELECT MAX( form_response ), MIN( form_response )
                                    FROM form_response
                                    WHERE form_field_id = ".$_GET['Par'];

                $resultField = mysql_query($strSqlCommand);
                $rowField = @mysql_fetch_array($resultField);

                for( $i = (int)$rowField[1]-$nRange ; $i <= (int)$rowField[0] ; $i+= $nRange)
                {
                    // Step 2.
                    // get Count of Category:
                    $strSqlCommand = "	SELECT Count(*)
                                        FROM form_response
                                        WHERE form_field_id = ".(int)$_GET['Par']."
                                        AND form_response > ".$i."
                                        AND form_response <= ".($i + $nRange);

                    $resultCount = mysql_query($strSqlCommand);
                    $rowCount = @mysql_fetch_array($resultCount);
                    $Items["data"][ $i ] = array( "id" => $i, "cate_name" => $i. " ~ ".($i + $nRange) , "counter" => $rowCount[0]);
                }
            }
        }
    }

    echo json_encode($Items);


?>

