<?php /*
	Copyright 2015 Cédric Levieux, Parti Pirate

	This file is part of PPMoney.

    PPMoney is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    PPMoney is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with PPMoney.  If not, see <http://www.gnu.org/licenses/>.
*/
$relativeDirectory = "../";
set_include_path(get_include_path() . PATH_SEPARATOR . $relativeDirectory);

session_start();

if (!isset($_SESSION["login"])) {
	header("Location: index.php");
	exit();
}

include_once("config/database.php");
require_once("engine/bo/GaugeBo.php");
require_once("engine/bo/ProjectBo.php");
require_once("engine/bo/TransactionBo.php");

$connection = openConnection();

$transactionBo = TransactionBo::newInstance($connection);

$transactions = $transactionBo->getStats(array("tra_from_date" => "2015-01-01", "tra_to_date" => "2015-12-31"));

include_once("header.php");

?>
<body>

<nav><a href="https://www.partipirate.fr/" target="_blank"><img src="../assets/img/logo_pp.png" /></a></nav>

<main class="clearfix">
<h1><?php echo lang("pp_back_title") ?></h1>

<div class="text-right">
	<?php echo $_SESSION["login"]; ?> -
	<a href="projects.php">Projets</a> -
	<a href="transactions_stats.php">Statistiques</a> -
	<a href="do_disconnect.php">Deconnecter</a>
</div>

<ul>
	<?php 	foreach($transactions as $transaction) {?>
		<li <?php if(!$transaction["tra_transaction_date"]) {echo ' style="list-style: none;" '; } ?>>
		<?php echo $transaction["tra_transaction_date"]; ?> <?php if($transaction["tra_transaction_date"]) { echo "-"; } ?>
		<?php echo $transaction["tra_number"]; ?> transaction<?php if($transaction["tra_number"] > 1) { echo "s"; } ?>
		(
		<?php echo $transaction["tra_number_adhesions"]; ?> adh&eacute;sion<?php if($transaction["tra_number_adhesions"] > 1) { echo "s"; } ?>
		)
		:
		Min : <?php echo number_format($transaction["tra_min_amount"], 2); ?>&euro; -
		Moy : <?php echo number_format($transaction["tra_avg_amount"], 2); ?>&euro; -
		Max : <?php echo number_format($transaction["tra_max_amount"], 2); ?>&euro; =>
		<?php echo number_format($transaction["tra_amounts"], 2); ?>&euro;

		<?php //print_r($transaction); ?></li>
	<?php 	}?>
</ul>

</main>

<?php include("footer.php");?>
</body>
</html>