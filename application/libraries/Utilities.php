<?php


class Utilities {


	public $CI;


	public function __construct() {
		$this->CI =& get_instance();
	}


	public function create_random_string($length=11) {
		$string = "";
	    $characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	    for ($i = 0; $i < $length; $i++) {
	        $string .= $characters[rand(0, strlen($characters) - 1)];
	    }

    	return $string;
	}


	public function prepare_messages($messages) {
		if (is_array($messages)) {
			foreach ($messages as $id => $message) {
				$messages[$id]->body = $this->CI->encryption->decrypt($message->body);
				$messages[$id]->created_by = $this->CI->message->created_by($message->created_by);
				$messages[$id]->reads = $this->CI->message->reads($message->id);
			}
		} else {
			$messages->body = $this->CI->encryption->decrypt($messages->body);
			$messages->created_by = $this->CI->db->get_where('users', ['id' => $messages->created_by])->row_array();
			$messages->reads = $this->CI->message->reads($messages->id);
		}
		

		return $messages;
	}
}