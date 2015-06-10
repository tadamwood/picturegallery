<?php

	require_once(LIB_PATH.DS.'database.php');

	class Comment {

	protected static $table_name="comments";
	protected static $db_fields = array('id', 'photograph_id', 'created', 'author', 'body');
	public $id;
	public $photograph_id;
	public $created;
	public $author;
	public $body;


	//makes a comment
	public static function make($photo_id, $author="Anonymous", $body="") {
		if(!empty($photo_id) && !empty($author) && !empty($body)) {
			$comment = new Comment();
			$comment->photograph_id = (int)$photo_id;
			$comment->created = strftime("%Y-%m-%d %H:%M:%S", time());
			$comment->author = $author;
			$comment->body = $body;
			return $comment;
		} else {
			return false;
		}
	}

	public static function find_comments_on($photo_id=0) {
		global $db;
		$sql = "SELECT * FROM ".self::$table_name." WHERE photograph_id = ".$db->escape_value($photo_id)." ORDER BY created ASC";
		return self::find_by_sql($sql);
	}

	//Common Database Methods

		//static makes it a class method
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
	}

	//used to call one instance
	public static function find_by_id($id=0) {
		global $db;
		$result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE id=".$db->escape_value($id)." LIMIT 1");
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

	/*protected function attributes() {
		//return an array of atrribute keys and their values
		$attributes = array();
		foreach(self::$db_fields as $field) {
			if(property_exists($this, $field)) {
				$attributes[$field] = $this->field;
			}
		}
		return $attributes;
	}

	protected function sanitized_attributes() {
		global $db;
		$clean_attributes = array();
		//sanitize the values before submitting
		foreach($this->attributes() as $key => $value) {
			$clean_attributes[$key] = $db->escape_values($value);
		}
		return $clean_attributes;
	}*/

	public function save() {
		return isset($this->id) ? $this->update() : $this->create();
	}

	public function create() {
		global $db;
		// $attributes = $this->sanitized_attributes();
		$sql = "INSERT INTO ".self::$table_name." (photograph_id, created, author, body) VALUES ('$this->photograph_id', '$this->created', '$this->author', '$this->body')";
		if($db->query($sql)) {
			$this->id = $db->insert_id();
			return true;
		} else {
			return flase;
		}
	}

	public function update() {
		global $db;
		$sql = "UPDATE ".self::$table_name." SET photograph_id='$this->photograph_id', created='$this->created', author='$this->author', body='$this->body' WHERE id='$this->id'";
		$db->query($sql);
		return ($db->affected_rows() == 1) ? true : false;
	}

	public function delete() {
		global $db;
		$sql = "DELETE FROM ".self::$table_name." WHERE id='$this->id' LIMIT 1";
		$db->query($sql);
		return ($db->affected_rows() == 1) ? true : false;
	}

	}

 ?>