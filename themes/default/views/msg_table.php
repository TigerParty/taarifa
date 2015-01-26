<?php
$msgs=ORM::factory('message')->where('message_type','1')
			->orderby('message_date', 'desc')
			->find_all();
?>

<script src="/themes/default/views/table/js/jquery.dataTables.min.js" language="javascript" type="text/javascript"></script>
<style title="currentStyle" type="text/css">
@import "/themes/default/views/table/css/demo_page.css";
@import "/themes/default/views/table/css/jquery.dataTables.css";

#msg_table1 { 
  width: 100%; 
  background-color:white; 
  
}
 


</style>
<script charset="utf-8" type="text/javascript">
     
      
      
	$(document).ready(function() {
	
		$('#msg_table1').dataTable(
		 {	"bFilter": false,
		 	"bLengthChange": false,
		 	"sPaginationType": "full_numbers"
		        
        		
		  } 	
		
		);
		//do something...maybe...
		
		
		
		
	} );//end ready
	
    </script>
<div id="msg_table" style="width: 706px;">
   
<table style="border-collapse:collapse;width:706" id="msg_table1" >
<thead>
		<tr style="background-color:#999999;font-weight: bold;">
			
			<th>
				<p style="color:white">Message Details</p>
			</th>
			<th>
				<p style="color:white">Date</p>
			</th>
			
		</tr>
	</thead>
<tbody id="tbody_cool">
	<?php foreach($msgs as $msg){?>
	
		<tr >
			
			
			<td style="background-color:white;border:thin solid #999999;border-right-width:0px;">
				<p style='color: rgb(85, 85, 85);'><?php echo $msg->message;?></p>
				<br><br>
				<?php echo "<p style='font-size:10px;color: rgb(85, 85, 85);'>From: <a style='font-size:10px;color:red;'>".$msg->message_from.'</a></p>';?>
				
			</td>
			
			<td style="background-color:white;border: thin solid #999999;border-left-width:0px;">
				<?php echo 
				 $new_date = date("Y-m-d", strtotime($msg->message_date));
				 ;?>
			</td>
			
		</tr>
		
	<?php }?>
	</tbody>
</table>
</div>
<br><br>