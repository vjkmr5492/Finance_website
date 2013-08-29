<?
// require common code
require_once("includes/common.php");

//conditional error checks
if (!isset($_POST["username"]) || empty($_POST["username"])) {
	apologize("Please provide a username.");
}
if (!isset($_POST["displayname"]) || empty($_POST["displayname"])) {
	apologize("Please provide a displayname.");
}
if (strlen(trim($_POST["username"])) < 4) {
	apologize("Please provide a username longer than 3 characters.");
}
if (strlen(trim($_POST["displayname"])) < 4) {
	apologize("Please provide a displayname longer than 3 characters.");
}
if (		!isset($_POST["password"])	|| empty($_POST["password"]) ||
		!isset($_POST["password2"])	|| empty($_POST["password2"])
	) {
	apologize("Please provide a password for your account.");
}
if ($_POST["password2"] != $_POST["password"]) {
	apologize("passwords do not match");
}
if (strlen($_POST["password"]) < 4) {
	apologize("Please provide a password longer than 3 characters.");
}

// escape username and password for safety
$sUsername = $mysqli->real_escape_string(trim($_POST["username"])); // trim spcs
$sDisplayname = $mysqli->real_escape_string(trim($_POST["displayname"])); // trim spcs
$sPassword = md5($_POST["password"]); // allow leading/trailing spcs

$select = "SELECT uid FROM `$USERS_TBL` WHERE username='$sUsername'";

/* @var $nameCheck mysqli_result */
$nameCheck = $mysqli->query($select);

if ($nameCheck->num_rows) {
	apologize("This username is taken.");
}

$dselect = "SELECT uid FROM `$USERS_TBL` WHERE displayname='$sDisplayname'";

/* @var $nameCheck mysqli_result */
$dnameCheck = $mysqli->query($dselect);

if ($dnameCheck->num_rows) {
	apologize("This display name is taken.");
}

//finished checking conditions
//create new user

$insert =
"INSERT INTO `$USERS_TBL`
	(username,displayname,password,cash)
	VALUES
	('$sUsername','$sDisplayname','$sPassword',10000)";

/* http://php.net/manual/en/mysqli.query.php
 * $qObj = $mysqli->query($insert); //result can be boolean or a MySQLi_Result object
 * $last_insert_id = $mysqli->insert_id; // http://php.net/manual/en/mysqli.insert-id.php
 * $nrows = $mysqli->affected_rows; // http://php.net/manual/en/mysqli.affected-rows.php
 */
if ($mysqli->query($insert) && $mysqli->affected_rows == 1 && $mysqli->insert_id) {
	?>
	<!DOCTYPE html>
	<html>
		<head><? $html_head_title = 'C$50 Finance: Registered!'; require_once('includes/html_head.php'); ?>
		</head>
		<body>
			<div id="top">
				<a href="index.php"><img alt="C$50 Finance" src="images/logo.gif" /></a>
			</div>
			<div id="middle">
				<h2>Welcome aboard <?= $sUsername ?>!</h2>
				<p>Your account (#<?= $mysqli->insert_id ?>) has been created.</p>
			</div>

			<div id="bottom">
				You may now proceed to <a href="login.php">login</a> with your account.
			</div>
		</body>
	</html>
	<?
	/* -OR- if I wanted to blindly log in newly registered users without informing them the registration worked:
	 * $_SESSION["uid"] = $mysqli->insert_id;
	 * redirect("index.php");
	 */
} else {
	apologize("Unable to create user: $sUsername");
}

?>