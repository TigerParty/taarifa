<!--TTP Plugin google chart-->
<div style="width:707px; height:410px;margin-top:11px;">
	<div id="chartreport">
        <div class="title">Visualize Report Data</div>
        <div id="selecter" style="width:100%;height:350px;">
        	<div style="float:left;">
            	<table>
                	<tr style="height:110px;vertical-align:top;">
                    	<td><img src="<?php echo url::file_loc('img'); ?>media/img/A02.png" style="margin:5px;"/></td>
                        <td style="padding-top:8px;"><label style="font-size:120%">Select Chart Type</label>
                        	<br/>
                            
                            <span class="sleBG" style="margin-top:10px;">   
                            <span class="sleHid">   
                            <select id="Chart_selecter" class="selector" >
                                <option selected="selected" value="Column">Column</option>
                                <option value="Bar">Bar</option>
                                <option value="Pie">Pie</option>
                                <option value="Scatter">Scatter</option>
                            </select>
                            </span>   
                            </span> 
                        </td>
                    </tr>
                    <tr style="vertical-align:top;">
                    	<td><img src="<?php echo url::file_loc('img'); ?>media/img/A03.png" style="margin:5px;"/></td>
                        <td style="padding-top:8px;"><label style="font-size:120%;">Customize Data</label>
                        	<br/><br/>
                            <label style="font-size:100%;color:#999999;" id="lbl_yaxis">Y-Axis:</label>
                        	<span class="sleBG" style="margin-top:5px">   
                            <span class="sleHid">   
                            <select id="YAxis_selecter" class="selector">
                                <option selected="selected" value="Count">Count of Report</option>
                            </select>
                            </span>   
                            </span>
                            
                            <br/>
                            <label style="font-size:100%;color:#999999" id="lbl_xaxis">X-Axis:</label>
                        	<span class="sleBG" style="margin-top:5px">   
                            <span class="sleHid">   
                            	<select id="XAxis_selecter" class="selector"></select>
                            </span>   
                            </span>  
                            
                            <div class="XRange" style="display:none;float:left;margin-top:5px;">
                            	<label style="font-size:100%;color:#999999" id="lbl_xrange">X-Range:</label> 
                            
                             	<span class="sleBG" style="margin-top:5px">   
                                <span class="sleHid">   
                                	<select id="XRange_selecter" class="selector"></select>
                                </span>   
                                </span>  
                            </div>
                        	
                                 
                        </td>
                    </tr>
                </table>
            </div>
            <div style="float:left;width:100px;height:50px;">
            	<img src="<?php echo url::file_loc('img'); ?>media/img/A04.png" style="padding:50px 20px;"/>
            </div>
            <div style="float:left;width:100px;height:50px;"></div>
            	<div id="chart_div" style="width:100%;position:relative;top:50px;left:290px;"></div>
        	</div>
        
     </div>
</div>
<!--End of TTP Plugin google chart-->
<div class="slider-holder" style="display:none;">
	<form action="">
		<input type="hidden" value="0" name="currentCat" id="currentCat"/>
		<fieldset>
			<div class="play" id="playTimeline" style="cursor:pointer;float:right;width:80px;background-image:url(<?php echo url::file_loc('img'); ?>media/img/A06.png)"></div>

			<label style="color: #999999;padding:10px 10px 0 0;float:left;" for="startDate"><?php echo Kohana::lang('ui_main.from'); ?>:</label>
			<span class="sleBG" style="float:left;">   
            <span class="sleHid">   
                 <select name="startDate" id="startDate" class="selector" ><?php echo $startDate; ?></select>
            </span>   
            </span>
			
            <label style="color: #999999;padding:10px 10px;float:left;" for="endDate"><?php echo Kohana::lang('ui_main.to'); ?>:</label>
            
            <span class="sleBG" style="float:left;">   
            <span class="sleHid">   
                <select name="endDate" id="endDate" class="selector" ><?php echo $endDate; ?></select>
            </span>   
            </span>
            
			<select name="endDate" id="endDate" style="display:none;"><?php echo $endDate; ?></select>
		</fieldset>
	</form>
</div>
<div id="graph" class="graph-holder" style="display:none;"></div>