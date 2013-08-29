<?php
// require common code
require_once("includes/common.php");

$uid = (int) $_SESSION['uid'];

$historyQuery =
"SELECT history.sid,
	history.number_of_stocks,
	history.value,
	history.history_epoch,
	history.sale_tail,
	history.action,
	stocks.symbol,
	stocks.`name`
	FROM `$HISTORY_TBL` as history
	LEFT JOIN `$STOCKS_TBL` as stocks ON stocks.sid = history.sid
	WHERE history.uid = $uid
	ORDER BY history.history_epoch DESC"; // `name` quoted because it probably is a reserved SQL word methinks

$result = $mysqli->query($historyQuery);
if (!$result or $mysqli->error) {
	apologize("Cannot display history"); // . $mysqli->error during debuging
}

?>

<h4>Your transaction history</h4>
<table class="history">

<?

if ($result->num_rows != 0) {
	while ($row = $result->fetch_assoc()) {
		$stock = ( $row['number_of_stocks'] > 1) ? 'stocks' : 'stock';
		$transaction = $row['number_of_stocks'] .
				" " . $stock . " of " . $row['symbol'] . " (" . $row['name'] . ") " .
				"for \$" . $row['value'];

		$color = "black";
		if ($row['action'] == 'sale') {
			if ($row['sale_tail'] && $row['sale_tail'] < 0) {
				$color = "red";
				$transaction .= " at a loss of \$" . $row['sale_tail'];
			} else if ($row['sale_tail'] && $row['sale_tail'] > 0) {
				$color = "green";
				$transaction .= " at a profit of \$" . $row['sale_tail'];
			}
		}

		?>

	<tr class="history <?= $row['action'] ?>">
		<td class="history datetime"><?= my_strftime($row['history_epoch']) ?></td>
		<td class="history action"><?= $row['action'] ?></td>
		<td class="history transaction" style="color:<?= $color ?>;"><?= $transaction ?></td>
	</tr>

		<?
	}
}

?>

</table> <!-- /history -->
