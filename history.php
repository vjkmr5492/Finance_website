<?php
// require common code
require_once("includes/common.php");

?>
<!DOCTYPE html>
<html>
	<head><? $html_head_title = 'C$50 Finance: Transaction History'; require_once('includes/html_head.php'); ?>
	</head>
	<body>
		<div id="top"><? require_once('includes/application_top.php'); ?>
		</div>
		<div id="middle">
			<? require_once("includes/history_tbl.php"); ?>
		</div>
		<div id="bottom">
			
		</div>
	</body>
</html>
