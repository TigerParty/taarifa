<?php
    include("../mysql_connect.php");
    include_once ('PHPMailer/class.phpmailer.php');
     
    $incident_id=  $_GET['incident_id'];
    $mail_to=$_GET['mail_to'];
    $msg=  $_GET['msg'];
    $sub=$_GET['sub'];	
    $type=$_GET['type'];
     
    if($type=='0')
    {
        $strSqlCommand = "INSERT INTO mail_info (incident_id,msg,mail_to,sub,time)".
            "VALUES ('".$incident_id."','".$msg."','".$mail_to."','".$sub."','".date("Y-m-d H:i:s",strtotime("now"))."');";
                    
        $strSqlCommand_email_send = "UPDATE `incident` SET  `email_send` =  '1' WHERE  `incident`.`id` = ".$incident_id;
                    
        if(mysql_query($strSqlCommand)){
            mysql_query($strSqlCommand_email_send);
            sendmail($mail_to, $sub, $msg);
            echo "Sucess,please choose a set on 'Send reminders every'. ";
                
        }else{
            echo "Save error!";
        }

    }else if($type=='1'){

        $strSqlCommand ="UPDATE mail_info SET ".
            "  msg     ='".$msg.
            "',mail_to ='".$mail_to.
            "',sub     ='".$sub.
            "',time    ='".date("Y-m-d H:i:s",strtotime("now")).
            "' WHERE incident_id=".$incident_id;
        
            $strSqlCommand_email_send = "UPDATE `incident` SET  `email_send` =  '1' WHERE  `incident`.`id` =".$incident_id;
        
            if(mysql_query($strSqlCommand)){
                
                mysql_query($strSqlCommand_email_send);
                
                sendmail($mail_to, $sub, $msg);
                
                echo "UPDATE SUCCESS!"; 
            }else{
                echo "Sorry!";
            
            }

    }	
                                                

                                                
    function sendmail($mail_to, $sub, $msg){
        
        $mail=null;
        $mail = new PHPMailer;

        $mail->IsSMTP();                                      
        $mail->Host = "localhost";   
        $mail->SMTPAuth = true;                           
        $mail->Username = 'louis@ghdistrictsmonitor.org';                            
        $mail->Password = 'goghana';     
        $mail->SMTPDebug = 0;                        
            
        $mail->From = 'admin@ghdistrictsmonitor.org';
        $mail->FromName = 'Ghana Districts Monitor';
            
        $toArray = explode(',',$mail_to);  
        for($index=0;$index<count($toArray);$index++){ 
            $mail->AddAddress($toArray[$index]);
        }  
            
        $mail->WordWrap = 50;                
        $mail->IsHTML(true);                                  // Set email format to HTML
            
        $mail->Subject = $sub;
        $mail->Body    = $msg;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
        if(!$mail->Send()) {
                         
                           exit;
        }else{
                            $f=fopen("../logs/mail_log.html","ab");
                            fwrite($f,"[send time:".date("Y-m-d H:i:s",strtotime( $row['time']))."-Now".date("Y-m-d H:i:s",strtotime("now"))."-to ".$row['mail_to']."--DAY send ok]<br>");
                            fclose($f);
        } 
    }

?>
