<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Layout {

	protected $_ci;

	public function __construct()
	{
		$this->_ci =& get_instance();
	}

	function view($template, $data=null) 
	{
		if(empty($this->_ci->session->userdata(config_item('sesUserId')))) {
			//if (!$this->_ci->uri->segment(1) == "auth" || !$this->_ci->uri->segment(2) == "login") {
			//redirect(site_url('auth/login'));
			//}
		}

		if(isset($data["title"])) {
			$data["title"] = $data["title"] . " - DQ Smartplus";
		}
		else {
			$data["title"] = "DQ Smartplus";
		}
	
		$data["menu"] = $this->_ci->load->view("layout/menu_view", null, true);
		$data["content"] = $this->_ci->load->view($template, $data, true);

		$this->_ci->load->view("layout/index_view", $data);
	}

}