<?php


class Authenticate {
	
	public $CI;


	public function __construct() {
		$this->CI =& get_instance();
	}


	public function is_authenticated() {
		if (!$this->CI->session->userdata('user')) {
			return redirect('users/login');
		}
	}


	public function current_user() {
		return $this->CI->session->userdata('user');
	}


	public function login_user($user) {
		unset($user->password);
		$this->CI->session->set_userdata('user', $user);
	}


	public function logout_user() {
		$this->CI->session->unset_userdata('user');
	}

}