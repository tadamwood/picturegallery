<?php
require_once("../../includes/functions.php");
require_once("../../includes/session.php");
require_once("../../includes/database.php");
require_once("../../includes/user.php");

if($session->is_logged_in()) {
	redirect_to("index.php");
}

//form has been submitted
if(isset($_POST['submit'])) {

	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	//Check the db to see if the username and password exist
	$found_user = User::authenticate

	if($found_user) {
		$session->login($found_user);
		redirect_to("index.php");
	} else {
		$message = "Username/password combination incorrect.";
	}

} else { //form has not been submitted
	$username = "";
	$password = "";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Photo Gallery</title>
	<link rel="stylesheet" type="text/css" href="../stylesheets/main.css" media="all">
</head>
<body>

	<div id="header">
		<h1>Photo Gallery</h1>
	</div>

	<div id="main">
		<h2>Staff Login</h2>
		<?php echo output_message($message); ?>

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

	<div id="footer">
		Copyright <?php echo date("Y", time()); ?>, Thomas Wood
	</div>

	<?php if(isset($db)) { $db->close_connection(); } ?>

</body>
</html>