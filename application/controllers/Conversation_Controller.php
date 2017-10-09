<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Conversation_Controller extends CI_Controller {


	public function __construct() {
		parent::__construct();

		header("Content-Type: Application/Json");
	}


	public function index() {
		$conversations = $this->conversation->all();

		foreach ($conversations as $id => $conversation) {
			$conversations[$id]->participants = $this->db->select('users.id, users.first_name, users.last_name')
														 ->from('chat_participants')
														 ->join('users', 'users.id = chat_participants.user_id')
														 ->where('conversation_id', $conversation->id)
														 ->get()
														 ->result();
		}

		return print json_encode($conversations);
	}


	public function show($conversation_id) {
		$conversation = $this->conversation->find($conversation_id);
		$conversation->participants = $this->db->select('users.id, users.first_name, users.last_name')
											   ->from('chat_participants')
											   ->join('users', 'users.id = chat_participants.user_id')
											   ->where('conversation_id', $conversation->id)
											   ->get()
											   ->result();
		return print json_encode($conversation);
	}


	public function messages($conversation_id) {
		$this->db->select('*');
		$this->db->from('chat_messages');
		$this->db->where('conversation_id', $conversation_id);
		$this->db->order_by('created_at');
		return print json_encode($this->utilities->prepare_messages($this->db->get()->result()));
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
