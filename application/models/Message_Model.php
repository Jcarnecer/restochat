<?php


class Message_Model extends CI_Model {


	public function insert() {
		return $this->db->insert('chat_messages', $this->message);
	}
}