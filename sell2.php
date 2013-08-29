<?php
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

//steps to take:
//1. check if user's portfolio does have this symbol in it, and in the required ammount
//2. get current price of stock, calculate monetary value
//3. edit (substract stock quantity, on partial sales) position from user's portfolio
//	 -or-
//	 completely remove (whole position sales) position from user's portfolio
//4. edit user's cash ammount (add sold stock value)
//5. during the above I also need to consider I'll need to do a history insert later on. so, I need the tail value

//find the sid for this stock
$SidQuery = "SELECT sid FROM `$STOCKS_TBL` WHERE symbol='$sSymbol' LIMIT 1";
$SidResult = $mysqli->query($SidQuery);
if (!$SidResult or $mysqli->error) {
	apologize("Failure to query database for stock id. :" . $sSymbol);
}
$row = $SidResult->fetch_row();
$sid = (int) $row[0];
$SidResult->free();
if (!$sid) {
	apologize("Stock unknown to this system. :" . $sid);
}
// fetch all entries in this porfolio for this sid (it can be more than one). and sum total stocks
$CheckPortfolio =
"SELECT 
	portfolios.pid,
	portfolios.number_of_stocks,
	portfolios.purchase_price
	FROM `$PORTFOLIOS_TBL` as portfolios
	WHERE portfolios.uid = $uid
	AND portfolios.sid = $sid
	ORDER BY purchase_time ASC"; // `name` quoted because it probably is a reserved SQL word methinks
$result = $mysqli->query($CheckPortfolio);
if (!$result or $mysqli->error) {
	apologize('Failed to get your portfolio.');
}

$totalstocks = 0;
$stockPositions = array();
//loop through the result rows and store them in $StockInstances
while ($row = $result->fetch_assoc()) {
	$totalStocks += $row['number_of_stocks'];
	$stockPositions[] = $row;
}
//if user actually has LESS stock than what they're trying to sell
if ($totalStocks < $sQuantity) {
	apologize("You can't sell what you don't have.<br/> Unlike in the real stock market... this is not allowed here!");
}
// step 1 done

prefetch_stocks(array($sid  => $sSymbol));

$price = get_most_recent_price_from_cache($sid);

$saleValue = (float) $price * $sQuantity;
//apologize ("UPDATE `$USERS_TBL` SET cash = cash + " . $saleValue . " WHERE uid = $uid");

//$purchaseValue will ONLY be relevant in the history insert
$purchaseValue = 0.0;
// step 2 done

//copy quantity
$StocksToSell = $sQuantity;

while ($StocksToSell > 0) {
	if (!(count($stockPositions) > 0)) {
		apologize('Failed to properly substract stocks from your portfolio.');
	}
	//... loop through $StockInstances and do magic
	$position = array_shift($stockPositions);
	if ($position['number_of_stocks'] == $StocksToSell) {
		//sell off this position, and we're done
		$purchaseValue += (float) $position['purchase_price'] * $position['number_of_stocks'];
		$sellQuery1 = "DELETE FROM `$PORTFOLIOS_TBL` WHERE pid=" . $position['pid'];
		$rv = $mysqli->query($sellQuery1);
		if (!$rv or $mysqli->error) {
			apologize("Database error while removing portfolio position.");
		}
		$StocksToSell = 0;
	} else if ($position['number_of_stocks'] > $StocksToSell) {
		//this is a partial sale of a larger position, edit portfolio accordingly. and we're done
		$diff = (int) $position['number_of_stocks'] - $StocksToSell;
		$purchaseValue += (float) $position['purchase_price'] * $diff;
		$sellQuery2 = "UPDATE `$PORTFOLIOS_TBL` SET number_of_stocks = $diff WHERE pid =" . $position['pid'];
		$rv = $mysqli->query($sellQuery2);
		if (!$rv or $mysqli->error) {
			apologize("Database error while modifying portfolio position.");
		}
		$StocksToSell = 0;
	} else { ////// if ($position['number_of_stocks'] < $StocksToSell)
		//we will sell off this position, and continue looking for further instances
		$purchaseValue += (float) $position['purchase_price'] * $position['number_of_stocks'];
		$sellQuery3 = "DELETE FROM `$PORTFOLIOS_TBL` WHERE pid=" . $position['pid'];
		$rv = $mysqli->query($sellQuery3);
		if (!$rv or $mysqli->error) {
			apologize("Database error while removing portfolio position.(2)");
		}
		$StocksToSell -= $position['number_of_stocks'];
	}
}
//step 3 done

$AddCashQuery = "UPDATE `$USERS_TBL` SET cash = cash + " . $saleValue . " WHERE uid = $uid";
//UPDATE `pset7_users` SET cash = cash + 9187.5600 WHERE uid = 7
$rv = $mysqli->query($AddCashQuery);
if (!$rv or $mysqli->error) {
	apologize("Database error while modifying your cash balance.");
}
//step 4 done

$saleTail = $saleValue - $purchaseValue; // tail positive means profit, tail negative means loss.
//implement history here
$time = time();
$InsertHistory =
	"INSERT INTO `$HISTORY_TBL`
		(action, uid, sid, number_of_stocks, value, sale_tail, history_epoch)
		VALUES
		('sale', $uid, $sid, $sQuantity, $saleValue, $saleTail, $time)";

if (!($mysqli->query($InsertHistory) && ($mysqli->affected_rows == 1 || $mysqli->affected_rows == 2))) {
	apologize("DB error. Unable to update your history. <br />" . $mysqli->affected_rows . " " . $InsertHistory);
}

// redirect to portfolio
redirect("index.php");

?>