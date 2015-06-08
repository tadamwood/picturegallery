<?php
require_once("../includes/database.php");
require_once("../includes/user.php");

if (isset($db)) {
	echo "true";
} else {
	echo "false";
}
echo "<br />";

echo $db->escape_value("It's working?<br />");

//$sql = "INSERT INTO users (id, username, password, first_name, last_name)";
//$sql .= "VALUES (1, 'beckyrox', 'awsmpw', 'becky', 'richbitch')";
//$result = $db->query($sql);

$sql = "SELECT * FROM users WHERE id = 1";
$result_set = $db->query($sql);
$found_user = $db->fetch_array($result_set,MYSQL_BOTH);
echo $found_user['username'];

echo "<hr/>";
//calling a static function from user.php
$found_user = User::find_by_id(1);
echo $found_user['username'];

echo "<hr/>";
$user_set = User::find_all();
while ($user = $db->fetch_array($user_set)) {
	echo "User: ". $user['username'] . "<br/>";
	echo "Name: ". $user['first_name'] . " " . $user['last_name'] . "<br/><br/>";
}

?>