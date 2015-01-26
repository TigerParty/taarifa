<?php
	//----------------------------------------------------------
	// Sample for Link To MySQL
	// Author  : Lance Kuo
	// Created : 2012/07/13
	// Version : V1.0.0.0
	// Note    : My Model for mySQL Connect  
	// History : 2012/07/13 Created by Lance Version 1.
	//----------------------------------------------------------


    //MySQL Setting

    //SQL Server Address
    //$db_server = "107.22.182.14";
    //Test Server Address

    $db_server = "localhost";

    //Database Name
    $db_name = "";

    //Administrator Account And password
    $db_user = "";
    $db_passwd = "";

    //connect my_sql
    $link = mysql_connect($db_server, $db_user, $db_passwd);
    if(!@mysql_connect($db_server, $db_user, $db_passwd))
        die("Can't Link to Database Server!");

    //select Database
    if(!@mysql_select_db($db_name,$link))
            die("Can't select this database!");
?> 
