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

//error_reporting(E_ALL);
$path = "../";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

include_once("config/database.php");
include_once("config/mail.php");

require_once("engine/bo/TransactionBo.php");

$fromDate = new DateTime();
$fromDate = $fromDate->sub(new DateInterval("P7D"));
$fromDate = $fromDate->format("Y-m-d");
$toDate = new DateTime();
$toDate = $toDate->sub(new DateInterval("P1D"));
$toDate = $toDate->format("Y-m-d");

echo "Treasurer batch from " . $fromDate . " to " . $toDate;

$transactionBo = TransactionBo::newInstance(openConnection());
$transactions = $transactionBo->getTransactions(array("tra_status" => "accepted", "tra_confirmed" => "1", "tra_from_date" => $fromDate, "tra_to_date" => $toDate));

$fileHandler = fopen("treasurer.csv", "w");

$headers = array("Référence", "Email", "Pseudo Forum", "Nom", "Prénom", "Adresse", "Code postal", "Ville", "Pays", "Téléphone", "Date", "Montant", "Don", "Adhésion", "Section locale", "Don à la section", "Projet", "Don au projet", "Don additionel au projet", "Election circo", "Don circo", "Inscription aux CR BN & CN", "Ventilation");
$fields = array("tra_reference", "tra_email", ">forumPseudo", "tra_lastname", "tra_firstname", "tra_address", "tra_zipcode", "tra_city", "tra_country", "tra_telephone", "tra_date", "tra_amount", ">donation", ">join", ">local>section", ">local>donation", ">project>code", ">project>donation", ">project>additionalDonation", ">election>circo", ">election>donation", ">reportSubscription", "tra_purpose");

fputcsv($fileHandler, $headers);

foreach ($transactions as $transaction) {
	$purpose = json_decode($transaction["tra_purpose"], true);
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

$subject = "[PartiPirate] Fichier Trésorier CB du $fromDate au $toDate";

$mailMessage = "Voilà, voilà";

$mail->Subject = subjectEncode($subject);
$mail->msgHTML(str_replace("\n", "<br>\n", utf8_decode($mailMessage)));
$mail->AltBody = utf8_decode($mailMessage);
$mail->addAttachment("treasurer.csv", "cb_$fromDate" . "_$toDate.csv");

//$headers = "From: " . $config["smtp"]["from.name"] . " <".$config["smtp"]["from.address"].">" ."\r\n" .
//"Reply-To: " . $config["smtp"]["from.name"] . " <".$config["smtp"]["from.address"].">" ."\r\n" .
//"To: afpp@partipirate.org, tresorier@partipirate.org\r\n" .
//"Bcc: Dev Don Pirate <contact@levieuxcedric.com>\r\n" .
//"X-Mailer: PHP/" . phpversion();

$from = $config["smtp"]["from.name"] . " <".$config["smtp"]["from.address"].">";

if (sendMail($from, "afpp@partipirate.org, tresorier@partipirate.org",
			$mail->Subject,
			str_replace("\n", "<br>\n", utf8_decode($mailMessage)),
			"treasurer.csv",
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

unlink("treasurer.csv");

echo "Batch end";

?>