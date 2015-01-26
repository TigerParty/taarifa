<?php
 
/**
 * Main cluster js file.
 * 
 * Server Side Map Clustering
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
$(document).ready(function() {
	$("#map").parent().height(450);
  // Handler for .ready() called.
});
 

// TTP Add Google Chart
    //alert("hello");
    //--------------------------Global Data----------------------------//
    var fieldData;
        //--------------------------Google Chart Data-----------//
    var ChartData;
    var ChartOptions;
    var ChartCanvas;
    var ScatterChartData;
	// Load the Visualization API and the piechart package.
	google.load('visualization', '1.0', {'packages':['corechart']});
    google.setOnLoadCallback(drawCB);
    function drawCB() 
    {
   /* 
        ChartData = new google.visualization.DataTable();
        ChartData.addColumn('string', 'Topping');
        ChartData.addColumn('number', 'Slices');
        ChartData.addRows([['Pepperoni', 2]]);

        ChartOptions = {'title': 'Taarifa',
        				'width':376,
                        'height':250
                       };
        ChartCanvas = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        ChartCanvas.draw(ChartData, ChartOptions);
    */    DrawChart();
    }
    
    //---------------http://www.ugtaarifa.org/-----------Draw Google Chart----------------------------//
    function DrawChart()
    {
        $.getJSON( location.href + 'Tphp/GetCounter.php?'
                    + 'Type='+$('#Chart_selecter').val()
                    +'&Par='+$('#XAxis_selecter').val()
                    +'&Range='+$('#XRange_selecter').val()
                    +"&Counter="+$('#YAxis_selecter').val(),
        'parameters/data',
        function(data)
        {
        //alert(JSON.stringify(data));
            // Build Data
            ChartData = new google.visualization.DataTable();
            var ScatterArr=Array();
            if( $('#Chart_selecter').val() == 'Scatter')
            {
                ScatterArr.push([$('#XAxis_selecter option:selected').text(),"Count of Report"]);
               
                 for( var i in data.data )
                {
                     //alert(JSON.stringify(data.data[i]));
                    //alert("a");
                   //ScatterChartData.addRows([[parseInt(data.data[i].cate_name), parseInt(data.data[i].counter)]]); 
                    ScatterArr.push([parseInt(data.data[i].form_response),parseInt(data.data[i].form_response)]);
                }
               
               
                ScatterChartData=google.visualization.arrayToDataTable(ScatterArr);
                //ScatterChartData.addColumn('number', data.tittle.tittleX);
                //ScatterChartData.addColumn('number', data.tittle.tittleY);
           //      for( var i in data.data )
           //      {
           //          ScatterChartData.addRows([[parseInt(data.data[i].cate_name), parseInt(data.data[i].counter)]]);
           //      }
                
           }
            else
            {
                ChartData.addColumn('string', data.tittle.tittle);//set type and val
                ChartData.addColumn('number', 'Count');
                for( var i in data.data )
                {
                    ChartData.addRows([[data.data[i].cate_name, parseInt(data.data[i].counter)]]);//x's ame and the count num
        
                }
            }					
            // Build Option & Draw Chart
            RedrawChart();
        });
    }
    function RedrawChart()
    {
        switch( $("#Chart_selecter").val() )
        {
            case "Column":
                ChartOptions = 
                {
                    'title': 'Taarifa',
               /*X*/'hAxis': {title: $('#XAxis_selecter option:selected').text(), titleTextStyle: {color: 'black'}},
               /*Y*/'vAxis': {title: $('#YAxis_selecter option:selected').text(), titleTextStyle: {color: 'black'},
                              viewWindowMode:'explicit',
                              viewWindow:{max:100,min:0}
                            },
                    'width':376,
                    'height':250

                };
                if($("#XAxis_selecter option:selected").val()==-1||$("#XRange_selecter").val()=="Day"){
                   ChartOptions.width=1000;
                   ChartOptions.height=500;
                   ChartOptions.hAxis.textStyle={ fontSize: 10};
                   ChartOptions.chartArea={top:0};
                   $("#selecter").css("overflow","auto");
                }

                ChartCanvas = new google.visualization.ColumnChart(document.getElementById("chart_div"));
                ChartCanvas.draw(ChartData, ChartOptions);
                break;
            case "Bar":
                ChartOptions = 
                {
                    title: 'Taarifa',
                    hAxis: {title: $('#YAxis_selecter option:selected').text(), titleTextStyle: {color: 'black'}},
                    vAxis: {title: $('#XAxis_selecter option:selected').text(), titleTextStyle: {color: 'black'}},
                    width: 376,
                    height: 250
                };
                if($("#XAxis_selecter option:selected").val()==-1||$("#XRange_selecter").val()=="Day"){
                   ChartOptions.width=1300;
                   ChartOptions.height=600;
                   ChartOptions.vAxis.textStyle={ fontSize: 10};
                   ChartOptions.chartArea={top:0};
                   $("#selecter").css("overflow","auto");
                }
                ChartCanvas = new google.visualization.BarChart(document.getElementById("chart_div"));
                ChartCanvas.draw(ChartData, ChartOptions);
                break;
            case "Pie":
                ChartOptions = 
                {
                    title: 'Taarifa' ,
                    is3D: true,
                    'width':376,
                    'height':250
                };
                ChartCanvas = new google.visualization.PieChart(document.getElementById("chart_div"));
                ChartCanvas.draw(ChartData, ChartOptions);
                break;
            case "Scatter":
             ChartOptions = 
                {
                    'width':376,
                    'height':250
                };
                ChartCanvas = new google.visualization.ScatterChart(document.getElementById("chart_div"));
                ChartCanvas.draw(ScatterChartData, ChartOptions);
                break;
        }
        
        
    }
    
    // TTP Plugin : Get Data & Draw Function
    function BuildSelect()
    {
    	//alert("aaa");
        BuildXaxis();
        BuildXRange();
        BuildYaxis();
    }
    
    function BuildXaxis()
    {
/*    
        if( $("#XAxis_selecter").val() == -2 || ( $("#Chart_selecter").val() == "Scatter" && $("#XAxis_selecter").val() == -1 ) )
            var strSelect = '<option selected="selected" value="-2">Time</option>';
        else
            var strSelect = '<option value="-2">Time</option>';
        
        if( $("#Chart_selecter").val() != "Scatter"  )
        {	
            if( $("#XAxis_selecter").val() == -1 )
                strSelect += '<option selected="selected" value="-1">Category</option>';
            else
                strSelect += '<option value="-1">Category</option>';
        }
        strSelect += '<option value="" disabled>----Data Field----</option>';
/*/
 	if( $("#Chart_selecter").val() != "Scatter" )
        {
            if( $("#XAxis_selecter").val() == -2 ){
                //alert("1");
                var strSelect = '<option selected="selected" value="-2">Time</option><option value="-1">Category</option>';
            }
                
            else if( $("#XAxis_selecter").val() == -1 ){
                //alert("2");
                var strSelect = '<option value="-2">Time</option><option selected="selected" value="-1">Category</option>';
            }
            else{
                //alert("3");
                var strSelect = '<option elected="selected" value="-2">Time</option><option value="-1">Category</option>';
            }
			
       	}
    else
        {
        	var strSelect = '<option value="" disabled>----Data Field----</option>';
       	      
//*/
            for(var i in fieldData)
            {
                if( $("#XAxis_selecter").val() == i )
                    strSelect += '<option value="'+i+'">'+fieldData[i].field_name+'</option>';
                else
                    strSelect += '<option value="'+i+'">'+fieldData[i].field_name+'</option>';
            }
        }
        $('#XAxis_selecter').html(strSelect);
    }
    function BuildXRange()
    {
        if( $("#Chart_selecter").val() != "Scatter" )
        {
            $('.XRange').css( "display", "block");
            switch( $("#XAxis_selecter").val() )
            {
                case "-2":
                    var strSelect = '<option selected="selected" value="Year">Year</option>'
                                  +	'<option value="Month">Month</option>'
                                  +	'<option value="Day">Day</option>';
                    $('#XRange_selecter').html(strSelect);
                    break;
                case "-1":
                    $('.XRange').css( "display", "none");
                    break;
                default://http://www.ugtaarifa.org/
                    //alert("df");
                    $.getJSON(	location.href + 'Tphp/FieldRange.php?Field=' + $("#XAxis_selecter").val(),
                                'parameters/data',
                                function(data)
                    {
                        if( data.data >= 30 )
                        {
                            var nRange = Math.floor(data.data / 10);
                            var strSelect = '<option selected="selected" value="'+ nRange +'">'+ nRange +'</option>';
                            for( var i = 9; i > 3; --i )
                            {
                                nRange = Math.floor(data.data / i);
                                strSelect += '<option value="'+ nRange +'">'+ nRange +'</option>'
                            }
                            $('#XRange_selecter').html(strSelect);
                        }
                        else if ( data.data > 0 )
                        {

                            var strSelect = '<option selected="selected" value="'+ 1 +'">'+ 1 +'</option>';
                            if( data.data > 2 )
                                strSelect += '<option selected="selected" value="'+ 2 +'">'+ 2 +'</option>';
                            if( data.data > 5 )
                                strSelect += '<option selected="selected" value="'+ 5 +'">'+ 5 +'</option>';
                            if( data.data > 10 )
                                strSelect += '<option selected="selected" value="'+ 10 +'">'+ 10 +'</option>';
                            if( data.data > 15 )
                                strSelect += '<option selected="selected" value="'+ 15 +'">'+ 15 +'</option>';
                            $('#XRange_selecter').html(strSelect);
                        }
                        else
                        {
                            $('.XRange').css( "display", "none");
                        }
                        DrawChart();
                    });
            }
        }
        else
        {
            $('.XRange').css( "display", "none");
        }
    }
    function BuildYaxis()
    {
       //if( $("#Chart_selecter").val() != "Scatter" )
        // {
            var strSelect = '<option selected="selected" value="-1">Count of Report</option>';
       //}
//         else
//         {

//             if( $("#XAxis_selecter").val() == -2 )
//             {
//                 var strSelect = '<option value="" disabled>Any</option>';
//             }
//             else if( $("#YAxis_selecter").val() < 0 || $("#YAxis_selecter").val() == $("#XAxis_selecter").val() )
//             {
//                 var strSelect = '<option selected="selected" value="-2">Time</option>';
//             }
//             else
//             {
//                 var strSelect = '<option value="-2">Time</option>';
//             }
// /*/
//              strSelect = '<option value="" disabled>Any</option>';
// //*/
//             for(var i in fieldData)
//             {
//                 if( i == $("#XAxis_selecter").val() )
//                 {
//                 }
//                 else if( $("#XAxis_selecter").val() >= 0 )
//                 {
//                     if( fieldData[ $("#XAxis_selecter").val() ].form_id == fieldData[i].form_id  )
//                     {
//                         if( i == $("#YAxis_selecter").val() )
//                         {
//                             strSelect += '<option selected="selected" value="'+i+'">'+fieldData[i].field_name+'</option>';
//                         }
//                         else
//                         {
//                             strSelect += '<option value="'+i+'">'+fieldData[i].field_name+'</option>';
//                         }
//                     }
//                 }
//                 else
//                 {
//                     if( i == $("#YAxis_selecter").val() )
//                     {
//                         strSelect += '<option selected="selected" value="'+i+'">'+fieldData[i].field_name+'</option>';
//                     }
//                     else
//                     {
//                         strSelect += '<option value="'+i+'">'+fieldData[i].field_name+'</option>';
//                     }
//                 }
//             }
//         }
        $('#YAxis_selecter').html(strSelect);
    }     
//TTP Plugin Global var for shift
    var tempMarkersLayerID;
    var arrayMarkersLayerID = new Array();
    var arrayMarkers2LayerID = new Array();
    var arrayClickID = new Array();;
    var ShiftKeyPress = false;
    var noShiftParent = true;
// End of TTP Plugin
//
		// Map JS

		// Map Object
		var map;
		// Selected Category
		var currentCat;
		// Selected Layer
		var thisLayer;
		// WGS84 Datum
		var proj_4326 = new OpenLayers.Projection('EPSG:4326');
		// Spherical Mercator
		var proj_900913 = new OpenLayers.Projection('EPSG:900913');
		// Change to 1 after map loads
		var mapLoad = 0;
		// /json or /json/cluster depending on if clustering is on
		var default_json_url = "<?php echo $json_url ?>";
		// Current json_url, if map is switched dynamically between json and json_cluster
		var json_url = default_json_url;
		
		/* 
		 - Part of #2168 fix
		 - Added by E.Kala <emmanuel(at)ushahidi.com>
		*/
		// Global list for current KML overlays in display
		var kmlOverlays = [];
		
		var baseUrl = "<?php echo url::site(); ?>";
		var longitude = <?php echo $longitude; ?>;
		var latitude = <?php echo $latitude; ?>;
		var defaultZoom = <?php echo $default_zoom; ?>;
		var markerRadius = <?php echo $marker_radius; ?>;
		var markerOpacity = "<?php echo $marker_opacity; ?>";
		var selectedFeature;
		var allGraphData = "";
		var dailyGraphData = "";
		var gMediaType = 0
		var timeout = 1500;
		
		var activeZoom = null;

		var gMarkerOptions = {
			baseUrl: baseUrl, longitude: longitude,
			latitude: latitude, defaultZoom: defaultZoom,
			markerRadius: markerRadius,
			markerOpacity: markerOpacity,
			protocolFormat: OpenLayers.Format.GeoJSON
		};
							
		/*
		Create the Markers Layer
		*/
		function addMarkers(catID,startDate,endDate, currZoom, currCenter,
			mediaType, thisLayerID, thisLayerType, thisLayerUrl, thisLayerColor)
		{
			activeZoom = currZoom;
// TTP Plugin addMarkers Add catID for time delay bug			
			if(activeZoom == ''){
				return $.timeline({categoryId: catID,
		                   startTime: new Date(startDate * 1000),
		                   endTime: new Date(endDate * 1000),
						   mediaType: mediaType
						  }).addMarkers(
							startDate, endDate, gMap.getZoom(),
							gMap.getCenter(), thisLayerID, thisLayerType, 
							thisLayerUrl, thisLayerColor, json_url, catID);
			}
			
			setTimeout(function(){
				if(currZoom == activeZoom){
					return $.timeline({categoryId: catID,
		                   startTime: new Date(startDate * 1000),
		                   endTime: new Date(endDate * 1000),
						   mediaType: mediaType
						  }).addMarkers(
							startDate, endDate, gMap.getZoom(),
							gMap.getCenter(), thisLayerID, thisLayerType, 
							thisLayerUrl, thisLayerColor, json_url, catID);
				}else{
					return true;
				}
			}, timeout); 
		}
		
		/*
		Create the Markers2 background Layer
		*/
		function addMarkers2(catID,startDate,endDate, currZoom, currCenter,
			mediaType, thisLayerID, thisLayerType, thisLayerUrl, thisLayerColor)
		{
			activeZoom = currZoom;
// TTP Plugin addMarkers Add catID for time delay bug			
			if(activeZoom == ''){
				return $.timeline({categoryId: catID,
		                   startTime: new Date(startDate * 1000),
		                   endTime: new Date(endDate * 1000),
						   mediaType: mediaType
						  }).addMarkers2(
							startDate, endDate, gMap.getZoom(),
							gMap.getCenter(), thisLayerID, thisLayerType, 
							thisLayerUrl, thisLayerColor, json_url, catID);
			}
			
			setTimeout(function(){
				if(currZoom == activeZoom){
					return $.timeline({categoryId: catID,
		                   startTime: new Date(startDate * 1000),
		                   endTime: new Date(endDate * 1000),
						   mediaType: mediaType
						  }).addMarkers2(
							startDate, endDate, gMap.getZoom(),
							gMap.getCenter(), thisLayerID, thisLayerType, 
							thisLayerUrl, thisLayerColor, json_url, catID);
				}else{
					return true;
				}
			}, timeout); 
		}
		
		/**
		 * Display loader as Map Loads
		 */
		function onMapStartLoad(event)
		{
			if ($("#loader"))
			{
				$("#loader").show();
			}

			if ($("#OpenLayers\\.Control\\.LoadingPanel_4"))
			{
				$("#OpenLayers\\.Control\\.LoadingPanel_4").show();
			}
		}

		/**
		 * Hide Loader
		 */
		function onMapEndLoad(event)
		{	
			if ($("#loader"))
			{
				$("#loader").hide(); 
			}

			if ($("#OpenLayers\\.Control\\.LoadingPanel_4"))
			{
				$("#OpenLayers\\.Control\\.LoadingPanel_4").hide();
                  
			}
		}

		/**
		 * Close Popup
		 */
		function onPopupClose(event)
		{
			selectControl.unselect(selectedFeature);
			selectedFeature = null;
		}

		/**
		 * Display popup when feature selected
		 */
		function onFeatureSelect(event)
		{
			selectedFeature = event.feature;
			zoom_point = event.feature.geometry.getBounds().getCenterLonLat();
			lon = zoom_point.lon;
			lat = zoom_point.lat;
			
			var thumb = "";
			if ( typeof(event.feature.attributes.thumb) != 'undefined' && 
				event.feature.attributes.thumb != '')
			{
				thumb = "<div class=\"infowindow_image\"><a href='"+event.feature.attributes.link+"'>";
				thumb += "<img src=\""+event.feature.attributes.thumb+"\" height=\"59\" width=\"89\" /></a></div>";
			}

			var content = "<div class=\"infowindow\">" + thumb;
			content += "<div class=\"infowindow_content\"><div class=\"infowindow_list\">"+event.feature.attributes.name+"</div>";
			content += "\n<div class=\"infowindow_meta\">";
			if ( typeof(event.feature.attributes.link) != 'undefined' &&
				event.feature.attributes.link != '')
			{
				content += "<a href='"+event.feature.attributes.link+"'><?php echo Kohana::lang('ui_main.more_information');?></a><br/>";
			}
			
			content += "<a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +",1)'>";
			content += "<?php echo Kohana::lang('ui_main.zoom_in');?></a>";
			content += "&nbsp;&nbsp;|&nbsp;&nbsp;";
			content += "<a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +",-1)'>";
			content += "<?php echo Kohana::lang('ui_main.zoom_out');?></a></div>";
			content += "</div><div style=\"clear:both;\"></div></div>";		

			if (content.search("<?php echo '<'; ?>script") != -1)
			{
				content = "Content contained Javascript! Escaped content below.<br />" + content.replace(/<?php echo '<'; ?>/g, "&lt;");
			}
            
			// Destroy existing popups before opening a new one
			if (event.feature.popup != null)
			{
				map.removePopup(event.feature.popup);
			}
			
			popup = new OpenLayers.Popup.FramedCloud("chicken", 
				event.feature.geometry.getBounds().getCenterLonLat(),
				new OpenLayers.Size(100,100),
				content,
				null, true, onPopupClose);

			event.feature.popup = popup;
			map.addPopup(popup);
		}

		/**
		 * Destroy Popup Layer
		 */
		function onFeatureUnselect(event)
		{
			// Safety check
			if (event.feature.popup != null)
			{
				map.removePopup(event.feature.popup);
				event.feature.popup.destroy();
				event.feature.popup = null;
			}
		}

		// Refactor Clusters On Zoom
		// *** Causes the map to load json twice on the first go
		// *** Need to fix this!
		function mapZoom(event)
		{
			// Prevent this event from running on the first load
			if (mapLoad > 0)
			{
				// Get Current Category
				currCat = $("#currentCat").val();

				// Get Current Start Date
				currStartDate = $("#startDate").val();

				// Get Current End Date
				currEndDate = $("#endDate").val();

				// Get Current Zoom
				currZoom = map.getZoom();

				// Get Current Center
				currCenter = map.getCenter();

				// Refresh Map
				addMarkers(currCat, currStartDate, currEndDate, currZoom, currCenter);
			}
		}

		function mapMove(event)
		{
			// Prevent this event from running on the first load
			if (mapLoad > 0)
			{
				// Get Current Category
				currCat = $("#currentCat").val();

				// Get Current Start Date
				currStartDate = $("#startDate").val();

				// Get Current End Date
				currEndDate = $("#endDate").val();

				// Get Current Zoom
				currZoom = map.getZoom();

				// Get Current Center
				currCenter = map.getCenter();
				
				// Part of #2168 fix
				// Remove the KML overlays
				if (kmlOverlays.length > 0)
				{
					for (var i = 0; i < kmlOverlays.length; i++)
					{
						map.removeLayer(kmlOverlays[i]);
					}
				}
				
				// Refresh Map
				addMarkers2(currCat, currStartDate, currEndDate, currZoom, currCenter, gMediaType);
				addMarkers(currCat, currStartDate, currEndDate, currZoom, currCenter, gMediaType);
				
				
				// Part of #2168 fix
				// E.Kala <emmanuel(at)ushahidi.com>
				// Add back the KML overlays
				
				/* 
				  - The timout is so that the cluster markers are given time to load before
				  - the overlays can be rendered
				*/
				setTimeout(
					function()
					{
						if (kmlOverlays.length > 0)
						{
							for (var i = 0; i < kmlOverlays.length; i++)
							{
								kmlItem = kmlOverlays[i];
								map.addLayer(kmlItem);
								
								// Add feature selection events to the last item
								if (i == kmlOverlays.length -1)
								{
									selectControl = new OpenLayers.Control.SelectFeature(kmlItem);
									map.addControl(selectControl);
									selectControl.activate();
									kmlItem.events.on({
										"featureselected": onFeatureSelect,
										"featureunselected": onFeatureUnselect
									});
								}
								
							}
						}
					},
					timeout
				);
			}
		}
		
		/**
		 * Display info window for checkin data
		 */
		function showCheckinData(event)
		{
			selectedFeature = event.feature;
			zoom_point = event.feature.geometry.getBounds().getCenterLonLat();
			lon = zoom_point.lon;
			lat = zoom_point.lat;
			
			var content = "<div class=\"infowindow\" style=\"color:#000000\"><div class=\"infowindow_list\">";
			
			if(event.feature.attributes.ci_media_medium !== "")
			{
				content += "<a href=\""+event.feature.attributes.ci_media_link+"\" rel=\"lightbox-group1\" title=\""+event.feature.attributes.ci_msg+"\">";
				content += "<img src=\""+event.feature.attributes.ci_media_medium+"\" /><br/>";
			}

			content += event.feature.attributes.ci_msg+"</div><div style=\"clear:both;\"></div>";
			content += "\n<div class=\"infowindow_meta\">";
			content += "<a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +",1)'><?php echo Kohana::lang('ui_main.zoom_in');?></a>";
			content += "&nbsp;&nbsp;|&nbsp;&nbsp;";
			content += "<a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +",-1)'><?php echo Kohana::lang('ui_main.zoom_out');?></a></div>";
			content += "</div>";

			if (content.search("<?php echo '<'; ?>script") != -1)
			{
				content = "Content contained Javascript! Escaped content below.<br />" + content.replace(/<?php echo '<'; ?>/g, "&lt;");
			}
		 
			popup = new OpenLayers.Popup.FramedCloud("chicken", 
					event.feature.geometry.getBounds().getCenterLonLat(),
					new OpenLayers.Size(100,100),
					content,
					null, true, onPopupClose);
					
			event.feature.popup = popup;
			map.addPopup(popup);
		}

		/**
		 * Display Checkin Points
		 * Note: This function totally ignores the timeline
		 */
		function showCheckins()
		{
			$(document).ready(function(){

				var ci_styles = new OpenLayers.StyleMap({
					"default": new OpenLayers.Style({
						pointRadius: "5", // sized according to type attribute
						fillColor: "${fillcolor}",
						strokeColor: "${strokecolor}",
						fillOpacity: "${fillopacity}",
						strokeOpacity: 0.75,
						strokeWidth: 1.5,
						graphicZIndex: 1
					})
				});

				var checkinLayer = new OpenLayers.Layer.Vector('Checkins', {styleMap: ci_styles});
				map.addLayers([checkinLayer]);

				highlightCtrl = new OpenLayers.Control.SelectFeature(checkinLayer, {
				    hover: true,
				    highlightOnly: true,
				    renderIntent: "temporary"
				});
				map.addControl(highlightCtrl);
				highlightCtrl.activate();
				
				selectControl = new OpenLayers.Control.SelectFeature(checkinLayer);
				map.addControl(selectControl);
				selectControl.activate();
				checkinLayer.events.on({
					"featureselected": showCheckinData,
					"featureunselected": onFeatureUnselect
				});

				$.getJSON("<?php echo url::site()."api/?task=checkin&action=get_ci&mapdata=1&sqllimit=1000&orderby=checkin.checkin_date&sort=ASC"?>", function(data) {
					var user_colors = new Array();
					// Get colors
					$.each(data["payload"]["users"], function(i, payl) {
						user_colors[payl.id] = payl.color;
					});

					// Get checkins
					$.each(data["payload"]["checkins"], function(key, ci) {

						var cipoint = new OpenLayers.Geometry.Point(parseFloat(ci.lon), parseFloat(ci.lat));
						cipoint.transform(proj_4326, proj_900913);

						var media_link = '';
						var media_medium = '';
						var media_thumb = '';

						if(ci.media === undefined)
						{
							// No image
						}
						else
						{
							// Image!
							media_link = ci.media[0].link;
							media_medium = ci.media[0].medium;
							media_thumb = ci.media[0].thumb;
						}

						var checkinPoint = new OpenLayers.Feature.Vector(cipoint, {
							fillcolor: "#"+user_colors[ci.user],
							strokecolor: "#FFFFFF",
							fillopacity: ci.opacity,
							ci_id: ci.id,
							ci_msg: ci.msg,
							ci_media_link: media_link,
							ci_media_medium: media_medium,
							ci_media_thumb: media_thumb
						});

						checkinLayer.addFeatures([checkinPoint]);
					});
				});
			});			
		}

		/**
		 * Refresh Graph on Slider Change
		 */
		function refreshGraph(startDate, endDate)
		{
			var currentCat = gCategoryId;
			
			// refresh graph
			if (!currentCat || currentCat == '0')
			{
				currentCat = '0';
			}

			var startTime = new Date(startDate * 1000);
			var endTime = new Date(endDate * 1000);

			// daily
			var graphData = "";

			// plot hourly incidents when period is within 2 days
			if ((endTime - startTime) / (1000 * 60 * 60 * 24) <?php echo '<'; ?>= 3)
			{
				$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat+"?i=hour", function(data) {
					graphData = data[0];

					gTimeline = $.timeline({categoryId: currentCat,
						startTime: new Date(startDate * 1000),
					    endTime: new Date(endDate * 1000), mediaType: gMediaType,
						markerOptions: gMarkerOptions,
						graphData: graphData
					});
					gTimeline.plot();
				});
			} 
			else if ((endTime - startTime) / (1000 * 60 * 60 * 24) <?php echo '<'; ?>= 124)
			{
			    // weekly if period > 2 months
				$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat+"?i=day", function(data) {
					graphData = data[0];

					gTimeline = $.timeline({categoryId: currentCat,
						startTime: new Date(startDate * 1000),
					    endTime: new Date(endDate * 1000), mediaType: gMediaType,
						markerOptions: gMarkerOptions,
						graphData: graphData
					});
					gTimeline.plot();
				});
			} 
			else if ((endTime - startTime) / (1000 * 60 * 60 * 24) > 124)
			{
				// monthly if period > 4 months
				$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat, function(data) {
					graphData = data[0];

					gTimeline = $.timeline({categoryId: currentCat,
						startTime: new Date(startDate * 1000),
					    endTime: new Date(endDate * 1000), mediaType: gMediaType,
						markerOptions: gMarkerOptions,
						graphData: graphData
					});
					gTimeline.plot();
				});
			}

			// Get dailyGraphData for All Categories
			$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat+"?i=day", function(data)
            {
				dailyGraphData = data[0];
//                GoogleChartDraw( dailyGraphData );
			});

			// Get allGraphData for All Categories
			$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat, function(data) {
				allGraphData = data[0];
			});
		}

		/**
		 * Zoom to Selected Feature from within Popup
		 */
		function zoomToSelectedFeature(lon, lat, zoomfactor)
		{
			var lonlat = new OpenLayers.LonLat(lon,lat);

			// Get Current Zoom
			currZoom = map.getZoom();
			// New Zoom
			newZoom = currZoom + zoomfactor;
			// Center and Zoom
			map.setCenter(lonlat, newZoom);
			// Remove Popups
			for (var i=0; i<?php echo '<'; ?>map.popups.length; ++i)
			{
				map.removePopup(map.popups[i]);
			}
		}
		
		/*
		Zoom to Selected Feature from outside Popup
		*/
		function externalZeroIn(lon, lat, newZoom, cipopup)
		{
			
			var point = new OpenLayers.LonLat(lon,lat);
			point.transform(proj_4326, map.getProjectionObject());
			// Center and Zoom
			map.setCenter(point, newZoom);
			
			if (cipopup === undefined) 
			{
				// A checkin id was not passed so we won't bother showing the info window
			}
			else
			{
				// An id was passed, so lets show an info window
				// TODO: Do this.
			}
		}

		/*
		Add KML/KMZ Layers
		*/
		function switchLayer(layerID, layerURL, layerColor)
		{
			if ( $("#layer_" + layerID).hasClass("active") )
			{
				new_layer = map.getLayersByName("Layer_"+layerID);
				if (new_layer)
				{
					for (var i = 0; i <?php echo '<'; ?> new_layer.length; i++)
					{
						map.removeLayer(new_layer[i]);
					}
					
					// Part of #2168 fix
					// Added by E.Kala <emmanuel(at)ushahidi.com>
					// Remove the layer from the list of KML overlays - kmlOverlays
					if (kmlOverlays.length == 1)
					{
						kmlOverlays.pop();
					}
					else if (kmlOverlays.length > 1)
					{
						// Temporarily store the current list of overlays
						tempKmlOverlays = kmlOverlays;
						
						// Re-initialize the list of overlays
						kmlOverlays = [];
						
						// Search for the overlay that has just been removed from display
						for (var i = 0; i < tempKmlOverlays.length; i ++)
						{
							if (tempKmlOverlays[i].name != "Layer_"+layerID)
							{
								kmlOverlays.push(tempKmlOverlays[i]);
							}
						}
						// Unset the working list
						tempKmlOverlays = null;
					}
				}
				$("#layer_" + layerID).removeClass("active");

			}
			else
			{
				$("#layer_" + layerID).addClass("active");

				// Get Current Zoom
				currZoom = map.getZoom();

				// Get Current Center
				currCenter = map.getCenter();
				
				// Add New Layer
				addMarkers2('', '', '', currZoom, currCenter, '', layerID, 'layers', layerURL, layerColor);
				addMarkers('', '', '', currZoom, currCenter, '', layerID, 'layers', layerURL, layerColor);
			}
		}

		/**
		 * Toggle Layer Switchers
		 */
		function toggleLayer(link, layer)
		{	
        	currCenter = map.getCenter();
        	 
			if ($("#"+link).text() == "<?php echo Kohana::lang('ui_main.show'); ?>")
			{
				$("#"+link).text("<?php echo Kohana::lang('ui_main.hide'); ?>");
                $("#map").width(707);
                
                 $("#map").parent().height(450);
                map.setCenter(new OpenLayers.LonLat(currCenter), 6);
			}
			else
			{
				$("#"+link).text("<?php echo Kohana::lang('ui_main.show'); ?>");
                $("#map").width(996); 
                 $("#map").parent().height(450);
                map.setCenter(new OpenLayers.LonLat(currCenter), 6);
			}
            
			$('#'+layer).toggle(1000);
           
		}
		
		
		/**
		 * Create a function that calculates the smart columns
		 */
		function smartColumns()
		{
			//Reset column size to a 100% once view port has been adjusted
			$("ul.content-column").css({ 'width' : "100%"});

			var colWrap = $("ul.content-column").width(); //Get the width of row
			//var colNum = Math.floor(colWrap / 460); //Find how many columns of 200px can fit per row / then round it down to a whole number
			var colNum = <?php echo $blocks_per_row; ?>;
			var colFixed = Math.floor(colWrap / colNum); //Get the width of the row and divide it by the number of columns it can fit / then round it down to a whole number. This value will be the exact width of the re-adjusted column

			$("ul.content-column").css({ 'width' : colWrap}); //Set exact width of row in pixels instead of using % - Prevents cross-browser bugs that appear in certain view port resolutions.
			$("ul.content-column li").css({ 'width' : colFixed}); //Set exact width of the re-adjusted column	

		}						
		
        function reloop()
		{

		} 
        
        var timer = 0;
        function clock(catID){
        	//console.log(markers.length); 
            //console.log(categoryIdAry.length); 
        	if(categoryIdAry.length == markers.length){
            	for(var m = 0;m < markers.length; m++){
                    console.log(markers[m].id);
                }
                    
                for(var m = 0;m < categoryIdAry.length; m++){
                    console.log(categoryIdAry[m]);
                } 
                    
                if(!$("div[id='cat_" + catID + "']").hasClass("active")){
                    	
                        for(var m = 0;m < markers.length; m++){
                            //console.log(markers[m].id);
                        }  
                 }   
                
                
                clearInterval ( timer );
            }
        }

        var mapLayerIdAry = new Array();
        var categoryIdAry = new Array();
        
                
		jQuery(function() {
			//when start load map create "All category" Markers
			$("#cat_0").addClass("active");
            var color = $("div[id='cat_0'] > a.swatch").attr("data")
            $("div[id='cat_0'] > a.swatch").css("background", color); 
            
			var map_layer;
			markers = null;
			var catID = '';
			OpenLayers.Strategy.Fixed.prototype.preload=true;
			
			/*
			- Initialize Map
			- Uses Spherical Mercator Projection
			- Units in Metres instead of Degrees					
			*/
			var options = {
				units: "mi",
				numZoomLevels: 18,
				controls:[],
				projection: proj_900913,
				'displayProjection': proj_4326,
				eventListeners: {
					"zoomend": mapMove
				},
				'theme': null
			};
			
			map = new OpenLayers.Map('map', options);
			map.addControl( new OpenLayers.Control.LoadingPanel({minSize: new OpenLayers.Size(573, 366)}) );
			
			<?php echo map::layers_js(FALSE); ?>
			map.addLayers(<?php echo map::layers_array(FALSE); ?>);  
            
			// Add Controls
			map.addControl(new OpenLayers.Control.Navigation());
			map.addControl(new OpenLayers.Control.Attribution());
			map.addControl(new OpenLayers.Control.PanZoomBar());
			map.addControl(new OpenLayers.Control.MousePosition(
				{
					div: document.getElementById('mapMousePosition'),
					numdigits: 5
				}));    
			map.addControl(new OpenLayers.Control.Scale('mapScale'));
			map.addControl(new OpenLayers.Control.ScaleLine());
			map.addControl(new OpenLayers.Control.LayerSwitcher());
			
			// display the map projection
			document.getElementById('mapProjection').innerHTML = map.projection;
				
			gMap = map;
// TTP Plugin Chart Event //http://www.ugtaarifa.org/
            $.getJSON(	location.href + 'Tphp/ReportFilter.php',
                        'parameters/data',
                        function(data)
            {
                 
                fieldData = data;
                BuildSelect();
                DrawChart();
            });
			 
            $("#Chart_selecter").change(function(e)
            {
                BuildSelect();
                DrawChart();
             	
                chartStyle = $("#Chart_selecter").val();
                
                if(chartStyle == "Pie"){
                	$("#lbl_yaxis").html("Data Set1:");
                    $("#lbl_xaxis").html("Data Set2:");
                    $("#lbl_xrange").html("Data Range2:");
                }else{
                	$("#lbl_yaxis").html("Y-Axis:");
                    $("#lbl_xaxis").html("X-Axis:");
                    $("#lbl_xrange").html("X-Range:");
                }
                        
            }); 
            $("#XAxis_selecter").change(function(e)
            {
                BuildXRange();
                BuildYaxis();
                DrawChart();
            });
            $("#XRange_selecter").change(function(e)
            {
                DrawChart();
            });
            $("#YAxis_selecter").change(function(e)
            {
                DrawChart();
            });
/*		
            // chart select
            $("#Chart_selecter").change(function(e)
            {	
            	
            	chartStyle = $("#Chart_selecter").val();
                // Set chart options
                var options = {'title':GoogleGraphData.label,'width':400,'height':150};
                // Instantiate and draw our chart, passing in some options.
                switch (chartStyle)
                {
                    case "Column":
                        var chart = new google.visualization.ColumnChart(document.getElementById("google-chart"));
		            	$(".NotPie").show();
                        break;
                    case "Bar":
                        var chart = new google.visualization.BarChart(document.getElementById("google-chart"));
		            	$(".NotPie").show();
                        break;
                    case "Pie":
                        var chart = new google.visualization.PieChart(document.getElementById("google-chart"));
		            	$(".NotPie").hide();
                        
                        break;
                }
                chart.draw(GoogleGraphData, options);
            });
            $("#XAxis_selecter").change(function(e)
			{
            	switch( $("#XAxis_selecter").val() )
                {
                	case "Time":
		            	$("#tim_obj").show();
        		        $("#cat_obj").hide();
                    	break;
                	case "Category":
            			$("#tim_obj").hide();
                		$("#cat_obj").show();
                    	break;
                }
            });
            $("#addCatGroup").click(function(e)
			{
            	
            });
            $("#addTimeGroup").click(function(e)
			{
            	if(this.checked)
                	$("#sub_tim_obj").show();
                else
                	$("#sub_tim_obj").hide();
            });
*/
// End of TTP Plugin Chart Event

// TTP Plugin show all of subcategories as default
        	$("[id^='child_']").hide(); // Hide All Children DIV
// End of TTP Plugin
			
			//Show and hide children div 
			$("a[id^='down_']").click(function(){
            	var catID = this.id.substring(5);
               
            	if( $("#child_" + catID).css("display") == "none" )
					$("#child_" + catID).show(); // Show children DIV
                else
                	$("#child_" + catID).hide(); // Show children DIV
                 
            }); 
            
            var selectedIdAry = new Array();
			// Category Switch Action
			$("a[id^='squ_']").click(function(event){
				var isAddMarker = true;
                var isRemoveMap = false;

				var catID = this.id.substring(4); 
				var color = $("div[id='cat_" + catID + "'] > a.swatch").attr("data");  
                
				
                if(catID == 0){																		//click first category
                	 if($("div[id='cat_" + catID + "']").hasClass("active")){
                      
                     	$("div[id='cat_" + catID + "']").removeClass("active");
	                	$("div[id='cat_" + catID + "'] > a.swatch").css("background", "");
                        
                        selectedIdAry.push(0);
                        
                        isRemoveMap = true;
                        isAddMarker = false; 
                     }else{
                      
                        for(var i = 0;i <= categoryIdAry.length;i++){
                        	selectedIdAry.push(categoryIdAry[i]);
                        }
                        																			//remove all category except first category
                        $("div[id^='cat_']").removeClass("active");
	                	$("div[id^='cat_'] > a.swatch").css("background", ""); 
                        
                        isRemoveMap = true;
                        
                     	$("div[id='cat_" + catID + "']").addClass("active");
	                	$("div[id='cat_" + catID + "'] > a.swatch").css("background", color);  
                       
                     }
                }else if( $("#child_"+catID).css("display") == "block" )							//click parent category 
				{
                	 if($("div[id='cat_" + catID + "']").hasClass("active")){						//if unactive and all subcategory is unactive (Remove all subMap)
                      
                        $("div[id='cat_" + catID + "']").removeClass("active");
	                	$("div[id='cat_" + catID + "'] > a.swatch").css("background", "");
                    	
                    	selectedIdAry.push(0);
                        selectedIdAry.push(catID);													//remove parent MapID
                        
                         
                        $("#child_"+ catID).children().children().children().each(function() {  
                              
                             $("#squ_" + $(this).attr("id").substring(4)).css("background", "");
                             $("#cat_" + $(this).attr("id").substring(4)).removeClass("active"); 
                             
                             selectedIdAry.push($(this).attr("id").substring(4)); 		
                        }); 
                         
                        
                        isRemoveMap = true;
                        isAddMarker = false; 
                     }else{																			//if active and all subcategory active
                     	//Remove "All Category first"
                     	//Then click parent categeory and all child category active
	                    $("#cat_0").removeClass("active"); 
	                    $("div[id='cat_0'] > a.swatch").css("background", "");
                        
                        selectedIdAry.push(0);
                        
                        $("div[id='cat_" + catID + "']").addClass("active");
	                	$("div[id='cat_" + catID + "'] > a.swatch").css("background", color); 
                       
                        $("#child_"+ catID).children().children().children().each(function() { 					//if parent category click then if child category have clicked than remove all
                        	if($("#cat_" + $(this).attr("id").substring(4)).hasClass("active")){
                            	  selectedIdAry.push($(this).attr("id").substring(4));
                            }
                        });
                        
                        $("#child_"+ catID).children().children().children().each(function() { 
                        	var color = $("#squ_" + $(this).attr("id").substring(4)).attr("data");
                            $("#squ_" + $(this).attr("id").substring(4)).css("background", color);
                            $("#cat_" + $(this).attr("id").substring(4)).addClass("active");
                        });
						
                        isRemoveMap = true; 
                     }
                }else{																							//click chidren category
                	 var parentID = $("div[id='cat_" + catID + "']").parent().parent().parent().attr("id").substring(6);
					 var pColor = $("div[id='cat_" + parentID + "'] > a.swatch").attr("data");

                	 if($("div[id='cat_" + parentID + "']").hasClass("active")){								//parent category click 
							 //alert("1");
							 selectedIdAry.push(parentID);//remove parent
							  isRemoveMap = true;
							  
							  if($("div[id='cat_" + catID + "']").hasClass("active")){
							  	
							  	$("div[id='cat_" + parentID + "']").removeClass("active");
							  	
							  	 $("#child_"+ parentID).children().children().children().each(function() { 					//if parent category click then if child category have clicked than remove all
			                        	if($(this).attr("id").substring(4)!=catID){
			                        		
			                        		//#20130313 young #sorry,it's only the way that I'm find. Using Dom's click event 
			                        		$("div[id='cat_"+$(this).attr("id").substring(4)+"'] > a.swatch").click();
			                        	}
			                            	 // alert($(this).attr("id").substring(4));
			                            
			                        });
			                        
							  	
							  	
							  		//$("div[id='cat_19'] > a.swatch").click();
							  	
							  	//alert("???");
							  }
							  
                             
                     }else{
                     		//alert("2");
                            if($("div[id='cat_" + catID + "']").hasClass("active")){
                         		 //alert("3");
                                $("div[id='cat_" + catID + "']").removeClass("active");
                                $("div[id='cat_" + catID + "'] > a.swatch").css("background", "");
                        		
                                selectedIdAry.push(catID); 
                             	
                                isRemoveMap = true;
                                isAddMarker = false;
                            }else{ 
        						//alert("4");
        						//Remove "All Category"
	                    		$("#cat_0").removeClass("active"); 
	                    		$("div[id='cat_0'] > a.swatch").css("background", "");

								selectedIdAry.push(0);
                                
                                $("div[id='cat_" + catID + "']").addClass("active");
                                $("div[id='cat_" + catID + "'] > a.swatch").css("background", color); 
                               
                                isRemoveMap = true; 
                                
                            } 
                           
                      }
		              //alert('5');       
                      //detect child nodes have active attribute
                      //if have no child selected => parent node blank
                      //if have selected least one child => parent node half 
                      //if all child selected => parent node full          
                      var k = 0;
                      $("#child_"+ parentID).children().children().children().each(function() {
                              if($(this).hasClass("active")){
                                   k++ 
                              }
                      }); 
                     //alert("K="+k);           
                      if(k == 0){  
                              $("div[id='cat_" + parentID + "'] > a.swatch").css("background", "");
                              $("div[id='cat_" + parentID + "']").removeClass("active");
                                    
                      }else if (k == $("#child_"+ parentID).children().children().children().size()){ 
                              $("div[id='cat_" + parentID + "']").addClass("active");
                              $("div[id='cat_" + parentID + "'] > a.swatch").css("background", pColor);
                                    
                      }else{
                              $("div[id='cat_" + parentID + "'] > a.swatch").css("background", "-moz-linear-gradient(left bottom 50deg, " + pColor + " 55%, white 45%)");
	            			  $("div[id='cat_" + parentID + "'] > a.swatch").css("background", "-webkit-gradient(linear, left bottom, right top, from(" + pColor + "), color-stop(50%, " + pColor + "), color-stop(50%, white))"); 
                      } 																			
                	 
                } 
                
                    
	            
	            //replace "Shift" trigger to "Active" class tigger
	            //ShiftKeyPress = $("div[id='cat_" + catID + "']").hasClass("active");
	            
	            //Start create new Layer
	            currentCat = catID;
				$("#currentCat").val(catID);

				// setUrl not supported with Cluster Strategy
				//markers.setUrl("<?php echo url::site(); ?>" json_url + '/?c=' + catID);
					
				// Destroy any open popups
				if (selectedFeature) {
					onPopupClose();
				};
					
				// Get Current Zoom
				currZoom = map.getZoom();
					
				// Get Current Center
				currCenter = map.getCenter();
					
				gCategoryId = catID;
					
				var startTime = new Date($("#startDate").val() * 1000);
				var endTime = new Date($("#endDate").val() * 1000); 
				
	            		 
// TTP Modify Check shift and call Marker
				if( isAddMarker ){ 
                    
					addMarkers2(catID, $("#startDate").val(), $("#endDate").val(), currZoom, currCenter, gMediaType);
					addMarkers(catID, $("#startDate").val(), $("#endDate").val(), currZoom, currCenter, gMediaType);
                 	
					graphData = "";
					$.getJSON("<?php echo url::site()."json/timeline/"?>"+catID, function(data) {
						graphData = data[0];

						gTimeline = $.timeline({categoryId: catID, startTime: startTime, endTime: endTime,
							graphData: graphData,
							mediaType: gMediaType
						});
						gTimeline.plot();
					});
					
					dailyGraphData = "";
					$.getJSON("<?php echo url::site()."json/timeline/"?>"+catID+"?i=day", function(data)
	                {
						dailyGraphData = data[0]; 
					});
					allGraphData = "";
					$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat, function(data) {
						allGraphData = data[0];
					}); 
					
                    mapLayerIdAry.push(tempMarkersLayerID);
                    categoryIdAry.push(catID);  
                    
                    //timer = setInterval(function(){clock(catID)},1000);
                   
                    timer = setInterval(function(){},1000);
                 }  
                
//remove maplayer
                 if(isRemoveMap){
                  	 
                     for(var n = 0;n < selectedIdAry.length; n++){
                     
                     	for(var m = 0;m < categoryIdAry.length; m++){
                        		if(selectedIdAry[n] == categoryIdAry[m]){ 
                                	
                                    try{ 
                                    	var markID = markers[m].id.split("_"); 
                                    	var mark2ID = markers2[m].id.split("_"); 
                                    	
                                    	var mapID = $("div[id$='" + markID[1] + "']").attr("id");
                                    	var map2ID = $("div[id$='" + markID[1] + "']").attr("id");
                                    	
                                    	$("div[id^='" + markID[0] + "']").each(function() { 
                                    		$("div[id$='" + markID[1] + "']").remove();
                                    	});
                                        
                                        $("div[id^='" + mark2ID[0] + "']").each(function() { 
                                    		$("div[id$='" + mark2ID[1] + "']").remove();
                                    	});
                                    	
                                    	categoryIdAry.splice(m,1,null); 
                                    	 
                                    }catch(err){
                                    	
                                    }
                                }
                        }
                     }
                 	 
                  } 
                  
                  selectedIdAry = [];
			});
			

			// Sharing Layer[s] Switch Action
			$("a[id^='share_']").click(function()
			{
				var shareID = this.id.substring(6);
				
				if ( $("#share_" + shareID).hasClass("active") )
				{
					share_layer = map.getLayersByName("Share_"+shareID);
					if (share_layer)
					{
						for (var i = 0; i <?php echo '<'; ?> share_layer.length; i++)
						{
							map.removeLayer(share_layer[i]);
						}
					}
					$("#share_" + shareID).removeClass("active");
					
				} 
				else
				{
					$("#share_" + shareID).addClass("active");
					
					// Get Current Zoom
					currZoom = map.getZoom();

					// Get Current Center
					currCenter = map.getCenter();
					
					// Add New Layer
					addMarkers2('', '', '', currZoom, currCenter, '', shareID, 'shares');
					addMarkers('', '', '', currZoom, currCenter, '', shareID, 'shares');
				}
			});

			// Exit if we don't have any incidents
			if (!$("#startDate").val())
			{
				map.setCenter(new OpenLayers.LonLat(<?php echo $longitude ?>, <?php echo $latitude ?>), 5);
				return;
			}
			
			//Accessible Slider/Select Switch
			$("select#startDate, select#endDate").selectToUISlider({
				labels: 4,
				labelSrc: 'text',
				sliderOptions: {
					change: function(e, ui)
					{
						var startDate = $("#startDate").val();
						var endDate = $("#endDate").val();
						var currentCat = gCategoryId;
						
						// Get Current Category
						currCat = currentCat;
						
						// Get Current Zoom
						currZoom = map.getZoom();
						
						// Get Current Center
						currCenter = map.getCenter();
						
						// If we're in a month date range, switch to
						// non-clustered mode. Default interval is monthly
						var startTime = new Date(startDate * 1000);
						var endTime = new Date(endDate * 1000);
						if ((endTime - startTime) / (1000 * 60 * 60 * 24) <?php echo '<'; ?>= 32)
						{
							json_url = "json";
						} 
						else
						{
							json_url = default_json_url;
						}
						
						// Refresh Map
                        addMarkers2(currCat, startDate, endDate, '', '', gMediaType);
						addMarkers(currCat, startDate, endDate, '', '', gMediaType);
						
						refreshGraph(startDate, endDate);
					}
				}
			});
			
			var startTime = <?php echo $active_startDate ?>;	// Default to most active month
			var endTime = <?php echo $active_endDate ?>;		// Default to most active month
			
			// get the closest existing dates in the selection options
			options = $('#startDate > optgroup > option').map(function()
			{
				return $(this).val(); 
			});
			startTime = $.grep(options, function(n,i)
			{
			  return parseInt(n) >= startTime;
			})[0];
			
			
			options = $('#endDate > optgroup > option').map(function()
			{
				return $(this).val(); 
			});
			endTime = $.grep(options, function(n,i)
			{
			  return parseInt(n) >= endTime ;
			})[0];
			
			
			gCategoryId = '0';
			gMediaType = 0;
			//$("#startDate").val(startTime);
			//$("#endDate").val(endTime);
			
			// Initialize Map
			addMarkers2(gCategoryId, startTime, endTime, '', '', gMediaType);
			addMarkers(gCategoryId, startTime, endTime, '', '', gMediaType);
			 
            mapLayerIdAry.push(tempMarkersLayerID);
			categoryIdAry.push("0");
                     
			refreshGraph(startTime, endTime);
			 
			// Media Filter Action
			$('.filters li a').click(function()
			{
				// Destroy any open popups
				if (selectedFeature) {
					onPopupClose();
				};
				
				var startTimestamp = $("#startDate").val();
				var endTimestamp = $("#endDate").val();
				var startTime = new Date(startTimestamp * 1000);
				var endTime = new Date(endTimestamp * 1000);
				gMediaType = parseFloat(this.id.replace('media_', '')) || 0;
				currentCat = $("#currentCat").val();
				
				// Get Current Zoom
				currZoom = map.getZoom();
					
				// Get Current Center
				currCenter = map.getCenter();
				
				// Refresh Map
                addMarkers2(currentCat, startTimestamp, endTimestamp, 
				           currZoom, currCenter, gMediaType);
                           
				addMarkers(currentCat, startTimestamp, endTimestamp, 
				           currZoom, currCenter, gMediaType);
				
				$('.filters li a').attr('class', '');
				$(this).addClass('active');
				
				graphData = "";
				$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat, function(data) {
					graphData = data[0];

					gTimeline = $.timeline({categoryId: currentCat, startTime: startTime, endTime: endTime,
						graphData: graphData,
						mediaType: gMediaType
					});
					gTimeline.plot();
				});
				
				dailyGraphData = "";
				$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat+"?i=day", function(data) {
					dailyGraphData = data[0];
//	                GoogleChartDraw( dailyGraphData );
				});
				allGraphData = "";
				$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat, function(data) {
					allGraphData = data[0];
				});
				
				return false;
			});
			
			$('#playTimeline').click(function()
			{
// TTP Plugin / Play Start
    ShiftKeyPress = false;   
    
    			 $('#playTimeline').empty("background-image");  
                
    			 if($("fieldset").attr("class") == "play"){
                 	$('#playTimeline').css("background-image", "url(<?php echo url::file_loc('img'); ?>media/img/PAUSE.png)");  
                 }else if($("fieldset").attr("class") == "play pause"){
                 	$('#playTimeline').css("background-image", "url(<?php echo url::file_loc('img'); ?>media/img/A06.png)");  
                 }else{
                 	$('#playTimeline').css("background-image", "url(<?php echo url::file_loc('img'); ?>media/img/PAUSE.png)");  
                 }
                 
                 

                 //alert($('#playTimeline').css("background-image"));
    			gTimelineMarkers2 = gTimeline.addMarkers2(gStartTime.getTime()/1000,
				$.dayEndDateTime(gEndTime.getTime()/1000), gMap.getZoom(),
				gMap.getCenter(),null,null,null,null,"json");
                     
			    gTimelineMarkers = gTimeline.addMarkers(gStartTime.getTime()/1000,
				$.dayEndDateTime(gEndTime.getTime()/1000), gMap.getZoom(),
				gMap.getCenter(),null,null,null,null,"json");
				gTimeline.playOrPause('raindrops');
			});
			
			smartColumns();//Execute the function when page loads
            
           
		});
		
		$(window).resize(function ()
		{ //Each time the viewport is adjusted/resized, execute the function
			smartColumns();
		});