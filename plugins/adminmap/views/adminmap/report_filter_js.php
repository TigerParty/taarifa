<script type="text/javascript">

/**
 * Toggle AND or OR
 */
function logicalOperatorFilterToggle(lo)
{
	urlParameters['lo'] = lo;	
}

/**
 * Set the selected categories as selected
 */
$(document).ready(function() {

	var categories = [<?php echo $selected_categories; ?>];
	for( i in categories)
	{
		if(!$("#filter_link_cat_" + categories[i]).hasClass("selected"))
		{
			$("#filter_link_cat_" + categories[i]).trigger("click");
		}
	}

	//ride the reset all filters bandwagon
	$("#reset_all_filters").click(function(){
		$("#logicalOperatorRadioOr").attr("checked","checked");
		$("#logicalOperatorRadioAnd").removeAttr("checked");
		logicalOperatorFilterToggle('or');
	});
});




</script>