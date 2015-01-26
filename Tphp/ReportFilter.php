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

    // Step 1.
    // Search all Form that Reports using:
	$strSqlCommand = "	SELECT incident.form_id
						FROM incident, incident_category, category
						WHERE incident.id = incident_category.incident_id
						AND incident_category.category_id = category.id
						AND category.category_trusted = 0
						AND category.category_visible = 1
						GROUP BY incident.form_id";

	$resultForm = mysql_query($strSqlCommand);
    $Items = array();
	while($rowForm = @mysql_fetch_array($resultForm) )
	{

    // Step 2.
    // Search All Form_Field Title From "Form"
		$strSqlCommand = '	SELECT form_field.id, form_field.form_id, form_field.field_name
							FROM form_field, form_field_option
							WHERE form_field.form_id = '.$rowForm['form_id'].'
							AND form_field.id = form_field_option.form_field_id
							AND form_field_option.option_name = "field_datatype"
							AND form_field_option.option_value = "numeric"';

		$resultField = mysql_query($strSqlCommand);

		while($rowField = @mysql_fetch_array($resultField) )
		{
			$Items[ $rowField[0] ] = array( "id" => $rowField[0], "form_id" => $rowField[1], "field_name" => $rowField[2]);
		}
	}
    echo json_encode($Items);

?>
