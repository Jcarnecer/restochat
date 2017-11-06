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


	public function get_private_conversation() {
		$participants = $_GET["participants"];
		$conversation = $this->conversation->get_private_conversation($participants[0], $participants[1]);
		return print json_encode($conversation);
	}
}
