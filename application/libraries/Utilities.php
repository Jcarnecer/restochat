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
			}
		} else {
			$messages->body = $this->CI->encryption->decrypt($messages->body);
			$messages->created_by = $this->CI->db->get_where('users', ['id' => $messages->created_by])->row_array();
		}
		

		return $messages;
	}

	public function sort_conversations($conversations) {
		$length = count($conversations);

		if ($length > 1) {
			$pivot = $conversations[0];

			$left = $right = [];

			for ($i = 1; $i < $length; $i++) {
				if ($conversations[$i]["latest_message"]["created_at"] > $pivot["latest_message"]["created_at"]) {
					$left[] = $conversations[$i];
				} else {
					$right[] = $conversations[$i];
				}
			}
			return array_merge($this->sort_conversations($left), array($pivot), $this->sort_conversations($right));
		}
		return $conversations;
	}
}