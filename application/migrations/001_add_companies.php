<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_Add_Companies extends CI_Migration {


	public function up() {
		$this->dbforge->add_field([
			"id" => [
				"type" => "VARCHAR",
				"constraint" => "11"
			],
			"name" => [
				"type" => "VARCHAR",
				"constraint" => "20",
				"null" => true
			]
		]);
		$this->dbforge->add_key("id", true);
		$this->dbforge->create_table('companies');
	}


	public function down() {
		$this->dbforge->drop_table('companies');
	}

}