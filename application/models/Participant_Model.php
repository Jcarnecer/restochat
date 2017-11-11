<?php


class Participant_Model extends CI_Model {

	public $user_id;
	public $conversation_id;


	public function insert($participant) {
		$this->db->insert('chat_participants', $participant);
	}
}