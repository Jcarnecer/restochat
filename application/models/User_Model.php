<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_Model extends CI_Model {

	public $id;
	public $first_name;
	public $last_name;
	public $username;
	public $password;
	public $role;

	public function authenticate_user($email_address, $password) {
		$user = $this->db->get_where('users', ['email_address' => $email_address])->row();
		if ($user && 
			$this->encryption->decrypt($user->password) === $password) {
			$this->authenticate->login_user($user);
			return true;
		}
		return false;
	}
	

	public function insert_user($user_details) {
		return $this->db->insert('users', $user_details);
	}


	public function get_users($query) {
		return $this->db->get_where('users', $query)->result();
	}
}
