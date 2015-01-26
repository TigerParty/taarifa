<?php

require("application/config/mysql_connect.php");
/**
 * Category helper. Displays categories on the front-end.
 *
 * @package    Category
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class category_Core {

	/**
	 * Displays a single category checkbox.
	 */
	public static function display_category_checkbox($category, $selected_categories, $form_field, $enable_parents = FALSE)
	{
		$html = '';
		$vis_child_count = 0;
		$disabled = "";
		if(strcmp(gettype($category),'object')==0){
			$cid = $category->id;
			$l = Kohana::config('locale.language.0');
			$category_title = Category_Lang_Model::category_title($cid, $l);
			// Category is selected.
			$category_checked = in_array($cid, $selected_categories);
			$category_color = $category->category_color;
			foreach ($category->children as $child)
			{
				$child_visible = $child->category_visible;
				if ($child_visible)
				{
					// Increment Visible Child count
					++$vis_child_count;
				}
			}
			if (!$enable_parents AND $category->children->count() > 0 AND $vis_child_count >0)
			{
				$disabled = " disabled=\"disabled\"";	
			}
			$category_checked = in_array($cid, $selected_categories);
		}
		else{
			$cid = $category;
			$l = Kohana::config('locale.language.0');
			$category_title = Category_Lang_Model::category_title($cid, $l);
			//color
			$sql = "SELECT * FROM category WHERE id =".$cid.";";
			$results = mysql_query($sql);
			while($row = mysql_fetch_array($results)){
				//echo $row[7];
				$category_color = $row[7];
			}
			//visible
			$sql2 = "SELECT * FROM category WHERE parent_id =".$cid.";";
			$results2 = mysql_query($sql2);
			if($row2 = mysql_fetch_array($results2)){
				$disabled = " disabled=\"disabled\"";
			}
			$category_checked = in_array($cid, $selected_categories);
		}

		$html .= form::checkbox($form_field.'[]', $cid, $category_checked, ' class="check-box"'.$disabled);
		$html .= $category_title;

		return $html;
	}


	/**
	 * Display category tree with input checkboxes.
	 */
	public static function tree($categories, array $selected_categories, $form_field, $columns = 1, $enable_parents = FALSE)
	{
		$html = '';

		// Validate columns
		$columns = (int) $columns;
		if ($columns == 0)
		{
			$columns = 1;
		}


		$categories_total = $categories->count();
		// Format categories for column display.
		$this_col = 1; // column number
		$maxper_col = round($categories_total/$columns); // Maximum number of elements per column
		$i = 1;  // Element Count
		foreach ($categories as $category)
		{
			// If this is the first element of a column, start a new UL
			if ($i == 1)
			{
				$html .= '<ul id="category-column-'.$this_col.'">';
			}

			// Display parent category.
			$html .= '<li>';
			$html .= category::display_category_checkbox($category, $selected_categories, $form_field, $enable_parents);
			
			// Visible Child Count
			$vis_child_count = 0;
			foreach ($category->children as $child)
			{
				$child_visible = $child->category_visible;
				if ($child_visible)
				{
					// Increment Visible Child count
					++$vis_child_count;
				}
			}
			// Display child categories.
			if ($category->children->count() > 0 AND $vis_child_count > 0)
			{
				$html .= '<ul>';
				foreach ($category->children as $child)
				{

					$child_visible = $child->category_visible;
					if ($child_visible)
					{
						$html .= '<li>';
						$html .= category::display_category_checkbox($child, $selected_categories, $form_field, $enable_parents);

						$html = self::recursively_checkbox($child->id,$selected_categories, $form_field, $enable_parents,$html);
						
					}
				}
				$html .= '</ul>';
			}
			$i++;

			// If this is the last element of a column, close the UL
			if ($i > $maxper_col || $i == $categories_total)
			{
				$html .= '</ul>';
				$i = 1;
				$this_col++;
			}
		}
		return $html;
	}
	
	function recursively_checkbox($nodesID,$selected_categories, $form_field, $enable_parents,$html){

		$sql = "SELECT * FROM category WHERE parent_id =".$nodesID.";";
		$results = mysql_query($sql);
		if($results){
			while($row = mysql_fetch_array($results)){
				if((int)($row[0])!=0){
					 $html .= '<ul>';
					 $html .= '<li>';
					 $html .= category::display_category_checkbox($row[0], $selected_categories, $form_field, $enable_parents);
					 $html = self::recursively_checkbox($row[0],$selected_categories, $form_field, $enable_parents,$html);
					 $html .= '</ul>';
				}
			}
			return $html;
		}
		else{
			echo "1";
			return $html;
		}
	}

	function mainview_category($nodesID,$style_px){

		//$html = "";
		$sql = "SELECT * FROM category WHERE parent_id =".$nodesID.";";
		$results = mysql_query($sql);
		if($results){
			echo  '<ul>';
			while($row = mysql_fetch_array($results)){
				if((int)($row[0])!=0){
					$child_title = $row[5];
            		$child_color = $row[7];
            		$child_image = '';
            		$color_css = 'href="javascript:void(0)" class="swatch" style="border:1px solid #'.$child_color.'" data="#'.$child_color.'"';
            		if($row[8] != NULL && file_exists(Kohana::config('upload.relative_directory').'/'.$row[8])) {
		                $child_image = html::image(array(
		                	'src'=>Kohana::config('upload.relative_directory').'/'.$row[8],
		                	'style'=>'float:left;padding-right:5px;'
		                ));
		                //$color_css = '';
		            }
		            echo  '<li><div id="cat_'. $row[0] .'" style="padding-left:'.$style_px.'px;"><a '.$color_css.' id="squ_'. $row[0] .'">'.$child_image.'</a><span class="category-title">'.$child_title.'</span>
		                  <a href="javascript:void(0)" id="down_'. $row[0] .'" style="float:right"><img src="'.url::file_loc('img').'media/img/DownArrow.png" /></a></div>';
		                                         	
		            echo '<div class="hide" id="child_'. $row[0] .'">';

		            self::mainview_category($row[0],(int)($style_px)+20);

			        echo '</div></li>';
				}
			}
			echo  '</ul>';
		}
		else{
			//echo  '</ul>';
		}                       
	}

	/**
	 * Generates a category tree view - recursively iterates
	 *
	 * @return string
	 */
	public static function get_category_tree_view()
	{
		// To hold the category data
		$category_data = array();
		
		// Database table prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		// Database instance
		$db = new Database();
		
		// Fetch all the top level parent categories
		foreach (Category_Model::get_categories() as $category)
		{
			self::_extend_category_data($category_data, $category);
		}
		
		// NOTES: Emmanuel Kala - Aug 5, 2011
		// Initialize the report totals for the parent categories just in case
		// the deployment does not have sub-categories in which case the query
		// below won't return a result
		self::_init_parent_category_report_totals($category_data, $table_prefix);
		
		// Query to fetch the report totals for the parent categories
		$sql = "SELECT c2.id,  COUNT(DISTINCT ic.incident_id)  AS report_count "
			. "FROM ".$table_prefix."category c, ".$table_prefix."category c2, ".$table_prefix."incident_category ic "
			. "INNER JOIN ".$table_prefix."incident i ON (ic.incident_id = i.id) "
			. "WHERE (ic.category_id = c.id OR ic.category_id = c2.id) "
			. "AND c.parent_id = c2.id "
			. "AND i.enable = 1 "
			. "AND i.incident_active = 1 "
			. "AND c2.category_visible = 1 "
			. "AND c.category_visible = 1 "
			. "AND c2.parent_id = 0 "
			. "AND c2.category_title != \"Trusted Reports\" "
			. "GROUP BY c2.id "
			. "ORDER BY c2.id ASC";
		
		// Update the report_count field of each top-level category
		foreach ($db->query($sql) as $category_total)
		{
			// Check if the category exists
			if (array_key_exists($category_total->id, $category_data))
			{
				// Update
				$category_data[$category_total->id]['report_count'] = $category_total->report_count;
				echo $category_total->report_count;
			}
		}
		
		// Fetch the other categories
		$sql = "SELECT c.id, c.parent_id, c.category_title, c.category_color, COUNT(c.id) report_count "
			. "FROM ".$table_prefix."category c "
			. "INNER JOIN ".$table_prefix."incident_category ic ON (ic.category_id = c.id) "
			. "INNER JOIN ".$table_prefix."incident i ON (ic.incident_id = i.id) "
			. "WHERE c.category_visible = 1 "
			. "AND i.incident_active = 1 " 
			. "GROUP BY c.category_title "
			. "ORDER BY c.category_title ASC";
		
		// Add child categories
		 
		foreach ($db->query($sql) as $category)
		{
			// Extend the category data array
			self::_extend_category_data($category_data, $category);
			
			if (array_key_exists($category->parent_id, $category_data))
			{
				// Add children
				$category_data[$category->parent_id]['children'][$category->id] = array(
					'category_title' => Category_Lang_Model::category_title($category->id,Kohana::config('locale.language.0')),
					'parent_id' => $category->parent_id,
					'category_color' => $category->category_color,
					'report_count' => $category->report_count,
					'children' => array()
				);
			}
		}
		
		// Generate and return the HTML
		return self::_generate_treeview_html($category_data);
	}
	
	/**
	 * Helper method for adding parent categories to the category data
	 *
	 * @param array $array Pointer to the array containing the category data
	 * @param mixed $category Object Category object to be added tot he array
	 */
	private static function _extend_category_data(array & $array, $category)
	{
		// Check if the category is a top-level parent category
		$temp_category = ($category->parent_id == 0) ? $category : ORM::factory('category', $category->parent_id);

		if ($temp_category instanceof Category_Model AND ! $temp_category->loaded)
			return FALSE;

		// Extend the array
		if ( ! array_key_exists($temp_category->id, $array))
		{
			// Get the report count
			$report_count = property_exists($temp_category, 'report_count')? $temp_category->report_count : 0;
			
			$array[$temp_category->id] = array(
				'category_title' => Category_Lang_Model::category_title($temp_category->id,Kohana::config('locale.language.0')),
				'parent_id' => $temp_category->parent_id,
				'category_color' => $temp_category->category_color,
				'report_count' => $report_count,
				'children' => array()
			);

			return TRUE;
		}

		return FALSE;
	}
	
	/**
	 * Traverses an array containing category data and returns a tree view
	 *
	 * @param array $category_data
	 * @return string
	 */
	private static function _generate_treeview_html($category_data)
	{
		// To hold the treeview HTMl
		$tree_html = "";
		
		foreach ($category_data as $id => $category)
		{
			// Determine the category class
			$category_class = ($category['parent_id'] > 0)? " class=\"report-listing-category-child\"" : "";
			
			$tree_html .= "<li".$category_class.">"
							. "<a href=\"#\" class=\"cat_selected\" id=\"filter_link_cat_".$id."\">"
							. "<span class=\"item-swatch\" style=\"background-color: #".$category['category_color']."\">&nbsp;</span>"
							. "<span class=\"item-title\">".strip_tags($category['category_title'])."</span>"
							. "<span class=\"item-count\">".$category['report_count']."</span>"
							. "</a></li>";
							
			$tree_html .= self::_generate_treeview_html($category['children']);
		}
		
		// Return
		return $tree_html;
	}
	
	/**
	 * Initializes the report totals for the parent categories
	 *
	 * @param array $category_data Array of the parent categories
	 * @param string $table_prefix Database table prefix
	 */
	private static function _init_parent_category_report_totals(array & $category_data, $table_prefix)
	{
		// Query to fetch the report totals for the parent categories
		$sql = "SELECT c.id, COUNT(DISTINCT ic.incident_id) AS report_count "
			. "FROM ".$table_prefix."category c "
			. "INNER JOIN ".$table_prefix."incident_category ic ON (ic.category_id = c.id) "
			. "INNER JOIN ".$table_prefix."incident i ON (ic.incident_id = i.id) "
			. "WHERE c.category_visible = 1 "
			. "AND i.incident_active = 1 "
			. "AND i.enable = 1 "
			. "AND c.parent_id = 0 "
			. "GROUP BY c.id";
		
		// Fetch the records
		$result = Database::instance()->query($sql);
		
		// Set the report totals for each of the parent categorie
		foreach ($result as $category)
		{
			if (array_key_exists($category->id, $category_data))
			{
				$category_data[$category->id]['report_count'] = $category->report_count;
			}
		}
		
		// Garbage collection
		unset ($sql, $result);
	}
}
