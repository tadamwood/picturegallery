<?php
require_once("../../includes/initialize.php");
require_once("../../includes/session.php");

if (!$session->is_logged_in()) { redirect_to("login.php");}
?>

<?php include_layout_template('admin_header.php');?>
		<h2>Menu</h2>
		<?php echo output_message($message); ?>
		<a href="list_photos.php">List of Photos</a> <br/>
		<a href="logfile.php">Log Files</a> <br/>
		<a href="logout.php">Logout</a>
	</div>

<?php include_layout_template('admin_footer.php');?>
	