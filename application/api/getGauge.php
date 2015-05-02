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
/*
Nombre d'adhésions
http://don-beta.partipirate.org/api/getGauge.php?from_date=2015-01-01&to_date=2016-01-01&search=join&amount_path=join

Nombre d'adhésions MidiPy
http://don-beta.partipirate.org/api/getGauge.php?from_date=2015-01-01&to_date=2016-01-01&search=%22section%22:%22midipy%22&amount_path=join

Projet 1
http://don-beta.partipirate.org/api/getGauge.php?from_date=2015-01-01&to_date=2016-01-01&amount_path=project%3Edonation&search=%22project%22:{%22code%22:%22PRJ_PreservatifPirate%22
http://don-beta.partipirate.org/api/getGauge.php?from_date=2015-01-01&to_date=2016-01-01&amount_path=project%3EadditionalDonation&search=%22project%22:{%22code%22:%22PRJ_PreservatifPirate%22
http://don-beta.partipirate.org/api/getGauge.php?from_date=2015-01-01&to_date=2016-01-01&amount_path=project%3EadditionalDonation,project%3Edonation&search=%22project%22:{%22code%22:%22PRJ_PreservatifPirate%22
 */
$path = "../";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

include_once("config/database.php");
require_once("engine/bo/GaugeBo.php");
require_once("engine/bo/TransactionBo.php");

$connection = openConnection();

$gaugeBo = GaugeBo::newInstance($connection);
$transactionBo = TransactionBo::newInstance($connection);

$gauge = array();
if (isset($_REQUEST["search"])) {
	$gauge["gau_searched_purpose"] = $_REQUEST["search"];
}
else {
	//'"section":"midipy"'
	echo json_encode(array("error" => "api_missing_parameter", "mandatory" => "search"));
	exit();
}

if (isset($_REQUEST["to_date"])) {
	$gauge["gau_to_date"] = $_REQUEST["to_date"];
}
if (isset($_REQUEST["from_date"])) {
	$gauge["gau_from_date"] = $_REQUEST["from_date"];
}
if (isset($_REQUEST["amount_goal"])) {
	$gauge["gau_amount_goal"] = $_REQUEST["amount_goal"];
}

if (isset($_REQUEST["amount_path"])) {
	$gauge["gau_amount_path"] = $_REQUEST["amount_path"];
}
else {
	//"local>donation"
	echo json_encode(array("error" => "api_missing_parameter", "mandatory" => "amount_path"));
	exit();
}

$transactionBo->getGaugeTransactions($gauge);
GaugeBo::normalize($gauge);

unset($gauge["transactions"]);

echo json_encode($gauge);
?>