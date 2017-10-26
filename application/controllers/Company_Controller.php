<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company_Controller extends CI_Controller {


	public function __construct() {
		parent::__construct();
	}


	public function users($company_id) {
		$key = isset($_GET['key']) ? $_GET['key'] : null;
		$users = null;

		# TODO: Move code to user or company model
		if ($key) {
			$this->db->select('*')->from('users')
					 ->group_start()
						 ->like("CONCAT(first_name, ' ', last_name)", $key)
						 ->or_like('email_address', $key)
					 ->group_end()
					 ->where('company_id', $company_id);
			$users = $this->db->get()->result();
		} else {
			$users = $this->db->from('users')
							  ->where('company_id', $company_id)
							  ->order_by('first_name')
							  ->get()->result();
		}

		return print json_encode($users);
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
