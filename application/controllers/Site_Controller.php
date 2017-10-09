<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_Controller extends CI_Controller {


	public function index() {
		$this->authenticate->is_authenticated();
		$user = $this->authenticate->current_user();
		$general_conversation = $this->db->get_where('chat_conversations', ['name' => 'General', 'company_id' => $user->company_id])->row();

		$this->load->view('index', [
			'current_user' => $user,
			'company_id' => $user->company_id,
			'general_conversation' => $general_conversation->id
		]);
	}


	public function messages() {
		redirect('/');
	}


	public function login() {
		$error = null;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$email_address = $_POST['email_address'];
			$password = $_POST['password'];

			if ($this->user->authenticate_user($email_address, $password)) {
				return redirect('/');
			}
			
			$error = 'Invalid login credentials';
		}
		return $this->load->view('login', ['error' => $error]);
	}


	public function logout() {
		$this->authenticate->logout_user();
		redirect('users/login');
	}
}
