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
include_once("config/paybox.php");
require_once("engine/bo/ProjectBo.php");
require_once("engine/bo/TransactionBo.php");
require_once("engine/utils/FormUtils.php");

// We sanitize the request fields
xssCleanArray($_REQUEST);

if (!isset($_REQUEST["iCertify"])) exit();

$connection = openConnection();

$transactionBo = TransactionBo::newInstance($connection);

$email = $_REQUEST["email"] ? $_REQUEST["email"] : $_REQUEST["renewEmail"];
$amount = str_replace(",", ".", $_REQUEST["donation"]);
$amount += (isset($_REQUEST["costRadio"]) ? str_replace(",", ".", $_REQUEST["costRadio"]) : 0);
$amount += (isset($_REQUEST["localDonation"]) ? str_replace(",", ".", $_REQUEST["localDonation"]) : 0);
$amount += (isset($_REQUEST["projectDonation"]) ? str_replace(",", ".", $_REQUEST["projectDonation"]) : 0);
$amount += (isset($_REQUEST["projectAdditionalDonation"]) ? str_replace(",", ".", $_REQUEST["projectAdditionalDonation"]) : 0);

$dateTime = date("c");

$purpose = array();

if (isset($_REQUEST["donation"])) {
	$purpose["donation"] = str_replace(",", ".", $_REQUEST["donation"]);
}
if (isset($_REQUEST["costRadio"])) {
	$purpose["join"] = str_replace(",", ".", $_REQUEST["costRadio"]);
}
if (isset($_REQUEST["localSection"]) && $_REQUEST["localSection"]) {
	$purpose["local"] = array();
	$purpose["local"]["section"] = $_REQUEST["localSection"];

	if (isset($_REQUEST["localDonation"]) && $_REQUEST["localDonation"]) {
		$purpose["local"]["donation"] = str_replace(",", ".", $_REQUEST["localDonation"]);
	}
}
if (isset($_REQUEST["forum"]) && isset($_REQUEST["pseudo"]) && $_REQUEST["pseudo"]) {
	$purpose["forumPseudo"] = $_REQUEST["pseudo"];
}
if (isset($_REQUEST["subscription"])) {
	$purpose["reportSubscription"] = true;
}
if (isset($_REQUEST["comment"]) && $_REQUEST["comment"]) {
	$purpose["comment"] = $_REQUEST["comment"];
}
if (isset($_REQUEST["projectId"]) && $_REQUEST["projectId"]) {
	$purpose["project"] = array();

	$projectBo = ProjectBo::newInstance($connection);
	$project = $projectBo->getProject($_REQUEST["projectId"]);

	$purpose["project"]["code"] = $project["pro_code"];
	$purpose["project"]["donation"] = str_replace(",", ".", $_REQUEST["projectDonation"]);
	$purpose["project"]["additionalDonation"] = str_replace(",", ".", $_REQUEST["projectAdditionalDonation"]);
}

$transaction = array();
$transaction["tra_amount"] = $amount;
$transaction["tra_email"] = $email;
$transaction["tra_date"] = $dateTime;
$transaction["tra_purpose"] = json_encode($purpose);

if (isset($_REQUEST["firstname"]) && $_REQUEST["firstname"]) $transaction["tra_firstname"] = $_REQUEST["firstname"];
if (isset($_REQUEST["lastname"]) && $_REQUEST["lastname"]) $transaction["tra_lastname"] = $_REQUEST["lastname"];
if (isset($_REQUEST["address"]) && $_REQUEST["address"]) $transaction["tra_address"] = $_REQUEST["address"];
if (isset($_REQUEST["zipcode"]) && $_REQUEST["zipcode"]) $transaction["tra_zipcode"] = $_REQUEST["zipcode"];
if (isset($_REQUEST["city"]) && $_REQUEST["city"]) $transaction["tra_city"] = $_REQUEST["city"];
if (isset($_REQUEST["country"]) && $_REQUEST["country"]) $transaction["tra_country"] = $_REQUEST["country"];
if (isset($_REQUEST["telephone"]) && $_REQUEST["telephone"]) $transaction["tra_telephone"] = $_REQUEST["telephone"];

$transactionBo->save($transaction);

$amount *= 100;
$reference = $transaction["tra_reference"];

if ((isset($purpose["donation"]) || isset($purpose["join"]) || isset($purpose["project"])) && $amount > 750000) {
	include_once("language/language.php");
	$data = array("error" => true, "title" => lang("error_title"), "message" => lang("error_donation_too_high"));

	echo json_encode($data);
	exit();
}

include ("engine/utils/paybox.php");

?>