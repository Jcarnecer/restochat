<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_Controller extends CI_Controller {


	public function __construct() {
		parent::__construct();
	}


	public function conversations($user_id) {
		$conversations = $this->db->query("
			select *
			from chat_conversations
			where id in (
				select conversation_id from chat_participants where user_id = '{$user_id}'
			)")->result_array();

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
		*/


		return print json_encode($conversations);
	}

}
