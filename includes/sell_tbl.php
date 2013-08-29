<?
// require common code
require_once("includes/common.php");

/*
 * string
 * mk_sell_links($symbol, $num)
 *
 * makes appropriate links for the sell page,
 * input is: the stock symbol, the number of stocks owned by the user
 * output is a string (not printed, just returned)
 */

prefetch_user_stocks();

$uid = (int) $_SESSION['uid'];
$DETAILED= false;
if (isset($_GET['detailed']) && $_GET['detailed'] == 1) {
	$DETAILED = true;
}

//same query also used in portfolio.php
$PortfolioQuery =
($DETAILED)
?	//if the user wants to see details, ie one row per stock purchase bundle...
	"SELECT portfolios.sid,
		portfolios.number_of_stocks,
		stocks.symbol,
		stocks.`name`
		FROM `$PORTFOLIOS_TBL` as portfolios
		LEFT JOIN `$STOCKS_TBL` as stocks ON stocks.sid = portfolios.sid
	WHERE portfolios.uid = $uid
	ORDER BY stocks.`name` ASC"
:
	"SELECT portfolios.sid,
		SUM(portfolios.number_of_stocks) as number_of_stocks,
		stocks.symbol,
		stocks.`name`
		FROM `$PORTFOLIOS_TBL` as portfolios
		LEFT JOIN `$STOCKS_TBL` as stocks ON stocks.sid = portfolios.sid
	WHERE portfolios.uid = $uid
	GROUP BY portfolios.sid
	ORDER BY stocks.`name` ASC"
;

$result = $mysqli->query($PortfolioQuery);
if (!$result or $mysqli->error) {
	apologize("Cannot display portfolio"); // . $mysqli->error during debuging
}

?>


<table id="sell_tbl" class="portfolio sell_tbl">
	<caption><h4 style="line-height: 3em;">Sell stocks from your portfolio</h4></caption>
	<tr class="portfolio pheader">
		<th class="pheader" colspan="2" >Stock</th>
		<!--	above cell spans 2 columns	-->
		<th class="pheader">Quantity</th>
		<th class="pheader">Stock Price</th>
		<th class="pheader">Total Value</th>
		<th class="pheader"># to sell</th>
	</tr>
<?

if ($result->num_rows != 0) {
	$i = 1;
	while ($row = $result->fetch_assoc()) {
		$CurrentPrice = get_most_recent_price_from_cache($row['sid']);
		if ($CurrentPrice === FALSE || !$CurrentPrice) {
			apologize("Invalid request to get_most_recent_price_from_cache().
					Unable to get current price for \$row['sid']={$row['sid']}, \$row['name']={$row['name']}");
		}
		$TotalValue = $CurrentPrice * $row['number_of_stocks'];
?>
	<tr class="portfolio">
		<td class="portfolio symbol"><?= $row['symbol'] ?></td>
		<td class="portfolio stockname"><?= $row['name'] ?></td>
		<td class="portfolio number right"><?= $row['number_of_stocks'] ?></td>
		<td class="portfolio currency"><?= $CurrentPrice ?></td>
		<td class="portfolio currency"><?= sprintf("%.2f", $TotalValue); ?></td>
		<td class="portfolio right"><?
			//all half quarter [textbox]
			$idSymbol = str_replace('.','DOT',$row['symbol']);
			$idSymbol = str_replace('-','DASH',$idSymbol);
			$idSymbol .= '_' . $i;
//			$links = "";
//			if ($row['number_of_stocks'] > 99) { //for position with more than 99 stocks, add a 'half' button
				$links =
				'<a href="#" title="halves the value." ' .
				'onclick="javascript:document.getElementById(\'quantity_' . $idSymbol . '\').value=Math.ceil(document.getElementById(\'quantity_' . $idSymbol . '\').value / 2);return false;">' .
				'&frac12;</a>' . PHP_EOL;
//			}
//			if ($row['number_of_stocks'] > 999) { //for position with more than 999 stocks, add a 'quarter' button
//				$links .=
//				' | <a href="#" title="a quarter of the total stock quantity." ' .
//				'onclick="javascript:document.getElementById(\'quantity_' . $idSymbol . '\').value=\'' . $row['number_of_stocks'] / 4 . '\';return false;">' .
//				'&frac14;</a>' . PHP_EOL;
//			}
			?>

			<div class="alignRight">
			<form name="sell_form_<?= $idSymbol ?>" id="<?= $idSymbol ?>" action="sell2.php">
				<label class="alignTopLeft" for="quantity">
					<span class="smallText"><?= $links ?>

					</span></label>&nbsp;
				<input type="text" name="quantity" value="<?= $row['number_of_stocks'] ?>" size="4" id="quantity_<?= $idSymbol ?>" />
				<input type="submit" value="Go" />
				<input type="hidden" name="symbol" value="<?= $row['symbol'] ?>" />
			</form>
			</div>
		</td>
	</tr>
<?
	$i++;
	}
}

$result->free();

?>
</table> <!-- /portfolio sell_tbl -->
