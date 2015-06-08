<?php require_once("../../includes/initialize.php"); ?>
<?php if (!$session->is_logged_in()) { redirect_to("login.php");} ?>
<?php 

$logfile = SITE_ROOT.DS.'logs'.DS.'log.txt';



 ?>

 <?php include_layout_template('admin_header.php');?>

 <a href="index.php">&laquo; Back</a> <br/> <br/>

 <h2>Log File</h2>

 <p><a href="logfile.php?clear=true">Clear log file</a></p>

 <?php 

 if($_GET["clear"] == "true") {
	file_put_contents($logfile, '');
	//Add the first log entry
	log_action('Logs Cleared', "by User ID {$session->user_id}");
	//redirect to the same page so the url won't have "clear=true"
	redirect_to('logfile.php');
}

 	if (file_exists($logfile) && is_readable($logfile) && $handle = fopen($logfile, 'r')) {
 		echo "<ul class=\"log-entries\">";
 		//while not at the end of the file
 		while(!feof($handle)) {
 			//get the next bit of info until a new line is reached
 			$entry = fgets($handle);
 			//no new line return until something else is there
 			if(trim($entry) != "") {
 				echo "<li>{$entry}</li>";
 			}
 		}
 		echo "</ul>";
 		fclose($handle);
 	} else {
 		echo "Could not read from {$logfile}.";
 	}

  ?>