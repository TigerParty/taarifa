<?php

    include "mysql_connect.php";

    $catIdandPid = array();
    $Category = array();
    $thirdCategory = array();

    $strSqlCommand = "SELECT id, category_title, parent_id FROM category
                      WHERE category_trusted != 1
                      AND parent_id = 0
                      AND category_visible = 1";

    $result = mysql_query($strSqlCommand);
    while($rowDetail = @mysql_fetch_array($result) )
    {
        array_push($catIdandPid,array("id"=>$rowDetail[0],"title"=>$rowDetail[1]));
    }

    //-- set $Category array
    $strSqlCommand = "SELECT id, category_title, parent_id FROM category
                      WHERE category_trusted != 1
                      AND category_visible = 1";
    $result = mysql_query($strSqlCommand);
    while($rowDetail = @mysql_fetch_array($result) )
    {
        array_push($Category, array(
            "idCate"        => $rowDetail[0],
            "nameCate"      => $rowDetail[1],
            "idParent"      => $rowDetail[2],
            "hasSub"        => 0
        ));
    }

    //-- loop category^2 to set 'hasSub' and 'children_id', and prevent to query db
    foreach($Category as $i => $category_this)
    {
        foreach($Category as $category_child)
        {
           if($category_this['idCate'] == $category_child['idParent'])
           {
               $Category[$i]['hasSub'] = 1;
           }
        }
    }

    $strSqlCommand = "SELECT id, form_title FROM form WHERE 1";
    $result = mysql_query($strSqlCommand);

    $FormList = array();
    while($rowForm = @mysql_fetch_array($result))
    {
        $strSqlCommand = "SELECT tb1.id, tb1.field_name, tb1.field_required, tb1.field_type, tb2.option_value , tb1.field_default
                          FROM form_field tb1
                          LEFT JOIN form_field_option tb2 ON tb2.form_field_id = tb1.id
                          WHERE tb1.form_id = ".$rowForm[0];

        $resultField = mysql_query($strSqlCommand);
        $FieldList = array();
        
        while($rowField = @mysql_fetch_array($resultField))
        {
            if($rowField[4] == "0")
            continue;

            switch ($rowField[3])
            {
                case '1':
                    $fieldtype = 'textfield';
                    break;
                case '2':
                    $fieldtype = 'textarea';
                    break;
                case '3':
                    $fieldtype = 'date';
                    break;
                case '4':
                    $fieldtype = 'password';
                    break;
                case '5':
                    $fieldtype = 'radio';
                    break;
                case '6':
                    $fieldtype = 'checkbox';
                    break;
                case '7':
                    $fieldtype = 'spinner';
                    break;
            }
            array_push($FieldList, array( "Field_ID" => $rowField[0], "Field_Name" => $rowField[1], "Required" => $rowField[2], "Field_Type" => $fieldtype, "Field_DataType" => $rowField[4],"Field_Default"=>$rowField[5] ));
        }
        array_push($FormList, array( "idForm" => $rowForm[0], "nameForm" => $rowForm[1], "rowData" => $FieldList));
    }

    //For_APK_Group
    $strSqlCommand4 = "SELECT id, GGroup_Name FROM GGroup WHERE 1";
    $result4 = mysql_query($strSqlCommand4);
    $FormList4 = array();
    array_push($FormList4, array( "idGroup" => '0', "nameGroup" => 'No Group'));
    while($rowGroup4 = mysql_fetch_array($result4))
    {
        array_push($FormList4, array( "idGroup" => $rowGroup4['id'], "nameGroup" => $rowGroup4['GGroup_Name']));
    }
    $Items = array( 'CategoriesList' =>$Category , 'FormList' => $FormList,'catIdandPid'=>$catIdandPid ,'FormList4' => $FormList4,'thirdCategory' => $thirdCategory);
    
    echo json_encode($Items);
?>
