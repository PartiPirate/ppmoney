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

// Can only be call from CLI
if (php_sapi_name() != "cli") exit();

$monthes = array("Janvier", "Février", "Mars",
				 "Avril", "Mai", "Juin",
				 "Juillet", "Août", "Septembre",
				 "Octobre", "Novembre", "Decembre");

//error_reporting(E_ALL);
$path = "../";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

include_once("config/database.php");
include_once("config/mail.php");

require_once("engine/bo/TransactionBo.php");

$monthly = false;
$daily = false;
$fromDate = new DateTime();

if (isset($argv) && count($argv)) {
	foreach($argv as $argIndex => $argValue) {
		if ($argValue == "-m") {
			$monthly = true;
		}
		else if ($argValue == "-q") {
			$daily = true;
		}
		else if ($argValue == "-d") {
			$fromDate = new DateTime($argv[$argIndex + 1]);
		}
	}
}

$treasurerFileName = "treasurer.csv";

if ($monthly) {
	$fromDate->setDate($fromDate->format("Y"), $fromDate->format("m") - 1, 1);

	$subject = "[PartiPirate] Don-adhésions de " . $monthes[$fromDate->format("m") - 1] . " " . $fromDate->format("Y") . " (mensuel)";

	$fromDate = $fromDate->format("Y-m");

	$treasurerFileName = $fromDate . "-cb.csv";

	$toDate = $fromDate . "-32";
	$fromDate = $fromDate . "-00";
}
else if ($daily) {
	$fromDate = $fromDate->sub(new DateInterval("P1D"));

	$subject = "[PartiPirate] Don-adhésions du " . $fromDate->format("d") . " " . $monthes[$fromDate->format("m") - 1] . " " . $fromDate->format("Y") . " (journalier)";

	$fromDate = $fromDate->format("Y-m-d");
	$toDate = new DateTime();
	$toDate = $toDate->sub(new DateInterval("P1D"));
	$toDate = $toDate->format("Y-m-d");

	$treasurerFileName = $fromDate . "-cb.csv";
}
else {
	$fromDate = $fromDate->sub(new DateInterval("P7D"));
	$fromDate = $fromDate->format("Y-m-d");
	$toDate = new DateTime();
	$toDate = $toDate->sub(new DateInterval("P1D"));
	$toDate = $toDate->format("Y-m-d");

	$treasurerFileName = $fromDate . "_" . $toDate . "-cb.csv";

	$subject = "[PartiPirate] Don-adhésions du $fromDate au $toDate (hebdomadaire glissant)";
}

echo "Treasurer batch from " . $fromDate . " to " . $toDate . "\n";

$transactionBo = TransactionBo::newInstance(openConnection());
$transactions = $transactionBo->getTransactions(array("tra_status" => "accepted", "tra_confirmed" => "1", "tra_from_date" => $fromDate, "tra_to_date" => $toDate));

$fileHandler = fopen($treasurerFileName, "w");

$headers = array("Référence", "Email", "Pseudo Forum", "Nom", "Prénom", "Adresse", "Code postal", "Ville", "Pays", "Téléphone", "Date", "Montant", "Don", "Adhésion", "Section locale", "Don à la section", "Projet", "Don au projet", "Don additionel au projet", "Election circo", "Don circo", "Inscription aux CR BN & CN", "Ventilation");
$fields = array("tra_reference", "tra_email", ">forumPseudo", "tra_lastname", "tra_firstname", "tra_address", "tra_zipcode", "tra_city", "tra_country", "tra_telephone", "tra_date", "tra_amount", ">donation", ">join", ">local>section", ">local>donation", ">project>code", ">project>donation", ">project>additionalDonation", ">election>circo", ">election>donation", ">reportSubscription", "tra_purpose");

fputcsv($fileHandler, $headers);

$numberOfJoins = 0;
$numberOfDonations = 0;

$transactionSum = 0;
$transactionMin = 7500;
$transactionAvg = 0;
$transactionMax = 0;

$general = 0;
$projects = array();
$elections = array();
$sections = array();

foreach ($transactions as $transaction) {
// 	print_r($transaction);
// 	echo "\n";

	$purpose = json_decode($transaction["tra_purpose"], true);

	if (isset($purpose["join"])) {
		$numberOfJoins++;
	}
	else {
		$numberOfDonations++;
	}

	$transactionSum += $transaction["tra_amount"];
	$transactionMax = max($transactionMax, $transaction["tra_amount"]);
	$transactionMin = min($transactionMin, $transaction["tra_amount"]);
	$transactionMoy = $transactionSum / ($numberOfDonations + $numberOfJoins);

	if (isset($purpose["project"])) {
		if (!isset($projects[$purpose["project"]["code"]])) {
			$projects[$purpose["project"]["code"]] = 0;
		}
		$projects[$purpose["project"]["code"]] += $purpose["project"]["donation"] + $purpose["project"]["additionalDonation"];
	}

	if (isset($purpose["local"])) {
		if (!isset($sections[$purpose["local"]["section"]])) {
			$sections[$purpose["local"]["section"]] = 0;
		}
		if (isset($purpose["local"]["donation"])) {
			$sections[$purpose["local"]["section"]] += $purpose["local"]["donation"];
		}
	}

	if (isset($purpose["join"])) {
		$general += $purpose["join"];
	}

	if (isset($purpose["donation"])) {
		$general += $purpose["donation"];
	}

	$data = array();
	foreach($fields as $field) {
		if (substr($field, 0, 1) == ">") {
			$field = substr($field, 1);
			$parts = explode(">", $field);

			$fieldData = $purpose;
			foreach($parts as $part) {
				if (isset($fieldData[trim($part)])) {
					$fieldData = $fieldData[trim($part)];
				}
				else {
					$fieldData = "";
				}
			}

			$data[] = $fieldData;
		}
		else {
			$data[] = $transaction[$field];
		}
	}

	fputcsv($fileHandler, $data);
}

fclose($fileHandler);

echo "Treasurer CSV file done\n";

// readfile("treasurer.csv");
// unlink("treasurer.csv");
// exit();

$mail = getMailInstance();

$mail->setFrom($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
$mail->addReplyTo($config["smtp"]["from.address"], $config["smtp"]["from.name"]);

// L'adresse ici des trésoriers
$mail->addAddress("afpp@partipirate.org");
$mail->addAddress("tresorier@partipirate.org");

$mailMessage = "";

if (!count($transactions)) {
	$mailMessage = "Pas de transaction";
}
else {
	$mailMessage .= "Nombre de transactions : " . ($numberOfDonations + $numberOfJoins) . "\n";
	$mailMessage .= "Dont\n";
	$mailMessage .= "\tNombre de dons : " . ($numberOfDonations) . "\n";
	$mailMessage .= "\tNombre d'adhésions : " . ($numberOfJoins) . "\n";
	$mailMessage .= "\n";
	$mailMessage .= "Montant total : " . number_format($transactionSum, 2, ",", " ") . "E\n";
	$mailMessage .= "Montant minimum : " . number_format($transactionMin, 2, ",", " ") . "E\n";
	$mailMessage .= "Montant moyen : " . number_format($transactionMoy, 2, ",", " ") . "E\n";
	$mailMessage .= "Montant maximum : " . number_format($transactionMax, 2, ",", " ") . "E\n";
	$mailMessage .= "\n";
	$mailMessage .= "Budget général : +" . number_format($general, 2, ",", " ") . "E\n";
	$mailMessage .= "\n";
	if (count($sections)) {
		$mailMessage .= "Sections :\n";

		foreach($sections as $section => $amount) {
			$mailMessage .= "\tSection $section : +" . number_format($amount, 2, ",", " ") . "E\n";
		}
		$mailMessage .= "\n";
	}
	if (count($projects)) {
		$mailMessage .= "Projets :\n";

		foreach($projects as $project => $amount) {
			$mailMessage .= "\tProjet $project : +" . number_format($amount, 2, ",", " ") . "E\n";
		}
		$mailMessage .= "\n";
	}
}

$mail->Subject = subjectEncode($subject);
$mail->msgHTML(str_replace("\n", "<br>\n", utf8_decode($mailMessage)));
$mail->AltBody = utf8_decode($mailMessage);
$mail->addAttachment($treasurerFileName, $treasurerFileName);

echo "Message attachment filename : $treasurerFileName \n";
echo "Message subject : $subject \n";
echo "Message mail : \n$mailMessage \n";

$from = $config["smtp"]["from.name"] . " <".$config["smtp"]["from.address"].">";

if (sendMail($from, "afpp@partipirate.org, tresorier@partipirate.org",
			$mail->Subject,
			str_replace("\n", "<br>\n", utf8_decode($mailMessage)),
			$treasurerFileName,
			"",
			"")) {
	echo "File sent\n";
}

//if (mail("afpp@partipirate.org", $mail->Subject, $mail->AltBody, $headers)) {
//	echo "File sent\n";
//}

//if ($mail->send()) {
//	echo "File sent\n";
//}

unlink($treasurerFileName);

echo "Batch end\n";

?>