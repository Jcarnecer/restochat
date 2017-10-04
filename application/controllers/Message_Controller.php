<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message_Controller extends CI_Controller {


	public function __construct() {
		parent::__construct();
	}

	public function index() {
		return print json_encode([
			[
				'id' => 1,
				'body' => "Hello",
				'created_by' => [
					'id' => 1,
					'first_name' => 'Christian Jordan',
					'last_name' => 'Dalan'
				],
				'created_at' => '10:00am'
			],
			[
				'id' => 2,
				'body' => 'World',
				'created_by' => [
					'id' => 1,
					'first_name' => 'Christian Jordan',
					'last_name' => 'Dalan'
				],
				'created_at' => '10:00am'
			]
		]);
	}
}
