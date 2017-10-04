<?php


class Authenticate {
	
	public $CI;


	public function __construct() {
		$this->CI =& get_instance();
	}


	public function is_authenticated() {
		if (!$this->CI->session->userdata('user')) {
			return redirect('http://localhost/login/users/login');
		}
	}


	public function current_user() {
		return $this->CI->session->userdata('user');
	}



}