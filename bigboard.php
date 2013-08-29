<?
// require common code
require_once('includes/common.php');
?>
<!DOCTYPE html>
<html>
	<head><? $html_head_title = 'C$50 Finance: The Big Board'; require_once('includes/html_head.php'); ?>
	</head>
	<body>
		<div id="top"><? require_once('includes/application_top.php'); ?>
		</div>
		<div id="middle">
<!--			<img src="images/construction.gif" />-->
			<? require_once('includes/bigboard_tbl.php'); ?>
		</div>
		<div id="bottom">
<!--			FIXED:Ugh, I know there's a bug, the board doesn't properly display users that don't currently own any stock, this is why:
			<pre style="text-align: left;">
	CREATE VIEW pset7_view_users_totals AS

		SELECT
			users.uid,
			users.displayname,
			SUM(portfolios.number_of_stocks*cache.price) + users.cash AS total_portfolio_value
			FROM
			pset7_users AS users
			LEFT JOIN pset7_portfolios AS portfolios
				ON portfolios.uid=users.uid
			LEFT JOIN pset7_view_cache_newest_price  AS cache
				ON cache.sid = portfolios.sid
			GROUP BY users.uid;
			</pre>-->
		</div>
	</body>
</html>