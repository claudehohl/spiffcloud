<?php
/**
 * Class and Function List:
 * Function list:
 * - __construct()
 * - create_tables()
 * - delete_tables()
 * Classes list:
 * - Db_forge extends Model
 */

class Db_forge extends CI_Model
{
	
	function __construct() 
	{
		parent::__construct();
		$this->load->dbforge();
	}
	
	function create_tables() 
	{
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'constraint' => '11',
				'unsigned' => true,
				'auto_increment' => true,
			) ,
			'word' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
			) ,
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('words', true);
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'constraint' => '11',
				'unsigned' => true,
				'auto_increment' => true,
			) ,
			'word_id' => array(
				'type' => 'INT',
				'constraint' => '11',
				'unsigned' => true,
			) ,
			'space_id' => array(
				'type' => 'INT',
				'constraint' => '11',
				'unsigned' => true,
			) ,
			'ip_id' => array(
				'type' => 'INT',
				'constraint' => '11',
				'unsigned' => true,
			) ,
			'timestamp' => array(
				'type' => 'INT',
				'constraint' => '11',
				'unsigned' => true,
			) ,
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('_words_in_space', true);
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'constraint' => '11',
				'unsigned' => true,
				'auto_increment' => true,
			) ,
			'space_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
			) ,
			'popularity' => array(
				'type' => 'INT',
				'constraint' => '11',
				'unsigned' => true,
			) ,
			'weight' => array(
				'type' => 'FLOAT',
				'constraint' => '2,1',
				'default' => '1.0',
				'unsigned' => true,
			) ,
			'timestamp' => array(
				'type' => 'INT',
				'constraint' => '11',
				'unsigned' => true,
			) ,
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('spaces', true);
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'constraint' => '11',
				'unsigned' => true,
				'auto_increment' => true,
			) ,
			'ip' => array(
				'type' => 'VARCHAR',
				'constraint' => '16',
			) ,
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('ips', true);
	}
	
	function delete_tables() 
	{
		$query = $this->db->query('SHOW TABLES');
		$result = $query->result_array();
		$table_name = array_keys($result[0]);
		$table_name = $table_name[0];
		foreach ($result as $table) 
		{
			$this->dbforge->drop_table($table[$table_name]);
		}
	}
}
