<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_Add_Conversations extends CI_Migration {


	public function up() {
		$this->dbforge->add_field([
			"id" => [
				"type" => "VARCHAR",
				"constraint" => "11"
			],
			"company_id" => [
				"type" => "VARCHAR",
				"constraint" => "11"
			],
			"name" => [
				"type" => "VARCHAR",
				"constraint" => "20",
				"null" => true
			],
			"type" => [
				"type" => "TINYINT",
				"constraint" => 1
			]
		]);
		$this->dbforge->add_key("id", true);
		$this->dbforge->add_key("");
		$this->dbforge->create_table('conversations');
	}


	public function down() {
		$this->dbforge->drop_table('conversations');
	}

}