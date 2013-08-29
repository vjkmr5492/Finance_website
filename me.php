<?php
// require common code
require_once("includes/common.php");

// the valid timezones
require_once('includes/timezones.php');

?>

<!DOCTYPE html>
<html>
	<head>
		<? $html_head_title = 'C$50 Finance: User options';require_once('includes/html_head.php'); ?>
		<script language="javascript" type="text/javascript">
			<!--
			function changepassword(toggle) {
				if (toggle == 'show') {
					document.forms['useroptions'].elements['changepass'].value = '1';
					document.getElementById('pwdtrigger').style.display = 'none';
					document.getElementById('changepasstable').style.display = '';
				} else {
					document.forms['useroptions'].elements['changepass'].value = '0';
					document.getElementById('pwdtrigger').style.display = '';
					document.getElementById('changepasstable').style.display = 'none';
				}
				return;
			}
			-->
		</script>
	</head>
	<body>
		<div id="top"><? require_once('includes/application_top.php'); ?>
		</div>
		<div id="middle">
			<? require_once('includes/me_form.php') ?>
		</div>
		<div id="bottom">
			<!-- TODO: allow user to change their time zone -->
		</div>
	</body>
</html>
