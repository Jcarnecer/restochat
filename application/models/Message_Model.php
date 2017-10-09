<?php


class Message_Model extends CI_Model {

	public $id;
	public $conversation_id;
	public $body;
	public $created_by;
	public $created_at;


	public function insert() {
		return $this->db->insert('chat_messages', $this->message);
	}


	public function created_by($user_id) {
		return $this->db->select('id, first_name, last_name')
						->from('users')
						->where('id', $user_id)
						->get()
						->row_array();
	}


	public function reads($message_id) {
		return $this->db->select('r.created_at, u.first_name, u.last_name, u.id')
						->from('message_reads r')
						->join('users u', 'u.id = r.created_by')
						->where('message_id', $message_id)
						->get()
						->result_array();
	}
}