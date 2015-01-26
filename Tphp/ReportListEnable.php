<?php

    include "mysql_connect.php";

    if( isset( $_POST['id'] ) )
    {
        if($_POST['enable'] == 1){
            $enable = 0;
        }else{
            $enable = 1;
        }

        $strSqlCommand ="UPDATE incident SET enable ='".$enable."' WHERE id = '".$_POST['id']."'";

        mysql_query($strSqlCommand);

    }

?>
