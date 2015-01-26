<?php

    include "mysql_connect.php";
    error_reporting(E_ALL ^ E_NOTICE);

    $rep = $_POST['Report'];
    $json = str_replace('\"', '"', $_POST['Report'] );

    $req_dump = print_r( $json, TRUE);
    $fp = fopen('FULL_Request'.date("Y_m_d_H_i_s").'.log', 'a');
    fwrite($fp, $req_dump);
    fclose($fp);

    //-- doing works
    $objReport = json_decode($json);

    $SqlCommandForEmail = 'Select `id` from `users` where `email` = "'.$objReport->user->email.'"';
    $resultMail = mysql_query($SqlCommandForEmail);
    $resultMailEach = mysql_fetch_array($resultMail);
    $Mail_rows = mysql_num_rows($resultMail);

    if(isset($objReport))
    {
        $position = $objReport->location->lat . "," . $objReport->location->lng;

        $strSqlCommand = 'INSERT INTO location (location_name, latitude, longitude, location_visible )
                            VALUES ("'.$position.'", "'.$objReport->location->lat . '","' . $objReport->location->lng.'", 1)';
        if (!mysql_query($strSqlCommand))
        {
            echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
            error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );
            error_log( $strSqlCommand."\n", 3, "upload-errors.log" );

            exit;
        }

        // Get New Media ID
        $location_id = mysql_insert_id();

        if($objReport->mainReport->idGroup == 0)
        {
            //Insert a New Group to SQL and Change ggroup_id FROM incident table
            $InsertGroup  = "INSERT INTO `GGroup` (`GGroup_Name`) VALUES ('".$objReport->mainReport->Title."');";
            mysql_query($InsertGroup);

            $SelectGroupID  = "SELECT `id` FROM `GGroup` WHERE `GGroup_Name`='".$objReport->mainReport->Title."'";
            $results = mysql_query($SelectGroupID);

            while($fetch_results = mysql_fetch_array($results))
            {
                $group_id = $fetch_results['id'];
            }

            //Group Version For Report Title is New Group
            //distinct by enter right email let user can view report
            if($Mail_rows == 0)
            {
                $strSqlCommand = 'INSERT INTO incident (location_id, ggroup_id, form_id, user_id, incident_title, incident_description, incident_date, incident_dateadd, incident_zoom ) VALUES ("'.$location_id.'", "'.$group_id.'", "'.$objReport->mainReport->idForm . '", 1, "' . $objReport->mainReport->Title.'", "'.$objReport->mainReport->Descript.'", "'.$objReport->mainReport->ReportDate.'", "'.$objReport->mainReport->ReportDate.'", 6)';
            }
            else
            {
                $strSqlCommand = 'INSERT INTO incident (location_id, ggroup_id, form_id, user_id, incident_title, incident_description, incident_date, incident_dateadd, incident_zoom ) VALUES ("'.$location_id.'", "'.$group_id.'", "'.$objReport->mainReport->idForm . '", "'.$resultMailEach['id'].'", "' . $objReport->mainReport->Title.'", "'.$objReport->mainReport->Descript.'", "'.$objReport->mainReport->ReportDate.'", "'.$objReport->mainReport->ReportDate.'", 6)';
            }

            if (!mysql_query($strSqlCommand))
            {
                echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
                error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );
                error_log( $strSqlCommand."\n", 3, "upload-errors.log" );

                exit;
            }
        }
        else
        {
            //Group Version For Group DorpDown List
            //distinct by enter right email let user can view report
            if($Mail_rows == 0)
            {
                $strSqlCommand = 'INSERT INTO incident (location_id, ggroup_id, form_id, user_id, incident_title,
                    incident_description, incident_date, incident_dateadd, incident_zoom )
                    VALUES ("'.$location_id.'", "'.$objReport->mainReport->idGroup.'", "'.$objReport->mainReport->idForm . '", 1, "' . $objReport->mainReport->Title.'", "'.$objReport->mainReport->Descript.'", "'.$objReport->mainReport->ReportDate.'", "'.$objReport->mainReport->ReportDate.'", 6)';
            }
            else
            {
                $strSqlCommand = 'INSERT INTO incident (location_id, ggroup_id, form_id, user_id, incident_title, incident_description, incident_date, incident_dateadd, incident_zoom ) VALUES ("'.$location_id.'", "'.$objReport->mainReport->idGroup.'", "'.$objReport->mainReport->idForm . '", "'.$resultMailEach['id'].'", "' . $objReport->mainReport->Title.'", "'.$objReport->mainReport->Descript.'", "'.$objReport->mainReport->ReportDate.'", "'.$objReport->mainReport->ReportDate.'", 6)';
            }

            if (!mysql_query($strSqlCommand))
            {
                echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
                error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );
                error_log( $strSqlCommand."\n", 3, "upload-errors.log" );

                exit;
            }
        }

        // Get New Media ID
        $incident_id = mysql_insert_id();
        $array_temp_id = array();
        foreach ((array)$objReport->additionReport as $Data)
        {
            $form_field_id = $Data->id;

            $SelectFieldType  = "SELECT `field_type` FROM `form_field` WHERE `id`='".$form_field_id."'";
            $results = mysql_query($SelectFieldType);
            $fetch_results = mysql_fetch_array($results);
            $field_type_name = $fetch_results[0];

            if(isset($field_type_name))
            {
                //-- if field type is CheckBox, use update
                if($field_type_name == "6")
                {
                    if(in_array($form_field_id, $array_temp_id))
                    {
                        $strSqlCommandForResponse = 'UPDATE form_response
                                                     SET `form_response` = CONCAT(form_response, ",", "'.$Data->value.'") 
                                                     WHERE `form_field_id` = "'.$form_field_id.'" AND `incident_id` = "'.$incident_id.'"';
                        mysql_query($strSqlCommandForResponse);
                    }
                    else
                    {
                        array_push($array_temp_id, $form_field_id);
                        $strSqlCommand = 'INSERT INTO form_response (form_field_id, incident_id, form_response ) 
                                        VALUES ('.$form_field_id.', '.$incident_id . ', "'.$Data->value.'")';
                        if (!mysql_query($strSqlCommand))
                        {
                            echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
                            error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );  
                            error_log( $strSqlCommand."\n", 3, "upload-errors.log" );  
                                
                            exit;
                        } 
                    }
                }
                else
                {
                    $strSqlCommand = 'INSERT INTO form_response (form_field_id, incident_id, form_response ) 
                                    VALUES ('.$Data->id.', '.$incident_id . ', "'.$Data->value.'")';
                    if (!mysql_query($strSqlCommand))
                    {
                        echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
                        error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );  
                        error_log( $strSqlCommand."\n", 3, "upload-errors.log" );  
                            
                        exit;
                    }
                }
            }
        }

        $strSqlCommand = 'INSERT INTO incident_person (incident_id, location_id, person_first, person_last, person_email, person_date) VALUES ('.$incident_id.', '.$location_id.', "'.$objReport->user->firstName . '", "' . $objReport->user->lastName.'", "'.$objReport->user->email.'", "'.$objReport->mainReport->ReportDate.'")';

        if (!mysql_query($strSqlCommand))
        {
            echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
            error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );
            error_log( $strSqlCommand."\n", 3, "upload-errors.log" );

            exit;
        }

        //save SimCardNo
        $strSqlCommand = 'INSERT INTO incident_phone (incident_id, phoneNumber, JSONValue) VALUES ('.$incident_id.', "'.$objReport->mainReport->SimCardSn.'", "'.mysql_real_escape_string($rep).'")';
        if (!mysql_query($strSqlCommand))
        {
            echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
            error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );
            error_log( $strSqlCommand."\n", 3, "upload-errors.log" );

            exit;
        }


        foreach((array)$objReport->categoriesList as $Data)
        {
            $strSqlCommand = 'INSERT INTO incident_category (incident_id, category_id ) VALUES ('.$incident_id.', '.$Data.')';
            if(!mysql_query($strSqlCommand))
            {
                echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
                error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );
                error_log( $strSqlCommand."\n", 3, "upload-errors.log" );

                exit;
            }
        }

        $Items = array('incident_id' => $incident_id , 'location_id' => $location_id);
        $Items = str_replace('\"', '"', $Items);

        echo json_encode($Items);
    }
?>