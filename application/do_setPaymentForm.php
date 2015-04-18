<?php
include_once("config/database.php");
include_once("config/paybox.php");
require_once("engine/bo/ProjectBo.php");
require_once("engine/bo/TransactionBo.php");

if (!isset($_REQUEST["iCertify"])) exit();

$connection = openConnection();

$transactionBo = TransactionBo::newInstance($connection);

$email = $_REQUEST["email"] ? $_REQUEST["email"] : $_REQUEST["renewEmail"];
$amount = $_REQUEST["donation"];
$amount += (isset($_REQUEST["costRadio"]) ? $_REQUEST["costRadio"] : 0);
$amount += (isset($_REQUEST["localDonation"]) ? $_REQUEST["localDonation"] : 0);
$amount += (isset($_REQUEST["projectDonation"]) ? $_REQUEST["projectDonation"] : 0);
$amount += (isset($_REQUEST["projectAdditionalDonation"]) ? $_REQUEST["projectAdditionalDonation"] : 0);

$dateTime = date("c");

$purpose = array();

if (isset($_REQUEST["donation"])) {
	$purpose["donation"] = $_REQUEST["donation"];
}
if (isset($_REQUEST["costRadio"])) {
	$purpose["join"] = $_REQUEST["costRadio"];
}
if (isset($_REQUEST["localSection"]) && $_REQUEST["localSection"]) {
	$purpose["local"] = array();
	$purpose["local"]["section"] = $_REQUEST["localSection"];

	if (isset($_REQUEST["localDonation"]) && $_REQUEST["localDonation"]) {
		$purpose["local"]["donation"] = $_REQUEST["localDonation"];
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
	$purpose["project"]["donation"] = $_REQUEST["projectDonation"];
	$purpose["project"]["additionalDonation"] = $_REQUEST["projectAdditionalDonation"];
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

include ("engine/utils/paybox.php");

?>