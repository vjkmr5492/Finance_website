<?
// require common code
require_once("includes/common.php");
?>
<!DOCTYPE html>
<html>
	<head><? $html_head_title = 'C$50 Finance: Log In'; require_once('includes/html_head.php'); ?>
	</head>
	<body>
		<div id="top">
			<a href="index.php"><img alt="C$50 Finance" src="images/logo.gif"></a>
		</div>
		<div id="middle">
			<form action="login2.php" method="post">
				<table class="tableCenter">
					<tr>
						<td class="alignRight">Username:</td>
						<td><input name="username" type="text"></td>
					</tr>
					<tr>
						<td class="alignRight">Password:</td>
						<td><input name="password" type="password"></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="submit" value="Log In"></td>
					</tr>
				</table>
			</form>
		</div>
		<div id="bottom">
			or <a href="register.php">register</a> for an account
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<p>What is this?</p>
			<p>My solution for <a href="http://cs50.tv/2010/fall/" target="_blank"> CS50</a>'s <a href="http://cdn.cs50.net/2010/fall/psets/7/pset7.pdf" target="_blank">pset7</a> <br />
			a virtual stockmarket, where you can compete against other players on a virtual stockmarket.<br />
			Good luck wining virtual money folks! :D <br />
			(these days, it's more likely to go completely broke though!)</p>
		</div>
	</body>
</html>
