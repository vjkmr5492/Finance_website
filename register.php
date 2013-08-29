<?
// require common code
require_once("includes/common.php");

//note to self: look at date_default_timezone_set later.
?>
<!DOCTYPE html>

<html>
	<head><? $html_head_title = 'C$50 Finance: Register'; require_once('includes/html_head.php'); ?>
	</head>
	<body>
		<div id="top">
			<a href="index.php"><img alt="C$50 Finance" src="images/logo.gif"></a>
		</div>
		<div id="middle">
			<form action="register2.php" method="post">
				<h4>Provide username and password to register a new account.</h4>
				<table class="tableCenter">
					<tr>
						<td style="text-align: right;">Username:</td>
						<td><input name="username" type="text" /></td>
					</tr>
					<tr>
						<td style="text-align: right;">Display name:</td>
						<td><input name="displayname" type="text" /></td>
					</tr>
					<tr>
						<td style="text-align: right;">Password:</td>
						<td><input name="password" type="password" /></td>
					</tr>
					<tr>
						<td style="text-align: right;">Re-type Password:</td>
						<td><input name="password2" type="password" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="submit" value="Register" /></td>
					</tr>
				</table>
			</form>
		</div>
		<div id="bottom">
			or <a href="login.php">login</a> with an existing account
		</div>
	</body>
</html>
