<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Conversation_Controller extends CI_Controller {


	public function __construct() {
		parent::__construct();

		header("Content-Type: Application/Json");
	}


	public function index() {
		$conversations = $this->db->get("chat_conversations")->result_array();

		foreach ($conversations as $id => $conversation) {
			$conversations[$id]["participants"] = $this->db->select('users.id, users.first_name, users.last_name')
				->from('chat_participants')
				->join('users', 'users.id = chat_participants.user_id')
				->where('conversation_id', $conversation["id"])
				->get()
				->result_array();
		}

		return print json_encode($conversations, JSON_PRETTY_PRINT);
	}


	public function show($conversation_id) {
		$conversation = $this->db->get_where("chat_conversations", ["id" => $conversation_id])->row_array();

		$conversation["participants"]= $this->db->select('users.id, users.first_name, users.last_name')
			->from('chat_participants')
			->join('users', 'users.id = chat_participants.user_id')
			->where('conversation_id', $conversation["id"])
			->get()
			->result_array();

		$conversation["messages"] = $this->db->select("messages.id, messages.body, messages.created_by, messages.created_at")
			->from("chat_messages as messages")
			->where("conversation_id", $conversation["id"])
			->order_by("created_at")
			->get()
			->result_array();

		if ($conversation["messages"]) {
			foreach ($conversation["messages"] as $id => $message) {
				$conversation["messages"][$id]["body"] = $this->encryption->decrypt($message["body"]);
				$conversation["messages"][$id]["created_by"] = $this->db->select("users.id, users.first_name, users.last_name")
					->from("users")
					->where("id", $message["created_by"])
					->get()
					->row_array();
			}
		}
		
		return print json_encode($conversation, JSON_PRETTY_PRINT);
	}

	public function create() {
		$user = $this->session->userdata("user");
		$type = $this->input->post("type");
		$participants = $this->input->post("participants");
		$participants[] = $user->id;

		if ($type === "1") {
			$conversation = [
				"id" => $this->utilities->create_random_string(),
				"company_id" => $user->company_id,
				"name" => $this->input->post("name"),
				"type" => $this->input->post("type")
			];

			$this->db->insert("chat_conversations", $conversation);
			
			foreach ($participants as $participant) {
				$this->db->insert("chat_participants", [
					"user_id" => $participant,
					"conversation_id" => $conversation["id"]
				]);
			}

			$conversation["latest_message"] = [
				"body" => "No messages"
			];

			$conversation["participants"] = $this->db->select('users.id, users.first_name, users.last_name')
				->from('chat_participants')
				->join('users', 'users.id = chat_participants.user_id')
				->where('conversation_id', $conversation["id"])
				->get()
				->result_array();
		} else if ($type === "2") {
			$conversation = $this->db->query("
				SELECT *
				FROM chat_conversations
				WHERE id IN (
					SELECT conversation_id FROM chat_participants WHERE user_id = '{$participants[0]}'
				) AND id IN (
					SELECT conversation_id FROM chat_participants WHERE user_id = '{$participants[1]}'
				) AND type = 2
			")->row_array();

			if (!$conversation) {
				$conversation = [
					"id" => $this->utilities->create_random_string(),
					"company_id" => $user->company_id,
					"name" => $this->input->post("name"),
					"type" => $this->input->post("type")
				];
			
				$this->db->insert("chat_conversations", $conversation);
			
				foreach ($participants as $participant) {
					$this->db->insert("chat_participants", [
						"user_id" => $participant,
						"conversation_id" => $conversation["id"]
					]);
				}
			
				$conversation["latest_message"] = [
					"body" => "No messages"
				];
			}
		
			$conversation["participants"] = $this->db->select('users.id, users.first_name, users.last_name')
				->from('chat_participants')
				->join('users', 'users.id = chat_participants.user_id')
				->where('conversation_id', $conversation["id"])
				->get()
				->result_array();
		}
		
		return print json_encode($conversation, JSON_PRETTY_PRINT);
	}


	public function create_message($conversation_id) {
		$user = $this->authenticate->current_user();

		$this->message->id = $this->utilities->create_random_string();
		$this->message->conversation_id = $conversation_id;
		$this->message->body = $this->encryption->encrypt($_POST['body']);
		$this->message->created_by = $user->id;
		$this->message->created_at = time();

		if ($this->message->insert()) {
			return print json_encode($this->utilities->prepare_messages($this->message));
		}
		return show_error(404);
	}

}
