<?php defined('SYSPATH') or die('No direct script access.');

class Status_Controller extends Main_Controller {

  function __construct()
  {
    parent::__construct();
  }

	public function index()
	{
		$this->template->header->this_page = "Status";
		$this->template->content = new View('status/about');
	}
}
?>
