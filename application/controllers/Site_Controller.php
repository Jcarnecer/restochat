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
}
