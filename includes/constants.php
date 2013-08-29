<?

/* * *********************************************************************
 * constants.php
 *
 * Computer Science 50
 * Problem Set 7
 *
 * Global constants.
 * ******************************************************************** */


// your database's name (i.e., username_pset7)
define("DB_NAME", "jharvard_pset7");

// your database's username
define("DB_USER", "jharvard");

// your database's password
define("DB_PASS", "crimson");

// hostname of course's database server
define("DB_SERVER", "localhost");

// URL for Yahoo Finance
define("YAHOO", "http://download.finance.yahoo.com/d/quotes.csv?f=sl1d1t1c1ohgn&s=");

//the time for which the cached values will live
define("CACHE_AGE",60); // 1 minute cache is enough.

//the time for which the prefetch function wont bother fetching new values.
define("PREFETCH_AGE", 50); // dont fetch new data if data is younger than that value

//these are here because it makes sence to have them here.
$USERS_TBL		= "pset7_users";
$STOCKS_TBL		= "pset7_stocks";
$PORTFOLIOS_TBL	= "pset7_portfolios";
$CACHE_TBL		= "pset7_cache";
$HISTORY_TBL	= "pset7_history";

$TOTALS_VIEW	= "pset7_view_users_totals";
$LASTPRICE_VIEW	= "pset7_view_cache_newest_price";

?>
