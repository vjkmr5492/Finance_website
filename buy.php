<?
// require common code
require_once("includes/common.php");
?>

<!DOCTYPE html>

<html>
	<head><? $html_head_title = 'C$50 Finance: Buy'; require_once('includes/html_head.php'); ?>
	</head>
	<body>
		<div id="top"><? require_once('includes/application_top.php'); ?>
		</div>
		<div id="middle">
			<form action="buy2.php" method="get">
				<table class="tableCenter">
					<tr>
						<td>Symbol:</td>
						<td><input name="symbol" type="text" maxlength="64" value="<?= $_GET['symbol'] ?>" /></td>
					</tr>
					<tr>
						<td>Quantity:</td>
						<td><input name="quantity" type="text" /></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" value="Buy" /></td>
					</tr>
				</table>
			</form>
		</div>
		<div id="bottom">
		
		</div>
	</body>
</html>