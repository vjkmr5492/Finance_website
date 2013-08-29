<?
// require common code
require_once("includes/common.php");

prefetch_user_stocks();

$uid = (int) $_SESSION['uid'];
$DETAILED= false;
if (isset($_GET['detailed']) && $_GET['detailed'] == 1) {
	$DETAILED = true;
}
$PortfolioQuery =
($DETAILED)
?	//if the user wants to see details, ie one row per stock purchase bundle...
	"SELECT portfolios.sid,
		portfolios.number_of_stocks,
		portfolios.number_of_stocks as actual_number_of_stocks,
		portfolios.purchase_price,
		portfolios.purchase_time,
		stocks.symbol,
		stocks.`name`
		FROM `$PORTFOLIOS_TBL` as portfolios
		LEFT JOIN `$STOCKS_TBL` as stocks ON stocks.sid = portfolios.sid
	WHERE portfolios.uid = $uid
	ORDER BY stocks.`name` ASC"
:	//else the regular query
	"SELECT
		stocks.symbol,
		stocks.`name`,
		W.CalculatedAverage as purchase_price,
		W.number_of_stocks,
		W.sid,
		W.uid,
		portfolios.number_of_stocks as actual_number_of_stocks,
		portfolios.purchase_time
		FROM
		(SELECT
			SUM(weighted.weights)/SUM(weighted.num) as CalculatedAverage,
			SUM(weighted.num) as number_of_stocks,
			weighted.sid,
			weighted.uid
			FROM
			(SELECT (number_of_stocks * purchase_price) as weights,
				number_of_stocks as num,
				sid,
				uid
				FROM `$PORTFOLIOS_TBL`
				WHERE uid = $uid
				) as weighted
				GROUP BY weighted.sid
		) as W
		LEFT JOIN `$STOCKS_TBL` as stocks
			ON W.sid=stocks.sid
		LEFT JOIN `$PORTFOLIOS_TBL` as portfolios
			ON portfolios.sid=W.sid
			AND portfolios.uid=W.uid
	GROUP BY sid
	ORDER BY stocks.`name` ASC"
;

$result = $mysqli->query($PortfolioQuery);
if (!$result or $mysqli->error) {
	apologize("Cannot display portfolio"); // . $mysqli->error during debuging
}

?>

<h4>Your Portfolio</h4>
<table class="portfolio">
	<tr class="portfolio pheader">
		<th class="pheader" colspan="2" rowspan="2">Stock</th>
		<!--	above cell spans 2 columns			-->
		<th class="pheader" rowspan="2">Quantity</th><?
		if ($DETAILED)echo '
		<th class="pheader" rowspan="2">Time acquired</th>' ?>
		<th class="pheader" colspan="2">Stock Price</th>
		<!--	above cell spans 2 columns			-->
		<th class="pheader" colspan="2">Total Value</th>
		<!--	above cell spans 2 columns			-->
		<th class="pheader" rowspan="2">Total<br/>Gains / Losses</th>
	</tr>
	<tr>
		<!--	Stock bottom part1		-->
		<!--	Stock bottom part2		-->
		<!--	Quantity bottom			--><?
		if ($DETAILED) echo '
		<!--	When bottom			-->' ?>
		<th class="pheader">@Purchase</th>
		<th class="pheader">Current</th>
		<th class="pheader">@Purchase</th>
		<th class="pheader">Current</th>
		<!--	Gain/Losses bottom		-->
	</tr>
<?
$diffTotal = 0;
$StockValueSum = 0;
$haveCalced = false;
if ($result->num_rows != 0) {
	while ($row = $result->fetch_assoc()) {
		$CurrentPrice = get_most_recent_price_from_cache($row['sid']);
		if ($CurrentPrice === FALSE || !$CurrentPrice) {
			apologize("Invalid request to get_most_recent_price_from_cache().
					Unable to get current price for \$row['sid']={$row['sid']}, \$row['name']={$row['name']}");
		}
		//how much the user payed for the stocks
		$PurchaseValue = $row['purchase_price'] * $row['number_of_stocks'];
		//how much these stocks could sell for right now.
		$CurrentValue = $CurrentPrice * $row['number_of_stocks'];
		$StockValueSum += $CurrentValue; // keep the total value of stocks.
		$difference = $CurrentValue - $PurchaseValue;
		$diffTotal += $difference; // keep the total gain/loses of stocks.
		$color = "black";
		if (abs($difference) > 0.05) {
			if ($difference > 0)
				$color = "green";
			else
				$color = "red";
		}
		//this makes a distinction bettween when we have a 'real' price per stock,
		// from when we have a 'calculated average'.
		// if this is a calclulated average, put tildes (~) in front of the price/value
		$isCalc = false;
		if ($row['actual_number_of_stocks'] != $row['number_of_stocks']) {
			$isCalc = true;
			$haveCalced = true; // this can be used to offer a link to a detailed listing
		}
?>
	<tr class="portfolio">
		<td class="portfolio symbol"><?= $row['symbol'] ?></td>
		<td class="portfolio stockname"><?= $row['name'] ?></td>
		<td class="portfolio number right"><?= $row['number_of_stocks'] ?></td><?
		if ($DETAILED) echo '
		<td class="portfolio date right">' . my_strftime($row['purchase_time']) . '</td>' ?>
		<td class="portfolio currency" title="<?= $row['purchase_price'] ?>"><?=
			( ($isCalc) ? '<span class="tilde">~</span>' : "") . sprintf("%.2f", $row['purchase_price'])
		?></td>
		<td class="portfolio currency" title="<?= $CurrentPrice ?>"><?= sprintf("%.2f", $CurrentPrice) ?></td>
		<td class="portfolio currency" title="<?= $PurchaseValue ?>"><?=
			( ($isCalc) ? '<span class="tilde">~</span>' : "") . sprintf("%.2f", $PurchaseValue)
		?></td>
		<td class="portfolio currency" title="<?= $CurrentValue ?>"><?= sprintf("%.2f", $CurrentValue); ?></td>
		<td class="portfolio currency" style="color:<?= $color ?>;"><?= sprintf("%.2f", $difference); ?></td>
	</tr>
<?
	}
}
$result->free();

$myCashQuery = "SELECT cash FROM `$USERS_TBL` WHERE uid = $uid LIMIT 1";
$CashResult = $mysqli->query($myCashQuery);
$cash = 0;
if ($CashResult && $CashResult->num_rows == 1) {
	$row = $CashResult->fetch_row();
	$cash = $row[0];
}
$CashResult->free();
$color = "black";
if (abs($diffTotal) > 0.05) {
	if ($diffTotal > 0)
		$color = "green";
	else
		$color = "red";
}

?>
	<tr class="pfooter">
		<td colspan="<?= ($DETAILED) ? 8 : 7 ?>" class="pfooter">
			<div style="clear:none;float:right;">
			<table class="tbl_foot">
				<tr class="tbl_foot">
					<td class="tbl_foot bold right">Total Stock Value:</td>
					<td class="tbl_foot bold right"><?= sprintf("%.2f", $StockValueSum) ?></td>
				</tr>
				<tr class="tbl_foot">
					<td class="tbl_foot bold right">Cash balance:</td>
					<td class="tbl_foot bold right"><?= sprintf("%.2f", $cash) ?></td>
				</tr>
				<tr class="tbl_foot">
					<td class="tbl_foot bold right">TOTAL:</td>
					<td class="tbl_foot bold right"><?= sprintf("%.2f", $StockValueSum + $cash) ?></td>
				</tr>
			</table>
			</div>
		</td>
		<td class="pfooter currency bold alignTop" style="color:<?= $color ?>;"><?= sprintf("%.2f", $diffTotal) ?></td>
	</tr>
	<tr class="pfooterBottom">
		<td colspan="<?= ($DETAILED) ? 9 : 8 ?>" class="pfooterBottom">
		<div class="SmallDetailsLinkRelative"><?
		if ($haveCalced || $DETAILED) {
			if ($DETAILED) {
				echo '<a href="' . $_SERVER["PHP_SELF"] . '?detailed=0">less details</a>';
			} else {
				echo '<a href="' . $_SERVER["PHP_SELF"] . '?detailed=1">more details</a>';
			}
		}
		?></div>
		</td>
	</tr>
</table> <!-- /portfolio-->


