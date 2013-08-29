<?
// require common code
require_once("includes/common.php");

//lookup the symbol provided by the user, and display it's information

if (!isset($_GET["symbol"]) or empty($_GET["symbol"])) {
	apologize("You must provide a symbol.");
}

// check if the symbol is only the characters: a-z A-Z 0-1 . _ -
if (!preg_match("/^[a-zA-Z0-1\._-]+$/", $_GET["symbol"])) {
	apologize("That does not appear to be a valid symbol.");
}

$s = lookup($_GET["symbol"]);

if (!$s) {
	apologize("That does not appear to be a valid stock symbol (according to Yahoo finance).");
}

?>
<!DOCTYPE html>

<html>
	<head><? $html_head_title = 'C$50 Finance: Get Quote'; require_once('includes/html_head.php'); ?>
	</head>
	<body>
		<div id="top"><? require_once('includes/application_top.php'); ?>
		</div>
		<div id="middle">
			A share of <?= $s->name ?> (<?= $s->symbol ?>) currently costs $<?= $s->price  ?>.
			<br /><br />
			Perhaps you'd like to <a href="buy.php?symbol=<?= $s->symbol ?>">buy</a> it?
			<p>TODO: add more stuff here, ie list if user already owns shares of this company.</p>
		</div>
		<div id="bottom">
			<a href="index.php">portfolio</a>
		</div>
	</body>
</html>
