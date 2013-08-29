<?

// require common code
require_once("includes/common.php");

// escape username and password for safety
/* @var $sUsername safeQueryString */
$sUsername = $mysqli->real_escape_string(trim($_POST["username"]));
/* @var $sPassword safeQueryString */
$sPassword = md5($_POST["password"]);

// prepare SQL
$sql = "SELECT uid,displayname,timezone FROM `$USERS_TBL` WHERE username='$sUsername' AND password='$sPassword'";

// execute query
/* @var $result mysqli_result */
$result = $mysqli->query($sql);

// if we found a row, remember user and redirect to portfolio
if ($result && $result->num_rows == 1) {
	// grab row

	/* @var $row rowAssociativeArray */
	$row = $result->fetch_assoc();

	// cache uid in session
	$_SESSION["uid"] = (int) $row["uid"];

	// cache displayname in session
	$_SESSION["displayname"] = $row["displayname"];

	// cache timezone in session
	$_SESSION["timezone"] = $row["timezone"];

	//set the timezone
	date_default_timezone_set($row["timezone"]);
	
	// redirect to portfolio
	redirect("index.php");
}

// else report error
else
	apologize("Invalid username and/or password!");
?>