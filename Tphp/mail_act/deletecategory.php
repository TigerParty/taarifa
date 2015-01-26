<?php
    include '../../application/config/mysql_connect.php';

    //-- return 406 if category_id not exists
    if( !isset($_GET['category_id'] ) )
    {
        error_log("Error 406: GET data check error: " . __FILE__);
        http_response_code(406);
        exit;
    }

    //-- recursive deleteing function
    function delete_category_rec($category_id)
    {
        $sql = "SELECT id FROM `category` WHERE `parent_id` = " . mysql_real_escape_string($category_id);
        $query = mysql_query($sql);

        //-- access children
        while( $row = mysql_fetch_array($query) )
        {
            delete_category_rec($row[0]);
        }

        //-- delete this category's lv1 children
        $sql = "DELETE FROM `category` WHERE `parent_id` = " . mysql_real_escape_string($category_id);
        // $sql = "UPDATE `category` SET `category_type` = 0 WHERE `parent_id` = " . mysql_real_escape_string($category_id);
        $query = mysql_query($sql);
    }

    //-- call recursive function
    $category_id = $_GET['category_id'];
    delete_category_rec($category_id);

?>
