<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_Controller extends CI_Controller {


	public function __construct() {
		parent::__construct();
	}

	public function index() {
		if (!$this->session->has_userdata("user")) {
			return redirect("http://localhost/main/users/login");
		}

		$user = $this->authenticate->current_user();

		$general_conversation = $this->db->get_where(
			'chat_conversations', 
			[
				'name' => 'General', 
				'company_id' => $user->company_id
			]
		)->row_array();

		if (!$general_conversation) {
			$general_conversation = [
				"id" => $this->utilities->create_random_string(),
				"name" => "General",
				"company_id" => $user->company_id,
				"type" => 1
			];

			$this->db->insert("chat_conversations", $general_conversation);
		}

		# check if users is participant of general
		$is_participant = $this->db->get_where(
			"chat_participants", [
				"conversation_id" => $general_conversation["id"],
				"user_id" => $user->id
			]
		)->row_array();

		if (!$is_participant) {
			$this->db->insert(
				"chat_participants", [
					"conversation_id" => $general_conversation["id"],
					"user_id" => $user->id
				]
			);
		}

		$this->load->view('index', [
			'current_user' => $user,
			'company_id' => $user->company_id,
			'general_conversation' => $general_conversation["id"]
		]);
	}


	public function messages() {
		redirect("/");
	}

}
