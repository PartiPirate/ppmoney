<?php
/*
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

// To protect from direct call
if (!isset($hookEnabled)) exit();
if (!$transaction) exit();

include_once("engine/bo/GaletteBo.php");

$purpose = json_decode($transaction["tra_purpose"], true);

//print_r($purpose);

$galetteDatabase = isset($config["galette"]["database"]) ? $config["galette"]["database"] : $config["database"]["database"];
//$galetteConnection = openConnection($galetteDatabase);

//echo "galetteDatabase $galetteDatabase <br />";

$galetteBo = GaletteBo::newInstance($connection, $galetteDatabase);

//echo "galetteDatabase $galetteDatabase <br />";

$member = $galetteBo->getMemberByMail($transaction["tra_email"]);
//print_r($member);

if (isset($purpose["join"])) {
	$status = $galetteBo->getStatusByLabel("Active member");
}
else {
	$status = $galetteBo->getStatusByLabel("Non-member");
}

//print_r($status);

if ($member == null) {
	$member = array();
	$member["id_statut"] = $status["id_statut"];
//	$member["date_crea_adh"] = date("c");
	$member["date_crea_adh"] = $transaction["tra_date"];
}

// Echeance dans un an : date("Y-m-d")."T".date("H:i:s")
//$echeance = new DateTime();

$echeance = new DateTime($transaction["tra_date"]);

// echo "!!";
// print_r($echeance);
// echo "!!<br>";

$oneYear = new DateInterval("P1Y");
$echeance = $echeance->add($oneYear);
$echeance = $echeance->format("Y-m-d H:i:s");

if (isset($purpose["join"])) {
	$member["id_statut"] = $status["id_statut"];
	$member["date_echeance"] = $echeance;
}

$member["email_adh"] = $transaction["tra_email"];
$member["nom_adh"] = utf8_decode($transaction["tra_lastname"]);
$member["prenom_adh"] = utf8_decode($transaction["tra_firstname"]);
$member["adresse_adh"] = utf8_decode($transaction["tra_address"]);
$member["cp_adh"] = $transaction["tra_zipcode"];
$member["ville_adh"] = utf8_decode($transaction["tra_city"]);
$member["pays_adh"] = utf8_decode($transaction["tra_country"]);
$member["tel_adh"] = $transaction["tra_telephone"];
//$member["date_modif_adh"] = date("c");
$member["date_modif_adh"] = $transaction["tra_date"];
$member["activite_adh"] = 1;

if (isset($purpose["forumPseudo"])) {
	$member["pseudo_adh"] = $purpose["forumPseudo"];
}

$galetteBo->saveMember($member);

//echo "Member saved";

// insert transaction
$galetteTransaction = array("id_adh" => $member["id_adh"]);
$galetteTransaction["trans_date"] = $transaction["tra_date"];
$galetteTransaction["trans_amount"] = $transaction["tra_amount"];
$galetteTransaction["trans_desc"] = "CB_" . $transaction["tra_reference"];

$galetteBo->insertTransaction($galetteTransaction);

if (isset($purpose["donation"]) && $purpose["donation"] && $purpose["donation"] != 0) {
	$typeCotisation = $galetteBo->getTypeCotisationByLabel("donation in money");
	$cotisation = array();
	$cotisation["id_type_cotis"] = $typeCotisation["id_type_cotis"];
	$cotisation["id_adh"] = $member["id_adh"];
	$cotisation["montant_cotis"] = $purpose["donation"];
	$cotisation["date_enreg"] = $transaction["tra_date"];
	$cotisation["trans_id"] = $galetteTransaction["trans_id"];
	$cotisation["type_paiement_cotis"] = 2;
	$cotisation["info_cotis"] = "";
	$cotisation["date_debut_cotis"] = $member["date_modif_adh"];
	$cotisation["date_fin_cotis"] = '';

	$galetteBo->insertCotisation($cotisation);

	$additionals = array();
	$additionals[] = array("item_id" => $cotisation["id_cotis"], "field_id" => 4, "field_form"=> "contrib",
							"val_index" => 1,  "field_val" => 2);
	$galetteBo->insertAdditionals($additionals);

//	echo "Donation saved";
}

if (isset($purpose["join"]) && $purpose["join"] && $purpose["join"] != 0) {
	$typeCotisation = $galetteBo->getTypeCotisationByLabel("annual fee");
	$cotisation = array();
	$cotisation["id_type_cotis"] = $typeCotisation["id_type_cotis"];
	$cotisation["id_adh"] = $member["id_adh"];
	$cotisation["montant_cotis"] = $purpose["join"];
	$cotisation["date_enreg"] = $transaction["tra_date"];
	$cotisation["trans_id"] = $galetteTransaction["trans_id"];
	$cotisation["type_paiement_cotis"] = 2;
	$cotisation["info_cotis"] = "";
	$cotisation["date_debut_cotis"] = $member["date_modif_adh"];
	$cotisation["date_fin_cotis"] = $echeance;

	$galetteBo->insertCotisation($cotisation);

	$additionals = array();
	$additionals[] = array("item_id" => $cotisation["id_cotis"], "field_id" => 4, "field_form"=> "contrib",
			"val_index" => 1,  "field_val" => 0);
	$galetteBo->insertAdditionals($additionals);

//	echo "Cotisation saved";

	// Insert in SL
	if (isset($purpose["local"]) && isset($purpose["local"]["section"])) {
		$sectionName = utf8_decode($purpose["local"]["section"]);

		$section = $galetteBo->getSectionByName($sectionName);
		// insert section cotiz
		$typeCotisation = $galetteBo->getTypeCotisationByLabel("annual fee");
		$cotisation = array();
		$cotisation["id_type_cotis"] = $typeCotisation["id_type_cotis"];
		$cotisation["id_adh"] = $member["id_adh"];
		$cotisation["montant_cotis"] = $purpose["local"]["donation"] ? $purpose["local"]["donation"] : "0";
		$cotisation["date_enreg"] = $transaction["tra_date"];
		$cotisation["trans_id"] = $galetteTransaction["trans_id"];
		$cotisation["type_paiement_cotis"] = 2;
		$cotisation["info_cotis"] = $sectionName;
		$cotisation["date_debut_cotis"] = $member["date_modif_adh"];
		$cotisation["date_fin_cotis"] = $echeance;

		$galetteBo->insertCotisation($cotisation);

		$additionals = array();
		$additionals[] = array("item_id" => $cotisation["id_cotis"], "field_id" => 4, "field_form"=> "contrib",
				"val_index" => 1,  "field_val" => 1);
		$galetteBo->insertAdditionals($additionals);

//		echo "Locale Cotisation saved";
	}
	else {
		$section = $galetteBo->getSectionByName("Sans section");
	}

	$memberInGroup = array();
	$memberInGroup["id_adh"] = $member["id_adh"];
	$memberInGroup["id_group"] = $section["id_group"];
	$galetteBo->insertMemberInGroup($memberInGroup);
}

if (isset($purpose["project"]) && $purpose["project"]) {
	$typeCotisation = $galetteBo->getTypeCotisationByLabel("partnership");
	$cotisation = array();
	$cotisation["id_type_cotis"] = $typeCotisation["id_type_cotis"];
	$cotisation["id_adh"] = $member["id_adh"];
	$cotisation["montant_cotis"] = $purpose["project"]["donation"] + $purpose["project"]["additionalDonation"];
	$cotisation["date_enreg"] = $transaction["tra_date"];
	$cotisation["trans_id"] = $galetteTransaction["trans_id"];
	$cotisation["type_paiement_cotis"] = 2;
	$cotisation["info_cotis"] = utf8_decode("Projet : " . $purpose["project"]["code"] .
								", Don : " . $purpose["project"]["donation"] .
								", Don supplémentaire : " . $purpose["project"]["additionalDonation"]);
	$cotisation["date_debut_cotis"] = $member["date_modif_adh"];
	$cotisation["date_fin_cotis"] = '';

	$galetteBo->insertCotisation($cotisation);

	$additionals = array();
	$additionals[] = array("item_id" => $cotisation["id_cotis"], "field_id" => 4, "field_form"=> "contrib",
			"val_index" => 1,  "field_val" => 4);
	$galetteBo->insertAdditionals($additionals);

//	echo "Project Cotisation saved";
}
?>