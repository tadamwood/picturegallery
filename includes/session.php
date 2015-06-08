<?php
//Session used to log users in and out

class Session {

	private $logged_in=false;
	public $user_id;

	function __construct() {
		session_start();
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

}

$session = new Session();

?>