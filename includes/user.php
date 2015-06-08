<?php

require_once('database.php');

class User {

	//static makes it a class method
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM users");
	}

	//used to call one instance
	public static function find_by_id($id=0) {
		global $db;
		$result_set = self::find_by_sql("SELECT * FROM users WHERE id={$id} LIMIT 1");
		$found = $db->fetch_array($result_set);
		return $found;
	}

	//used to find a subset of sql
	public static function find_by_sql($sql="") {
		global $db;
		$result_set = $db->query($sql);
		return $result_set;
	}

}

?>