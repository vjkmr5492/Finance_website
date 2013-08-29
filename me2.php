<?php

// require common code
require_once('includes/common.php');

// the valid timezones
require_once('includes/timezones.php');

if (!isset($_POST['_useroptions_submit']) || '1' != $_POST['_useroptions_submit']) {
	apologize('Visiting pages you are not meant to visit? Neat!');
}
if (!isset($_POST['displayname']) || empty($_POST['displayname'])) {
	apologize('Please provide a displayname.');
}
if (strlen(trim($_POST['displayname'])) < 4) {
	apologize('Please provide a displayname longer than 3 characters.');
}
if (!isset($_POST['timezone']) || empty($_POST['timezone'])) {
	apologize('Please provide a timezone.');
}
//digs into $timezones and sees that the user provided timezone is actually in there.
if (!in_array($_POST['timezone'],$timezones)) {
	apologize('Please stop trying to hack this form; just choose a valid timezone from the menu. kthnxbai.');
}
if (!isset($_POST['changepass'])) {
	apologize('Please stop trying to hack this form; kthnxbai.');
}
// in the event the user is changing the password:
if ($_POST['changepass'] == 1) {
	if (	!isset($_POST['oldpassword'])	|| empty($_POST['oldpassword'])) {
		apologize('Please provide the current password.');
	}
	if (		!isset($_POST['password'])	|| empty($_POST['password']) ||
			!isset($_POST['password2'])	|| empty($_POST['password2'])
		) {
		apologize('Please provide a new password for your account.');
	}
	if ($_POST['password2'] != $_POST['password']) {
		apologize('New passwords do not match');
	}
	if (strlen($_POST['password']) < 4) {
		apologize('Please provide a new password longer than 3 characters.');
	}
	if ($_POST['oldpassword'] == $_POST['password']) {
		apologize('New password equals old password. That does not make sense.');
	}
}

$uid = (int) $_SESSION['uid'];
$sDisplayName = $mysqli->real_escape_string(trim($_POST["displayname"])); // trim spcs
$sTimezone = $mysqli->real_escape_string($_POST["timezone"]);
$changes = "";

if ($sDisplayName != $_SESSION['displayname']) {
	//first check that this display name is available (displaynames are unique....)
	$select = "SELECT displayname FROM `$USERS_TBL` WHERE displayname = '$sDisplayName' AND uid != $uid LIMIT 1";
	$result = $mysqli->query($select);
	if ($result && $result->num_rows == 1) {
		apologize("Display name: $sDisplayName, is already being used by someone else.");
	}
	$result->free();
	$changes .= " displayname='$sDisplayName', ";
	// also, change the value in the session on the fly, no need to log out
	$_SESSION['displayname'] = $sDisplayName;
}

if ($sTimezone != $_SESSION['timezone']) {
	$changes .= " timezone='$sTimezone', ";
	// also, change the value in the session on the fly, no need to log out
	$_SESSION['timezone'] = $sTimezone;
}

if ($_POST['changepass'] == 1) {
	//check the oldpassword field
	$md5OldPassword = md5( $_POST["oldpassword"] );
	$select = "SELECT password FROM `$USERS_TBL` WHERE password = '$md5OldPassword' AND uid = $uid LIMIT 1";
	$result = $mysqli->query($select);
	if (!($result && $result->num_rows == 1)) {
		apologize("Invalid old password. Forgotten it already?" . $md5OldPassword);
	}
	$result->free();
	$md5Password = md5( $_POST["password"] );
	$changes .= " password='$md5Password', ";
}

//remove trailing comma and spc from $changes
$changes = rtrim($changes," ,");

if (	$changes && strlen($changes) > 0) {
	$userQuery = "UPDATE `$USERS_TBL` SET $changes WHERE uid = $uid LIMIT 1";
	$rv = $mysqli->query($userQuery);
	if (!$rv or $mysqli->error) {
		apologize("Database error while modifying your options.");
	}
}

// redirect to portfolio
redirect("index.php");

?>