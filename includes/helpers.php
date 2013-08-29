<?

/* * *********************************************************************
 * helpers.php
 *
 * Computer Science 50
 * Problem Set 7
 *
 * Helper functions.
 * ******************************************************************** */


/*
 * void
 * apologize($message)
 *
 * Apologizes to user by displaying a page with message.
 */
function apologize($message) {
	// require template
	require_once("apology.php");

	// exit immediately since we're apologizing
	exit;
}

/*
 * void
 * dump($variable)
 *
 * Facilitates debugging by dumping contents of variable
 * to browser.
 */
function dump($variable) {
	// dump variable with some quick and dirty HTML
	require("dump.php");

	// exit immediately so that we can see what we printed
	exit;
}

/*
 * void
 * logout()
 *
 * Logs out current user, if any.  Based on Example #1 at
 * http://us.php.net/manual/en/function.session-destroy.php.
 */
function logout() {
	// unset any session variables
	$_SESSION = array();

	// expire cookie
	if (isset($_COOKIE[session_name()])) {
		if (preg_match("{^(/~[^/]+/pset7/)}", $_SERVER["REQUEST_URI"], $matches))
			setcookie(session_name(), "", time() - 42000, $matches[1]);
		else
			setcookie(session_name(), "", time() - 42000);
	}

	// destroy session
	session_destroy();
}

/*
 * Stock
 * retrieve($symbol)
 *
 * Returns a Stock by symbol (case-insensitively) else NULL if not found.
 */
function retrieve($symbol, $sid = 0) {
	// reject symbols that start with ^
	if (preg_match("/^\^/", $symbol))
		return NULL;

	// open connection to Yahoo
	if (($fp = @fopen(YAHOO . $symbol, "r")) === FALSE)
		return NULL;

	// download first line of CSV file
	if (($data = fgetcsv($fp)) === FALSE || count($data) == 1)
		return NULL;

	// close connection to Yahoo
	fclose($fp);

	// ensure symbol was found
	if ($data[2] == 0.00)
		return NULL;

	// instantiate a stock object
	$stock = new Stock();

	// remember stock's symbol and trades
	$stock->symbol = $data[0];
	$stock->price = $data[1];
	$stock->time = strtotime($data[2] . " " . $data[3]);
	//$stock->change = $data[4];
	//$stock->open = $data[5];
	//$stock->high = $data[6];
	//$stock->low = $data[7];
	$stock->name = trim($data[8]); // name last is safer!
	$stock->sid = $sid;

	//before we return it, store it in the DB
	cache_stock($stock);

	// return stock
	return $stock;
}

/*
 * Stock
 * lookup($symbol)
 *
 * Returns a stock by symbol (case-insensitively) else NULL if not found.
 * first looks if we have data cached in our table,
 * if the data is fresh (less than 5 minutes old) it returns that
 * else it relies on retrieve() to get fresh data.
 */
function lookup($usSymbol) {
	global $mysqli,$USERS_TBL,$STOCKS_TBL,$PORTFOLIOS_TBL,$CACHE_TBL,$HISTORY_TBL;
	$sSymbol = $mysqli->real_escape_string(trim($usSymbol));
	$sCacheTime = (int) time() - CACHE_AGE; // 1 minute cache only
	$cacheQuery =
	"SELECT stocks.sid,stocks.name, cache.price
		FROM `$STOCKS_TBL` as stocks, `$CACHE_TBL` as cache
		WHERE stocks.sid=cache.sid
		AND stocks.symbol = '$sSymbol'
		AND cache.quote_epoch > $sCacheTime
		ORDER BY cache.quote_epoch DESC
		LIMIT 1"; // only one row

	$result = $mysqli->query($cacheQuery);

	// if we found a row, load data and return it.
	if ($result && $result->num_rows == 1) {
		// grab row
		/* @var $row rowAssociativeArray */
		$row = $result->fetch_assoc();

		//limited data set, but we only really care about these values anyways:
		/* @var $stock Stock */
		$stock = new Stock();
		$stock->symbol = $usSymbol;
		$stock->price = $row['price'];
		$stock->name = $row['name'];
		$stock->sid = $row['sid'];

		$result->free();
		return $stock;
	} else {
		//make an effort to get a stock sid...
		$selectSid = "SELECT sid FROM `$STOCKS_TBL` WHERE symbol = '$sSymbol' LIMIT 1";
		$result = $mysqli->query($selectSid);
		if ($result && $result->num_rows == 1) {
			$row = $result->fetch_row();
			$sid = (int) $row[0];
			$result->free();
			return retrieve($usSymbol,$sid);
		}
		return retrieve($usSymbol);
	}
}

/*
 * void
 * cache_stock($stock)
 * stores a stock's data into the DB for future retrieval
 * this could be called via retrieve(), in which case the stock symbol doesn't have an sid in it. (that's ok, it'll be found)
 * prefetch_stocks() also calls this (with a sid in place)
 */
function cache_stock($stock) {
	global $mysqli,$USERS_TBL,$STOCKS_TBL,$PORTFOLIOS_TBL,$CACHE_TBL,$HISTORY_TBL;
	$sSymbol = $mysqli->real_escape_string($stock->symbol);
	$sName = $mysqli->real_escape_string($stock->name);
	
	$sid = 0;
	//if the provided stock object had a sid value in it, take that for granted.
	if ($s->sid && $s->sid > 0) {
		$sid = (int) $s->sid;
	} else {
		//make a sid for this stock in our stocks table -or- fetch it if we already have one
		$selectSid = "SELECT sid FROM `$STOCKS_TBL` WHERE symbol = '$sSymbol' LIMIT 1";
		$result = $mysqli->query($selectSid);
		if ($result && $result->num_rows == 1) {
			$row = $result->fetch_row();
			$sid = (int) $row[0];
			$result->free();
		} else { //we didnt have the stock in the stocks table, insert it.
			$insertQuery = "INSERT INTO `$STOCKS_TBL` (symbol,`name`) VALUES ('$sSymbol', '$sName')";
			if (!$mysqli->query($insertQuery)) {
				apologize("DB error.(0)");
			}
			if (!$mysqli->affected_rows == 1) {
				apologize("DB error.(1) :" . $mysqli->affected_rows);
			}
			$sid = (int) $mysqli->insert_id;
			if (!$sid) {
				apologize("DB error.(2) :" . $sid);
			}
		}
	}
	// in any case, by now we should have the sid for this stock
	$sCurTime = (int) time();
	$sPrice = $mysqli->real_escape_string($stock->price);
	$cacheQuery = "INSERT INTO `$CACHE_TBL` (sid,price,quote_epoch) VALUES ($sid,$sPrice,$sCurTime)";
	if (!($mysqli->query($cacheQuery) && $mysqli->affected_rows == 1)) {
		apologize("Sorry DB error.(3)");
	}
}


/*
 * void
 * redirect($destination)
 * 
 * Redirects user to destination, which can be
 * a URL or a relative path on the local host.
 *
 * Because this function outputs an HTTP header, it
 * must be called before caller outputs any HTML.
 */
function redirect($destination) {
	// handle URL
	if (preg_match("/^http:\/\//", $destination))
		header("Location: " . $destination);

	// handle absolute path
	else if (preg_match("/^\//", $destination)) {
		$protocol = (@$_SERVER["HTTPS"]) ? "https" : "http";
		$host = $_SERVER["HTTP_HOST"];
		header("Location: $protocol://$host$destination");
	}

	// handle relative path
	else {
		// adapted from http://www.php.net/header
		$protocol = (@$_SERVER["HTTPS"]) ? "https" : "http";
		$host = $_SERVER["HTTP_HOST"];
		$path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
		header("Location: $protocol://$host$path/$destination");
	}

	// exit immediately since we're redirecting anyway
	exit;
}


/*
 * void
 * prefetch_stocks($sidArray)
 *
 * $sidArray must be a numeric array where the numeric key is the 'sid' and the content is the 'symbol'
 * here's an example:
 * $sidArray =
 * Array
 * (
 *    [2] => AAPL
 *    [1] => MSFT
 * )
 * ie in the above example MSFT has an sid of 1 on our database.
 */
function prefetch_stocks($sidArray) {
	global $mysqli,$USERS_TBL,$STOCKS_TBL,$PORTFOLIOS_TBL,$CACHE_TBL,$HISTORY_TBL;
	//YAHOO allows multiple symbols on the same request, so leverage that
	//will be the parameter string used with the yahoo URL
	$symbols = "";
	//will be a reverse directory so we can also attach the sid value onto the stock object later on.
	$reverseArray = array();
	//this may or may not be a good idea: check local db for each symbol -
	// if all symbols have fresh data dont fetch fresher data... but is it worth the mysql overhead?
	//populate the reverse directory and the symbols string.
	foreach ($sidArray as $sid => $sym) {
		//connect to local db, check age, if age > PREFETCH_AGE fetch new data.
		$timeDiff = (int) time() - PREFETCH_AGE;
		// checking one by one is probably stupid, this feature would be better if I checked them all in one big ass query
		$sid = (int) $sid;
		$pfQuery =
		"SELECT quote_epoch
			FROM `$CACHE_TBL`
			WHERE sid= $sid
			AND quote_epoch > $timeDiff
			LIMIT 1";
		$pfResult = $mysqli->query($pfQuery);
		if (!$pfResult || $pfResult->num_rows != 1) {
			$symbols = $symbols . $sym . "+";
		}
		$pfResult->free();
		//$symbols = $symbols . $sym . "+";
		$reverseArray[$sym] = $sid;
	}
	$symbols = rtrim($symbols,"+"); // chop off the trailing '+' character.

	if (strlen($symbols) > 0) {
		// open connection to Yahoo
		if (($fp = @fopen(YAHOO . $symbols, "r")) === FALSE)
			return;

		//loop through the CSV data
		while(($data = fgetcsv($fp)) !== FALSE) {
			if (!$data || count($data) == 1 || $data[2] == 0.00) next;

			$stock = new Stock();
			$stock->symbol = $data[0];
			$stock->price = $data[1];
			$stock->time = strtotime($data[2] . " " . $data[3]);
			$stock->name = trim($data[8]); // name last is safer!

			$stock->sid = (int) $reverseArray[ $data[0] ];

			cache_stock($stock);
		}

		// close connection to Yahoo
		fclose($fp);
	}
}

/*
 * void
 * prefetch_user_stocks(void)
 *
 * checks that we have fresh(-ish) info for all stocks on this user's portfolio
 */
function prefetch_user_stocks() {
	global $mysqli,$USERS_TBL,$STOCKS_TBL,$PORTFOLIOS_TBL,$CACHE_TBL,$HISTORY_TBL;
	$prefetchUserStocks =
	"SELECT stocks.symbol,
		DISTINCT portfolios.sid
		FROM `$PORTFOLIOS_TBL` as portfolios, `$STOCKS_TBL` as stocks
		WHERE stocks.sid=portfolios.sid
		AND portfolios.uid=" . $_SESSION['uid'];
	$prefetchResult = $mysqli->query($prefetchUserStocks);
	if ($prefetchResult && $prefetchResult->num_rows > 0) {
		$sidArray = array();
		while($row = $prefetchResult->fetch_assoc()) {
			$sidArray[ $row['sid'] ] = $row['symbol'];
		}
		$prefetchResult->free();
		prefetch_stocks($sidArray);
	}
}

/*
 * void
 * prefetch_everyones_stocks(void)
 *
 * checks that we have fresh(-ish) info for all stocks on everyones' portfolio
 */
function prefetch_everyones_stocks() {
	global $mysqli,$USERS_TBL,$STOCKS_TBL,$PORTFOLIOS_TBL,$CACHE_TBL,$HISTORY_TBL;
	$prefetchAllStocks =
	"SELECT stocks.symbol,
		DISTINCT portfolios.sid
		FROM `$PORTFOLIOS_TBL` as portfolios, `$STOCKS_TBL` as stocks
		WHERE stocks.sid=portfolios.sid";
	$prefetchResult = $mysqli->query($prefetchAllStocks);
	if ($prefetchResult && $prefetchResult->num_rows > 0) {
		$sidArray = array();
		while($row = $prefetchResult->fetch_assoc()) {
			$sidArray[ $row['sid'] ] = $row['symbol'];
		}
		$prefetchResult->free();
		prefetch_stocks($sidArray);
	}
}



/*
 * float (or actually string, whatever..)
 * get_most_recent_price_from_cache($sid)
 * 
 * does_what_the_long_function_name_sais()
 * $sid is a stock id.
 * returns FALSE on failure
 */
function get_most_recent_price_from_cache($sid) {
	global $mysqli,$USERS_TBL,$STOCKS_TBL,$PORTFOLIOS_TBL,$CACHE_TBL,$HISTORY_TBL;
	$sid = (int) $sid;
	if (!$sid) apologize("Invalid request to get_most_recent_price_from_cache().");

	$cacheQuery =
	"SELECT price
		FROM `$CACHE_TBL`
		WHERE sid = $sid
		ORDER BY quote_epoch DESC
		LIMIT 1";
	$result = $mysqli->query($cacheQuery);
	if (!$result || $result->num_rows != 1 || $mysqli->error) {
		apologize("Failed to get_most_recent_price_from_cache() " . $mysqli->error );
	}
	$row = $result->fetch_row();
	$price = $row[0];
	$result->free();
	return $price;
}

function my_strftime($epoch) {
	/*
	 * php.net/manual/en/function.strftime.php
	 * %F	Same as "%Y-%m-%d" (commonly used in database datestamps)
	 * %T	Same as "%H:%M:%S"
	 */
	return strftime("%F %T", $epoch);
}

?>
