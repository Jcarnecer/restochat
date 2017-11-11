<?php


class Conversation_Model extends CI_Model {


	public $id;
	public $company_id;
	public $name;
	public $type;

	public function insert($conversation) {
		$this->db->insert('chat_conversations', $conversation);
	}


	public function get_general_group($company_id) {
		return $this->db->get_where('chat_conversations', ['company_id' => $company_id, 'name' => 'general'])->row();
	}


	public function get_participants($conversation_id) {
		return $this->db->from('chat_participants')
				 ->join('users', 'users.id = chat_participants.user_id')
				 ->where('conversation_id', $conversation_id)
				 ->get()->result();
	}


	public function find($id) {
		return $this->db->get_where('chat_conversations', ['id' => $id])->row();
	}


	public function find_or_404($id) {
		$group = $this->db->get_where('chat_conversations', ['id' => $id])->row();

		if (!$group) {
			return show_404();
		}
		return $group;
	}


	public function where($query) {
		return $this->db->get_where('chat_conversations', $query)->result();
	}


	public function all() {
		return $this->db->get('chat_conversations')->result();
	}


	public function get_private_conversation($user_one, $user_two) {
		return $this->db->query("
				select conversation_id id
				from chat_participants
				join chat_conversations on chat_conversations.id = chat_participants.conversation_id
				where chat_participants.conversation_id in (
					select conversation_id from chat_participants where user_id = '{$user_one}'
				) and conversation_id in (
				    select conversation_id from chat_participants where user_id = '{$user_two}'
				) and chat_conversations.type = 2
				group by conversation_id
				having count(conversation_id) = 2")->row_array();
	}
}