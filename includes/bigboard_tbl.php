<?php

// require common code
require_once("includes/common.php");

$query = "SELECT displayname,total_portfolio_value FROM `$TOTALS_VIEW` ORDER BY total_portfolio_value DESC";
$result = $mysqli->query($query);
if (!$result or $mysqli->error) {
	apologize("Cannot display Big Board". $mysqli->error); // . $mysqli->error during debuging
}

?>

<table class="bigboard tableCenter">
	<tr class="bigboard pheader">
		<th class="bigboard pheader">Rank</th>
		<th class="bigboard pheader">Name</th>
		<th class="bigboard pheader">Value</th>
	</tr>
<?
if ($result->num_rows != 0) {
	$rank = 1;
	while ($row = $result->fetch_assoc()) {
		?>
	<tr>
		<td class="bigboard alignRight rankPadding"><?= $rank ?></td>
		<td class="bigboard"><?= $row['displayname'] ?></td>
		<td class="bigboard currency"><?= $row['total_portfolio_value'] ?></td>
	</tr>
		<?
		$rank++;
	}
}


?>
</table>