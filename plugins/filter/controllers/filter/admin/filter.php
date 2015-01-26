<?php
class Reports_Controller extends Admin_Controller {
	function index()
	{
		if ($_POST)
		{
			echo Kohana::debug($_POST);
		}
	}
}
?>
