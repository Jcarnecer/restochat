<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company_Controller extends CI_Controller {


	public function __construct() {
		parent::__construct();

		header("Content-Type: Application/Json");
	}


	public function users() {
		$user = $this->session->userdata("user");
		$company = $this->db->get_where("companies", ["id" => $user->company_id])->row_array();

		$users = $this->db->select("users.id, users.first_name, users.last_name, users.email_address")
			->from("users")
			->where("company_id", $company["id"])
			->order_by('first_name')
			->get()
			->result_array();

		return print json_encode($users, JSON_PRETTY_PRINT);
	}


	public function create_conversation() {
		$user = $this->authenticate->current_user();
		$participants = $_POST['participants'];
		$company_id = $_POST['company_id'];
		$type = $_POST["type"];

		if ($type == 2) {
			$conversation = $this->conversation->get_private_conversation($participants[0], $participants[1]);

			if (!$conversation) {
				$conversation = [
					"id" => $this->utilities->create_random_string(),
					"company_id" => $company_id,
					"type" => 2
				];

				$this->conversation->insert($conversation);
			
				foreach ($participants as $participant) {
					$participant_details = [
						"user_id" => $participant,
						"conversation_id" => $conversation["id"]
					];

					$this->participant->insert($participant_details);
				}
			}
		} 

		/* else {
			$conversation = [
				"id" => $this->utilities->create_random_string(),
				"company_id" => $company_id,
				"type" => 1
			];

			$this->conversation->insert($conversation);
		
			foreach ($participants as $participant) {
				$participant_details = [
					"user_id" => $participant,
					"conversation_id" => $conversation["id"],
				];

				$this->participant->insert($participant_details);
			}
		} */

		$conversation["participants"] = $this->conversation->get_participants($conversation["id"]);
		
		return print json_encode($conversation);
	}
}
