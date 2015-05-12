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
require_once("engine/bo/ProjectBo.php");

$connection = openConnection();

$projectBo = ProjectBo::newInstance($connection);
$projectId = intval($_REQUEST["pro_id"]);
$project = $projectBo->getProject($projectId);

if (!$project) {
	$project = array();
	$project["pro_id"] = 0;
	$project["pro_code"] = "PRJ_" . utf8_decode($_REQUEST["pro_label"]);
	$project["pro_code"] = str_replace(" ", "", $project["pro_code"]);
}

$project["pro_label"] = utf8_decode($_REQUEST["pro_label"]);
$project["pro_amount_goal"] = $_REQUEST["pro_amount_goal"];
$project["pro_content"] = utf8_decode($_REQUEST["pro_content"]);
$project["counterparties"] = $_REQUEST["counterparties"];

foreach($project["counterparties"] as $offset => $counterpart) {
// 	$project["counterparties"][$offset]["cpa_id"] = $counterpart["['cpa_id']"];
// 	$project["counterparties"][$offset]["cpa_amount"] = $counterpart["['cpa_amount']"];
	$project["counterparties"][$offset]["cpa_content"] = utf8_decode($counterpart["cpa_content"]);
}

$projectBo->save($project);

//print_r($project);

header("Location: project.php?id=" . $project["pro_id"]);
?>