<?
// require common code
require_once("includes/common.php");
?>
<!DOCTYPE html>
<html>
	<head><? $html_head_title = 'C$50 Finance: Get Quote'; require_once('includes/html_head.php'); ?>
	</head>
	<body>
		<div id="top"><? require_once('includes/application_top.php'); ?>
		</div>
		<div id="middle">
			<form action="quote2.php" method="get">
				<table class="tableCenter">
					<tr>
						<td>Symbol:</td>
						<td><input name="symbol" type="text" maxlength="32"/></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="submit" value="Get Quote" /></td>
					</tr>
				</table>
			</form>
		</div>
		<div id="bottom">
			This site uses <a href="http://finance.yahoo.com/">http://finance.yahoo.com/</a>. Symbols valid there, are valid here.
		</div>
	</body>
</html>
