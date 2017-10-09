<?php


class Message_Read_Model extends CI_Model {


	public $message_id;
	public $created_by;
	public $created_at;


	public function get($message_id) {
		return $this->db->select('*')
				 ->from('message_reads')
				 ->where('message_id', $message_id)
				 ->get()
				 ->result_array();
	}

}