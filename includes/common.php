<?

/***********************************************************************
 * common.php
 *
 * Computer Science 50
 * Problem Set 7
 *
 * Code common to (i.e., required by) most pages.
 **********************************************************************/


// display errors and warnings but not notices
ini_set("display_errors", TRUE);
error_reporting(E_ALL ^ E_NOTICE);

// enable sessions, restricting cookie to /~username/pset7/
if (preg_match("{^(/~[^/]+/pset7/)}", $_SERVER["REQUEST_URI"], $matches)) {
	session_set_cookie_params(0, $matches[1]);
}
session_start();

// requirements
//require_once("constants.php");
require_once("../../finance.constants.php");
//MAKE IT SO finance.constants.php is outside the public_html folder, so you don't
//accidentally leak your DB info when/if we copy the project over
//require_once("../../../finance.constants.php");
require_once("helpers.php");
require_once("stock.php");

// require authentication for most pages
if (!preg_match("/(:?log(:?in|out)|register)\d*\.php$/", $_SERVER["PHP_SELF"])) {
	if (!isset($_SESSION["uid"]))
		redirect("login.php");
}

// ensure database's name, username, and password are defined
if (DB_NAME == "")
	apologize("You left DB_NAME blank.");
if (DB_USER == "")
	apologize("You left DB_USER blank.");
if (DB_PASS == "")
	apologize("You left DB_PASS blank.");

// connect to database server
/* @var $link mysqli */
//$mysqli = @mysqli_connect(DB_SERVER, DB_USER, DB_PASS);
$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS);
if (mysqli_connect_error()) { // Note example#1 @ http://php.net/manual/en/mysqli.connect.php
	apologize("Could not connect to database server. <br>Check DB_NAME, DB_PASS, and DB_USER in constants.php.");
}

// select database
if ($mysqli->select_db(DB_NAME) === FALSE) {
	apologize("Could not select database (" . DB_NAME . ").");
}

?>