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


	public function create_conversation($company_id) {
		$participants = $_POST['participants'];
		$company_id = $_POST['company_id'];

		if (count($participants) == 2) {
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
		} else {
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
		}

		$conversation["participants"] = $this->conversation->get_participants($conversation["id"]);

		return print json_encode($conversation);
		/*
		if (count($participants) == 2) {
			$conversation = $this->db->query("
				select conversation_id
				from chat_participants
				where conversation_id in (
					select conversation_id from chat_participants where user_id = '{$_POST['participants'][0]}'
				) and conversation_id in (
				    select conversation_id from chat_participants where user_id = '{$_POST['participants'][1]}'
				)
				group by conversation_id
				having count(conversation_id) = 2")->row('array');

			if (!$conversation) {
				$conversation->id = $this->utilities->create_random_string();
				$conversation->company_id = $company_id;
				$this->conversation->insert($conversation);

				foreach ($participants as $participant) {
					$participant->conversation_id = $conversation->id;
					$participant->user_id = $participant;
					$this->participant->insert($participant);
				}
			} else {
				$conversation = $this->conversation->find($conversation->conversation_id);
			}
		} else {
			$this->conversation->id = $this->utilities->create_random_string();
			$this->conversation->company_id = $company_id;
			$this->conversation->insert($this->conversation);

			foreach ($participants as $participant) {
				$this->participant->conversation_id = $this->conversation->id;
				$this->participant->user_id = $participant;
				$this->participant->insert($this->participant);
			}
		}

		$this->conversation->participants = $this->conversation->get_participants($conversation->id);
		
		return print json_encode($this->conversation);
		*/
	}
}
