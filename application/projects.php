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

<nav><img src="assets/img/logo_pp.png" /></nav>

<main class="clearfix">
<h1><?php echo lang("pp_projects_title") ?></h1>

<?php 	foreach($projects as $projectId => $project) {?>
<a href="project.php?id=<?php echo $project["pro_id"]; ?>">
	<div class="project">
		<h2><?php echo $project["pro_label"]; ?></h2>
		<h3>Besoin de <?php echo number_format($project["pro_amount_goal"], 0); ?>&euro;</h2>
		<br />
		<h3>Vous avez donné <?php echo number_format($project["pro_gauge"]["gau_amount"], 0); ?>&euro; (<?php echo number_format($gauge["gau_percent_goal"], 0); ?>%)</h2>
		<br />
		<br />
		<br />
		<br />
		<h4 class="text-right">
			<?php 	switch($project["pro_status"]) {
						case "open":
							echo lang("pp_money_common_open");
							break;
						case "closed":
							echo lang("pp_money_common_closed");
							break;
						case "cancel":
							echo lang("pp_money_common_cancel");
							break;
					}?>
		</h4>
	</div>
</a>
<?php 	}?>

<?php include("footer.php");?>
</body>
</html>