<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_Controller extends CI_Controller {


	public function __construct() {
		parent::__construct();

		header("Content-Type: Application/Json");
	}


	public function conversations($user_id) {
		/*
		$conversations = $this->utilities->prepare_messages($this->db->query("
			select m1.*, c.*
			from chat_messages m1 
			inner join (
				select conversation_id, max(created_at) as latest 
			    from chat_messages m2 
			    group by conversation_id
			) m2
			left join chat_conversations c
			on m1.conversation_id = c.id
			where m1.conversation_id = m2.conversation_id 
			and m1.created_at = m2.latest
			and m1.conversation_id in (
				select conversation_id 
			    from chat_participants 
			    where user_id = '{$user_id}'
			)
			order by m1.created_at desc")->result());

		foreach ($conversations as $id => $conversation) {
			$conversations[$id]->participants = $this->db->select('users.id, users.first_name, users.last_name')
														 ->from('chat_participants')
														 ->join('users', 'users.id = chat_participants.user_id')
														 ->where('conversation_id', $conversation->id)
														 ->get()
														 ->result();
		}

		/*
		foreach ($conversations as $id => $conversation) {
			$conversations[$id]["participants"] = $this->db->select('users.id, users.first_name, users.last_name')
														   ->from('chat_participants')
														   ->join('users', 'users.id = chat_participants.user_id')
														   ->where('conversation_id', $conversation["id"])
														   ->get()
														   ->result_array();
		}
		*/
		$user = $this->session->userdata("user");

		$conversations = $this->db->select("c.*")
			->from("chat_participants p")
			->join("chat_conversations c", "c.id = p.conversation_id")
			->where("user_id", $user->id)
			->get()
			->result_array();

		foreach ($conversations as $id => $conversation) {
			$conversations[$id]["participants"] = $this->db->select('users.id, users.first_name, users.last_name')
				->from('chat_participants')
				->join('users', 'users.id = chat_participants.user_id')
				->where('conversation_id', $conversation["id"])
				->get()
				->result_array();

			$conversations[$id]["latest_message"] = $this->db->select("*")
				->from("chat_messages")
				->where("conversation_id", $conversation["id"])
				->order_by("created_at", "desc")
				->get()
				->row_array();

			if ($conversations[$id]["latest_message"]) {
				$conversations[$id]["latest_message"]["body"] = $this->encryption->decrypt($conversations[$id]["latest_message"]["body"]);
			} else {
				$conversations[$id]["latest_message"] = [
					"body" => "No messages",
					"created_at" => "0"
				];
			}
		}

		$conversations = $this->utilities->sort_conversations($conversations);

		return print json_encode($conversations, JSON_PRETTY_PRINT);
	}

	public function show($user_id) {
		$user = $this->db->get_where("users", ["id" => $user_id])->row_array();

		if ($user) {
			unset($user["password"]);
			return print json_encode($user);
		}
	}

}
