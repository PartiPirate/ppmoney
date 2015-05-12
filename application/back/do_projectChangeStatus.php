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

$project = array();

$project["pro_id"] = $_REQUEST["id"];
$project["pro_status"] = $_REQUEST["status"];

$projectBo->update($project);

header("Location: projects.php");
?>