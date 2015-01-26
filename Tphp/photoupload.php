<?php

ini_set('memory_limit', '96M');
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');

require_once('mysql/mysql.class.php');
 
$target_path = "../media/uploads/";
$target_path = $target_path . $_POST["filename"] . '.'  . $_POST["extname"];

$req_dump = print_r( $target_path, TRUE);

$target_filename   = $_POST["filename"] . '.'  . $_POST["extname"];
$target_filename_t = $_POST["filename"] . 't.' . $_POST["extname"];
$target_filename_m = $_POST["filename"] . 'm.' . $_POST["extname"];

$MysqlObj = new SQLObj(); 
$MysqlObj->connect();

try
{
  move_uploaded_file($_FILES['image0']['tmp_name'], $target_path);

  $percent = 5/8;
  $percentSmall = 1/8;
      
  AddTimeStamp($target_path, $_POST["Date"], $_POST["location_id"]);
  ResizeImage($target_path,$percent, substr($target_path,0,strlen($target_path)-4)      . 't.' . $_POST["extname"]);
  ResizeImage($target_path,$percentSmall, substr($target_path,0,strlen($target_path)-4) . 'm.' . $_POST["extname"]);    

  SaveMedia($MysqlObj,$target_filename,$target_filename_t,$target_filename_m,$_POST["Date"], $_POST['incident_id'], $_POST["location_id"]);

}
catch(Exception $e)
{

  $req_dump = $e;
  $fp = fopen('upload_error.log', 'a');
  fwrite($fp, $req_dump);
  fclose($fp);
  print_r($e);
}

function SaveMedia($MysqlObj , $target_filename,$target_filename_t,$target_filename_m,$Date, $idReport, $idLocation )
{    
  $strSQL = sprintf("insert into media(media_type,media_link,media_medium,media_thumb,media_date, incident_id, location_id )values(1,'%s','%s','%s','%s', '%s', '%s')",
                         $target_filename, $target_filename_t, $target_filename_m, $Date, $idReport, $idLocation);
  $MysqlObj->RunSQL($strSQL);         
}     

function ResizeImage($filename,$percent,$target_filename)
{  
  list($width, $height) = getimagesize($filename);
  $new_width = $width * $percent;
  $new_height = $height * $percent;
  $image_p = imagecreatetruecolor($new_width, $new_height);
  $image = imagecreatefromjpeg($filename);
  imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
  imagejpeg($image_p, $target_filename , 100);
}

function AddTimeStamp($filename, $time, $location_id)
{
  list($width, $height) = getimagesize($filename);
  $image = imagecreatefromjpeg($filename);

  $white = imagecolorallocate($image, 255, 255, 255);
  $black = imagecolorallocate($image, 0, 0, 0);

  $timeStamp = timeFilter($time);
  $locationStamp = locationFilter($location_id);
  
  #-- For local testing 
  // $fontWidth = 40/2;
  // $timeFontSize = 60/2;
  // $locationFontSize = 30/2;

  $fontWidth = 40;
  $timeFontSize = 60;
  $locationFontSize = 30;

  $timeStamp_X = $width - (strlen($timeStamp) * $fontWidth);
  $timeStamp_Y = $height - 10;
  $locationStamp_X = $width - (strlen($timeStamp) * $fontWidth);
  $locationStamp_Y = $height - ($timeFontSize + $locationFontSize);

  $font = 'Arial.ttf';
  imagettftext($image, $timeFontSize, 0, $timeStamp_X, $timeStamp_Y, $white, $font, $timeStamp);
  imagettftext($image, $locationFontSize, 0, $locationStamp_X, $locationStamp_Y, $white, $font, $locationStamp);
  imagejpeg($image, $filename);
}
function timeFilter($time)
{
  if(strlen($time) > 20 )
  {
    $time = '0000-00-00 00:00:00';
  }
  return $time;
}
function locationFilter($location_id)
{
  $SqlCommandForLocation = 'Select `latitude`, `longitude` from `location` where `id` = "'.$location_id.'"';
  $resultLocation = mysql_query($SqlCommandForLocation);
  $resultLocationEach = mysql_fetch_array($resultLocation);
  if(!empty($resultLocationEach))
  {
    #-- Filter number after point 5 digits
    $latitude = intval(floatval($resultLocationEach['latitude']) * 100000) / 100000;
    $longitude = intval(floatval($resultLocationEach['longitude']) * 100000) / 100000;
    $locationStamp = 'Lat: '.$latitude.', Lng: '.$longitude;
    return $locationStamp;
  }
  else
  {
    $fp = fopen('upload_error.log', 'w');
    fwrite($fp, 'Can not get Location by id of location :'.$location_id );
    fclose($fp);
    return 'Lat: -, Lng: -';
  }
}
?>