<?php

include("../mysql_connect.php");

//reports view
$category_name = $_GET['category_name'];
$category_description = $_GET['category_description'];
$category_color = $_GET['category_color'];
$category_select = $_GET['category_select'];


if($category_select == 0)
{
	$strSqlCommand1 = "INSERT INTO `category` (`category_title`,`category_description`,`category_color`) VALUES ('".$category_name."','".$category_description."','".$category_color."');";
	$result1 = mysql_query($strSqlCommand1);
}
else
{
	$strSqlCommand2 = "INSERT INTO `category` (`parent_id`,`category_title`,`category_description`,`category_color`) VALUES (".$category_select.",'".$category_name."','".$category_description."','".$category_color."');";
	$result2 = mysql_query($strSqlCommand2);
}

?>