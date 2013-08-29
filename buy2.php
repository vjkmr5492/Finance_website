<?
// require common code
require_once("includes/common.php");

if (!isset($_GET['symbol']) || empty($_GET['symbol'])) {
	apologize("You must provide a symbol.");
}
if (!isset($_GET['quantity']) || empty($_GET['quantity'])) {
	apologize("No quantity provided.");
}

// check if the symbol is only the characters: a-z A-Z 0-1 . _ -
if (!preg_match("/^[a-zA-Z0-1\._-]+$/", $_GET["symbol"])) {
	apologize("That does not appear to be a valid symbol.");
}

$sSymbol = $mysqli->real_escape_string(trim($_GET['symbol']));
$sQuantity = (int) $_GET['quantity'];

if (strlen($sSymbol) > 64) {
	apologize("Symbol name too long");
}
if (!($sQuantity > 0)) {
	apologize("Invalid quantity provided.");
}

$uid = (int) $_SESSION['uid'];
//how much cash does the user have? is it enough to make this purchase?
$selectCash = "SELECT cash FROM `$USERS_TBL` WHERE uid = $uid LIMIT 1";

$result = $mysqli->query($selectCash);
$cash = 0.0;
if($result && $result->num_rows == 1) {
	$row = $result->fetch_assoc();
	$cash = (float) $row['cash'];
	if (!$cash || $cash < 0) {
		apologize("Either you're broke, or I can't figure out how much money you have. :" . $cash);
	}
	$result->free();
} else {
	apologize("DB Error, unable to lookup your cash ammount.");
}

$s = lookup($_GET["symbol"]);
if (!($s)) {
	apologize("That does not appear to be a valid stock symbol (according to Yahoo finance).");
}

$cost = $s->price * $sQuantity;
if (!($cost > 0.0)) {
	apologize("Cannot figure out how much money this transaction would cost.");
}
if ($cost > $cash) {
	apologize("You don't have enough cash to complete this transaction.");
}

//we need an sid to be in this stock symbol
if(!$s->sid) {
	//make an effort to get a stock sid...
	$selectSid = "SELECT sid FROM `$STOCKS_TBL` WHERE symbol = '$sSymbol' LIMIT 1";
	$result = $mysqli->query($selectSid);
	if ($result && $result->num_rows == 1) {
		$row = $result->fetch_row();
		$s->sid = (int) $row[0];
		$result->free();
	}
}

//"what we got here is a failure to communicate"
if(!$s->sid) { // still no sid!?
	apologize("DB Error, Unable to retrieve stock id from local DB.");
}

// if we reached here, then proceed with the transaction
//remove the cost from the user's cash ammount.
$diff = (float) $cash - $cost;
$updateCash = "UPDATE `$USERS_TBL` SET cash = $diff WHERE uid = $uid LIMIT 1";

//dump( $updateCash);
if (!($mysqli->query($updateCash) && $mysqli->affected_rows == 1)) {
	dump( $updateCash);
	apologize("DB error. Unable to update your cash.");
}

$time = time();
$insertPortfolio =
	"INSERT INTO `$PORTFOLIOS_TBL`
		(uid, sid, number_of_stocks, purchase_price, purchase_time)
		VALUES
		($uid, {$s->sid}, $sQuantity, {$s->price}, $time)
		ON DUPLICATE KEY UPDATE purchase_time = VALUES(purchase_time) ,
		number_of_stocks = number_of_stocks + VALUES(number_of_stocks)";
		// you'll notice the last two lines of the query.
		// that, coupled with the unique_index(sid,purchase_price) on the table
		// is a quick and dirty way to group same price stock purchases together.

if (!($mysqli->query($insertPortfolio) && ($mysqli->affected_rows == 1 || $mysqli->affected_rows == 2))) {
	apologize("DB error. Unable to update your porfolio. <br />".
			"Also, sorry but... you probably got stiffed for \$$cost ! kthnxbai! : " . $mysqli->affected_rows );
}

//implement history here
$InsertHistory =
	"INSERT INTO `$HISTORY_TBL`
		(action, uid, sid, number_of_stocks, value, history_epoch)
		VALUES
		('purchase', $uid, {$s->sid}, $sQuantity, $cost, $time)";

if (!($mysqli->query($InsertHistory) && ($mysqli->affected_rows == 1 || $mysqli->affected_rows == 2))) {
	apologize("DB error. Unable to update your history. <br />");
}

// redirect to portfolio
redirect("index.php");

?>