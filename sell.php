<?
// require common code
require_once("includes/common.php");
?>
<!DOCTYPE html>
<html>
	<head><? $html_head_title = 'C$50 Finance: Sell stocks'; require_once('includes/html_head.php'); ?>
		<script language="javascript" type="text/javascript">
			<!--
			function startTimer() {
				var t=setTimeout("ExpireTable()",59000); //59000 = 59 seconds
			}
			function ExpireTable() {
				var allForms = document.getElementsByTagName('form');
				for (var i = 0; i < allForms.length; i++) {
					var elements = allForms[i].elements;
					for(var j = 0; j < elements.length; j++) {
						if (elements[j].type == 'submit') {
							// this disables the submit buttons:
							elements[j].disabled = true;
						}
						if (elements[j].type == 'text') {
							// this changes the text color in the text input areas to gray:
							elements[j].style.color = 'gray';
						}
					}
				}
				//show the expiration message:
				document.getElementById('ExpirationMSG').style.display = '';
				//turn all text in the table to gray:
				document.getElementById('sell_tbl').style.color='gray';
			}
			-->
		</script>
	</head>
	<body onload="startTimer();">
		<div id="top"><? require_once('includes/application_top.php'); ?>
		</div>
		<div id="middle">
			<? require_once("includes/sell_tbl.php"); ?>
			<div id="ExpirationMSG" style="line-height: 2em;display:none;">Note: data becomes stale after 60 seconds; please <a href="#" onclick="window.location.reload()" title="Reload">reload</a> page to get fresh data.</div>
		</div>
		<div id="bottom">
			<p>&nbsp;</p><p>&nbsp;</p>
			
		</div>
	</body>
</html>
