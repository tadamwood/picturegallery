<?php
require_once("../../includes/initialize.php");

if($session->is_logged_in()) {
	redirect_to("index.php");
}

//form has been submitted
if(isset($_POST['submit'])) {

	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	//Check the db to see if the username and password exist
	$found_user = User::authenticate($username, $password);

	if($found_user) {
		$session->login($found_user);
		log_action('Login', "{$found_user->username} logged in.");
		redirect_to('index.php');
	} else {
		$message = "Username/password combination incorrect.";
	}

} else { //form has not been submitted
	$username = "";
	$password = "";
}

?>

<?php include_layout_template('admin_header.php');?>
		<h2>Staff Login</h2>
		 <!--<?php echo output_message($message); ?>-->

		<form action="login.php" method="post" accept-charset="utf-8">
			<table>
				<tr>
					<td>Username:</td>
					<td>
						<input type="text" name="username" maxlength="30" value="<?php echo htmlentities($username); ?>">
					</td>
				</tr>
				<tr>
					<td>Password:</td>
					<td>
						<input type="text" name="password" maxlength="30" value="<?php echo htmlentities($password); ?>">
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="submit" value="Login">
					</td>
				</tr>
			</table>
		</form>
	</div>

	<?php include_layout_template('admin_footer.php');?>