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

$projects = $projectBo->getProjects(array());

foreach($projects as $projectId => $project) {
	$gauge = array();
	$gauge["gau_searched_purpose"] = '"project":{"code":"'.$project["pro_code"].'"';
	$gauge["gau_from_date"] = "2015-01-01";
	$gauge["gau_amount_path"] = "join";
	$gauge["gau_amount_goal"] = intval($project["pro_amount_goal"]);

	$transactionBo->getGaugeTransactions($gauge);
	GaugeBo::normalize($gauge);

	$projects[$projectId]["pro_gauge"] = $gauge;
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

<ul>
	<?php 	foreach($projects as $project) {?>
		<li><span class="project-label"><?php echo $project["pro_label"]; ?></span>
		-
		<?php echo $project["pro_gauge"]["gau_amount"]; ?> &euro;
		sur <?php echo $project["pro_amount_goal"]; ?> &euro;

		-

		<a href="project.php?id=<?php echo $project["pro_id"]; ?>">Editer</a>

		<?php if ($project["pro_status"] != "open") {?>
			<a href="do_projectChangeStatus.php?status=open&id=<?php echo $project["pro_id"]; ?>">Ouvrir</a>
		<?php }?>
		<?php if ($project["pro_status"] != "canceled") {?>
			<a href="do_projectChangeStatus.php?status=canceled&id=<?php echo $project["pro_id"]; ?>">Annuler</a>
		<?php }?>
		<?php if ($project["pro_status"] != "finished") {?>
			<a href="do_projectChangeStatus.php?status=finished&id=<?php echo $project["pro_id"]; ?>">Finir</a>
		<?php }?>

		<?php //print_r($project); ?></li>
	<?php 	}?>
</ul>

<a href="project.php?id=0">Editer un nouveau projet</a>

</main>

<?php include("footer.php");?>
</body>
</html>