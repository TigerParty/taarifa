<?php

    include("mysql_connect.php");
    error_reporting(E_ALL ^ E_NOTICE);

    $rep = $_POST['Report'];
    $json = str_replace('\"', '"', $_POST['Report'] );

    $objReport = json_decode($json);

    $SqlCommandForEmail = 'SELECT `id` FROM `users` WHERE `email` = "'.$objReport->user->email.'"';
    $resultMail = mysql_query($SqlCommandForEmail);
    $resultMailEach = mysql_fetch_array($resultMail);
    $Mail_rows = mysql_num_rows($resultMail);

    if(isset($objReport))
    {
        $position = $objReport->location->lat . "," . $objReport->location->lng;

        $strSqlCommand = 'UPDATE `location`
            SET  `location_name` = "'.$position.'",
            `latitude` = "'.$objReport->location->lat.'",
            `longitude` = "'.$objReport->location->lng.'"
            WHERE `id` = "'.$objReport->mainReport->ID.'"';

        if(!mysql_query($strSqlCommand))
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
            //Distinct by enter right email let user can view report
            if($Mail_rows == 0)
            {
                $strSqlCommand = 'UPDATE `incident`
                    SET `incident_title` =  "'.$objReport->mainReport->Title.'" ,
                    `incident_description` = "'.$objReport->mainReport->Descript.'" ,
                    `incident_date` = "'.$objReport->mainReport->ReportDate.'"
                    WHERE `id` = "'.$objReport->mainReport->ID.'"';
            }
            else
            {
                $strSqlCommand = 'UPDATE `incident`
                    SET `incident_title` =  "'.$objReport->mainReport->Title.'" ,
                    `incident_description` = "'.$objReport->mainReport->Descript.'" ,
                    `incident_date` = "'.$objReport->mainReport->ReportDate.'"
                    WHERE `id` = "'.$objReport->mainReport->ID.'"';
            }

            if(!mysql_query($strSqlCommand))
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
                $strSqlCommand = 'UPDATE `incident`
                    SET `incident_title` =  "'.$objReport->mainReport->Title.'" ,
                    `incident_description` = "'.$objReport->mainReport->Descript.'" ,
                    `incident_date` = "'.$objReport->mainReport->ReportDate.'" ,
                    `ggroup_id` = "'.$objReport->mainReport->idGroup.'"
                    WHERE `id` = "'.$objReport->mainReport->ID.'"';
            }
            else
            {
                $strSqlCommand = 'UPDATE `incident`
                    SET `incident_title` =  "'.$objReport->mainReport->Title.'" ,
                    `incident_description` = "'.$objReport->mainReport->Descript.'" ,
                    `incident_date` = "'.$objReport->mainReport->ReportDate.'" ,
                    `ggroup_id` = "'.$objReport->mainReport->idGroup.'"
                    WHERE `id` = "'.$objReport->mainReport->ID.'"';
            }

            if(!mysql_query($strSqlCommand))
            {
                echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
                error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );
                error_log( $strSqlCommand."\n", 3, "upload-errors.log" );

                exit;
            }
        }

        //Get New Media ID
        $incident_id = mysql_insert_id();

        foreach ((array)$objReport->additionReport as $Data)
        {
            $strSqlCommand = 'DELETE FROM `form_response`
                WHERE `incident_id` = "'.$objReport->mainReport->ID.'"';

            if (!mysql_query($strSqlCommand))
            {
                echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
                error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );
                error_log( $strSqlCommand."\n", 3, "upload-errors.log" );

                exit;
            }
        }

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

        $strSqlCommand = 'UPDATE `incident_person`
            SET `person_first` = "'.$objReport->user->firstName.'" ,
            `person_last` = "'.$objReport->user->lastName.'" ,
            `person_email` = "'.$objReport->user->email.'"
            WHERE `incident_id` = "'.$objReport->mainReport->ID.'"';
        if(!mysql_query($strSqlCommand))
        {
            echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
            error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );
            error_log( $strSqlCommand."\n", 3, "upload-errors.log" );

            exit;
        }

        //save SimCardNo
        $strSqlCommand2 = 'DELETE FROM incident_phone
            WHERE incident_id = "'.$objReport->mainReport->ID.'"';

        if(!mysql_query($strSqlCommand2))
        {
            echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
            error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );
            error_log( $strSqlCommand2."\n", 3, "upload-errors.log" );

            exit;
        }

        $strSqlCommand3 = 'INSERT INTO incident_phone (incident_id, phoneNumber, JSONValue)
            VALUES ("'.$objReport->mainReport->ID.'", "'.$objReport->mainReport->SimCardSn.'", "'.mysql_real_escape_string($rep).'")';

        if (!mysql_query($strSqlCommand3))
        {
            echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
            error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );
            error_log( $strSqlCommand3."\n", 3, "upload-errors.log" );

            exit;
        }

        foreach ((array)$objReport->categoriesList as $Data)
        {
            $strSqlCommand4 = 'DELETE FROM incident_category
                WHERE incident_id = "'.$objReport->mainReport->ID.'"';

            if (!mysql_query($strSqlCommand4))
            {
                echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
                error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );
                error_log( $strSqlCommand4."\n", 3, "upload-errors.log" );

                exit;
            }
        }

        foreach((array)$objReport->categoriesList as $Data)
        {
            $strSqlCommand = 'DELETE FROM `incident_category`
                WRERE `incident_id` = "'.$objReport->mainReport->ID.'"';

            $strSqlCommand5 = 'INSERT INTO incident_category (incident_id, category_id )
                VALUES ("'.$objReport->mainReport->ID.'", '.$Data.')';

            if(!mysql_query($strSqlCommand5))
            {
                echo '<Error Message="'.mysql_error().'" Domain="UserUpload" />';
                error_log( "[".date("Y-m-d H:i:s")."] : ".mysql_error()."\n", 3, "upload-errors.log" );
                error_log( $strSqlCommand5."\n", 3, "upload-errors.log" );

                exit;
            }
        }

        $Items = array('incident_id' => $incident_id, 'location_id' => $location_id);
        $Items = str_replace('\"', '"', $Items);

        echo json_encode($Items);
    }
?>