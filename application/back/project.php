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

$projectBo = ProjectBo::newInstance($connection);
$transactionBo = TransactionBo::newInstance($connection);

$gaugeBo = GaugeBo::newInstance($connection);
$projectBo = ProjectBo::newInstance($connection);
$transactionBo = TransactionBo::newInstance($connection);

$projectId = intval($_REQUEST["id"]);
$project = $projectBo->getProject($projectId);

if (!$project) {
	$project = array();
	$project["pro_id"] = "";
	$project["pro_label"] = "";
	$project["pro_amount_goal"] = "0.00";
	$project["counterparties"] = array();
}

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

<form action="do_projectUpdate.php" method="post">
	<h2>Projet</h2>
	<input type="hidden" name="pro_id" value="<?php echo $project["pro_id"]; ?>" />
	<label><span>Nom du projet : </span><input type="text" name="pro_label" value="<?php echo $project["pro_label"]; ?>" /></label><br />
	<label><span>Objectif financier : </span><input type="text" name="pro_amount_goal" value="<?php echo $project["pro_amount_goal"]; ?>" /> &euro;</label><br />
	<label><span>Texte : </span><textarea
		style="width: 400px; height: 100px;"
		name="pro_content"><?php echo $project["pro_content"]; ?></textarea></label><br />

	<h2>Contreparties <span><a id="addCounterpart" href="#addCounterpart">Ajouter une contrepartie</a></span></h2>
	<div id="counterparts">
	<?php 	$offset = 0;

			$counterparties = array();
			$counterparty = array("cpa_id" => 0, "cpa_amount" => "0.00", "cpa_content" => "");
			$counterparties[] = $counterparty;

			foreach($project["counterparties"] as $counterparty) {
				$counterparties[] = $counterparty;
			}

			foreach($counterparties as $counterparty) {?>
		<div id="counterpart-<?php echo $offset;?>">
			<input type="hidden" name="counterparties[<?php echo $offset;?>][cpa_id]" value="<?php echo $counterparty["cpa_id"]; ?>" />
			<label><span>Objectif financier : </span><input type="text" name="counterparties[<?php echo $offset;?>][cpa_amount]" value="<?php echo $counterparty["cpa_amount"]; ?>" /> &euro;</label><br />
			<label><span>Contre-partie : </span><textarea
				style="width: 400px; height: 100px;"
				name="counterparties[<?php echo $offset;?>][cpa_content]"><?php echo $counterparty["cpa_content"]; ?></textarea></label><br />
		</div>
	<?php 		$offset++;
			}?>
	</div>

	<input type="submit" value="Sauver" />
</form>

</main>

<?php include("footer.php");?>
</body>
</html>