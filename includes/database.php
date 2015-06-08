<?php
require_once("config.php");

//creating the database object
class MySQLDatabase {

	//creates the connection object
	private $conn;
	public $last_query;
	private $magic_quotes_active;
	private $real_escape_string_exists;

	function __construct() {
		$this->open_connection();
		$this->magic_quotes_active = get_magic_quotes_gpc();
		$this->real_escape_string_exists = function_exists("mysql_real_escape_string");
	}

	public function open_connection() {
		$this->conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS);
		//if there is no connection then die
		if (!$this->conn) {
			die("Database connection failed: " . mysql_error());
		} else {
			//if there is a connection then select the database
			$db_select = mysqli_select_db($this->conn, DB_NAME);
			//if no database is selected then die
			if (!$db_select) {
				die ("Database selection failed: " . mysql_error());
			}
		}
	}

	public function close_connection() {
		if(isset($this->conn)) {
			mysql_close($this->conn);
			unset($this->conn);
		}
	}

	public function query($sql) {
		$this->last_query = $sql; //saves the last query run
		$result = mysqli_query($this->conn, $sql);
		$this->confirm_query($result);
		return $result;
	}


	public function escape_value($value) {
	//prepares values for submission to SQL
	//checks if magic quotes has been set
	//checks if there is access to msql_real_escape_string
	//processes the submitted value
	//will escape an ' if in a string for mysql
		if ($this->real_escape_string_exists) {
			//PHP v4.3.0 or higher
			//undo any magic quote effects so mysql_real_escape_string can do the work
			if ($this->magic_quotes_active) {
				$value = stripcslashes($value);
			}
			$value = mysqli_real_escape_string($this->conn, $value);
		} else {
			//before PHP v4.3.0
			//if magic quotes aren't already on then add slashes manually
			if (!$this->magic_quotes_active) {
				$value = addslashes($value);
			}//if magic quotes are active, then slashes already exist
		}
		return $value;
	}

	/*Start Database Neutral methods*/
	//if not using MSQL (like oracle)
	public function fetch_array($result_set) {
		return mysqli_fetch_array($result_set);
	}

	public function num_rows($result_set) {
		return mysql_num_rows($result_set);
	}

	public function insert_id() {
		//get the last id inserted over the current db connection
		return mysqli_insert_id($this->conn);
	}

	public function affected_rows() {
		return mysqli_affected_rows($this->conn);
	}
	/*End Database Neutral methods*/

	private function confirm_query($result) {
		if (!$result) {
			//die("Database query failed: " . mysql_error());
			$output = "Database Query failed: ".mysql_error()."<br/><br/>";
			//$output .= "Last SQL query: ".$this->last_query;
			die($output);
		}
	}



}

//allows the creation of a new database as well as being able to use it
$database = new MySQLDatabase();
//creating a shortcut basically to access this object
$db =& $database;

?>