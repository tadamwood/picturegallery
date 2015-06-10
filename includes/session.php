<?php
//Session used to log users in and out

class Session {

	private $logged_in=false;
	public $user_id;
	public $message;

	function __construct() {
		session_start();
		$this->check_message();
		$this->check_login();
	}

	public function is_logged_in() {
		return $this->logged_in;
	}

	public function login($user) {
		//the db will find the user based on username and password
		if($user) {
			//sets the user id var to the id
			$this->user_id = $_SESSION['user_id'] = $user->id;
			$this->logged_in = true;
		}
	}

	public function logout() {
		unset($_SESSION['user_id']);
		unset($this->user_id);
		$this->logged_in = false;
	}

	public function message($msg="") {
		if(!empty($msg)) {
			//sets the message if msg isn't empty
			$_SESSION['message'] = $msg;
		} else {
			//get the message if it is empty and retun the attribute
			return $this->message;
		}
	}

	private function check_login() {
		//checks if the session exists
		if(isset($_SESSION['user_id'])) {
			//sets the user id = to that value
			$this->user_id = $_SESSION['user_id'];
			$this->logged_in = true;
		} else {
			unset($this->user_id);
			$this->logged_in = false;
		}
	}

	private function check_message() {
		//is there a message stored?
		if(isset($_SESSION['message'])) {
			//add it as an attribute and erase the stored version
			$this->message = $_SESSION['message'];
			unset($_SESSION['message']);
		} else {
			$this->message = "";
		}
	}

}

$session = new Session();
$message = $session->message();

?>