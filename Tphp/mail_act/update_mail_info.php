<?PHP
include("../mysql_connect.php");
	
$incident_id=  $_GET['incident_id'];
$every =$_GET['every'];	
		

$strSqlCommand ="UPDATE mail_info SET every='".$every."' WHERE incident_id=".$incident_id;
	
if(mysql_query($strSqlCommand)){
	echo "UPDATE SUCCESS!";


}else{
	echo "Sorry!";

}
?>