<?php

require_once('database.php');

class User {

	public $id;
	public $username;
	public $password;
	public $first_name;
	public $last_name;

	//static makes it a class method
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM users");
	}

	//used to call one instance
	public static function find_by_id($id=0) {
		global $db;
		$result_array = self::find_by_sql("SELECT * FROM users WHERE id={$id} LIMIT 1");
		//checking to see if the array is empty
		//array shift will shift an element off the beginning of an array
		return !empty($result_array) ? array_shift($result_array) : false;
	}

	//used to find a subset of sql
	public static function find_by_sql($sql="") {
		global $db;
		$result_set = $db->query($sql);
		$object_array = array();
		while ($row = $db->fetch_array($result_set)) {
			//creates an array holding all the information held in $object
			$object_array[] = self::instantiate($row);
		}
		return $object_array;
	}

	//checks if the first and last name are set then returns the values
	public function full_name() {
		if(isset($this->first_name) && isset($this->last_name)) {
			return $this->first_name . " " . $this->last_name;
		} else {
			return "";
		}
	}

	private static function instantiate($record) {
		//creating a new instance of itself (the user class)
		$object = new self;
		/*
		$object->id 			= $record['id'];
		$object->username 		= $record['username'];
		$object->password 		= $record['password'];
		$object->first_name 	= $record['first_name'];
		$object->last_name 		= $record['last_name'];*/

		//more dynamic approach to this
		foreach($record as $attribute=>$value) {
			if($object->has_attribute($attribute)) {
				$object->$attribute = $value;
			}
		}
		return $object;
	}

	//private functions just create an instance
	private function has_attribute($attribute) {
		//get_object_vars returns an associative array with all attributes as the keys and their current values as value
						//this refers to get_object_vars
		$object_vars = get_object_vars($this);
		//checks to see if the key exists

		//returns true of false
		//does the key attribute exists in the array object vars?
		return array_key_exists($attribute, $object_vars);
	}

}

?>