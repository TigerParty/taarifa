<?php
if ($_GET['randomId'] != "oiixS0dpzDOEMVotp6WRmqNxTUY2p_BNjiIhIxDw9Pvhe2Q1tyk3bHyxcM6RknTD") {
    echo "Access Denied";
    exit();
}

// display the HTML code:
echo stripslashes($_POST['wproPreviewHTML']);

?>  
