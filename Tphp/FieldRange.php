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

    // Search for Counter for each Cateogry.
    if( !isset( $_GET['Field'] ) )
    {
        $Items[ 'data' ] = 0;
    }
    else
    {
        // Step 1.
        // Search Field is % ?:
        $strSqlCommand = "	SELECT field_name
                            FROM form_field
                            WHERE id = ".$_GET['Field'];

        $resultField = mysql_query($strSqlCommand);
        $rowField = @mysql_fetch_array($resultField);

        if( strpos( $rowField['field_name'], "%" ) )
        {
            $Items[ 'data' ] = 100;
        }
        else
        {
            // Step 2.
            $strSqlCommand = "	SELECT MAX( form_response ), MIN( form_response )
                                FROM form_response
                                WHERE form_field_id = ".$_GET['Field'];

            $resultField = mysql_query($strSqlCommand);
            $rowField = @mysql_fetch_array($resultField);

            $Items[ 'data' ] = $rowField[0] - $rowField[1];
        }
    }
    echo json_encode($Items);

?>
