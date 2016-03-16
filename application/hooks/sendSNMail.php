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

include_once("config/mail.php");

$purpose = json_decode($transaction["tra_purpose"], true);

//print_r($transaction);

if (isset($purpose["join"])) {

	$mail = getMailInstance();

	$mail->setFrom($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
	$mail->addReplyTo($config["smtp"]["from.address"], $config["smtp"]["from.name"]);

	// L'adresse ici des SN
	$mail->addAddress("secretaires-nationaux@lists.partipirate.org");
	$mail->addCC("dvi@partipirate.org");
	$mail->addBCC("contact@partipirate.org");

	$subject = "[PartiPirate] Un nouvel adhérent";
	$mailMessage = "Bonjour,\n\n";
	$mailMessage .= "Référence adhésion : " . $transaction["tra_reference"] . "\n";
	$mailMessage .= "Email adhérent : " . $transaction["tra_email"] . "\n";

	if (!$transaction["tra_lastname"]) {
		$subject = "[PartiPirate] Une réadhésion";
	}
	else {
		$mailMessage .= "Information adhérent : \n";
		$mailMessage .= "Nom : " . $transaction["tra_lastname"] . "\n";
		$mailMessage .= "Prénom : " . $transaction["tra_firstname"] . "\n";
		$mailMessage .= "Adresse : " . $transaction["tra_address"] . "\n";
		$mailMessage .= "Code postal : " . $transaction["tra_zipcode"] . "\n";
		$mailMessage .= "Ville : " . $transaction["tra_city"] . "\n";
		$mailMessage .= "Pays : " . $transaction["tra_country"] . "\n";
		if ($transaction["tra_telephone"]) {
			$mailMessage .= "Nom : " . $transaction["tra_telephone"] . "\n";
		}
	}

	if (isset($purpose["local"])) {
		$mailMessage .= "\n";
		$mailMessage .= "Section locale : " . $purpose["local"]["section"] . "\n";
	}

	if (isset($purpose["forumPseudo"])) {
		$mailMessage .= "\n";
		$mailMessage .= "Pseudo : " . $purpose["forumPseudo"] . "\n";
	}

	if (isset($purpose["reportSubscription"])) {
		$mailMessage .= "\n";
		$mailMessage .= "Inscription CR BN et CN : Oui\n";
	}

	if (isset($purpose["comment"]) && $purpose["comment"]) {
		$mailMessage .= "\n";
		$mailMessage .= "Commentaire : ".$purpose["comment"]."\n";
	}

// 	$headers = "From: " . $config["smtp"]["from.name"] . " <".$config["smtp"]["from.address"].">" ."\r\n" .
// 	"Reply-To: " . $config["smtp"]["from.name"] . " <".$config["smtp"]["from.address"].">" ."\r\n" .
// 	"To: secretaires-nationaux@lists.partipirate.org\r\n" .
// 	"X-Mailer: PHP/" . phpversion();

	$mail->Subject = subjectEncode(utf8_encode($subject));
	$mail->msgHTML(str_replace("\n", "<br>\n", utf8_decode($mailMessage)));
	$mail->AltBody = utf8_decode($mailMessage);

// 	if (sendMail($config["smtp"]["from.name"] . " <".$config["smtp"]["from.address"].">",
// 		"secretaires-nationaux@lists.partipirate.org",
// 		$mail->Subject,
// 		$mail->AltBody,
// 		"",
// 		"dvi@partipirate.org",
// 		"")) {
// 	}

// 	if (mail("secretaires-nationaux@lists.partipirate.org", $mail->Subject, $mail->AltBody, $headers)) {
// 		echo "Send SN Mails<br/>";
// 	}


	$mail->SMTPSecure = "ssl";
	if ($mail->send()) {
//		echo "Send SN Mails<br/>";
	}

	// Envoi du mail à l'adhérent
	$subject = "[PartiPirate] Mail de bienvenue";
	$mailMessage = "Ami-e pirate, bonjour

Tu as adhéré(e) au Parti Pirate et nous t'en remercions.
Nous te transmettons ici le livret du nouvel adhérent : <a href=\"https://adhesion.partipirate.org/livretadherent.pdf\">https://adhesion.partipirate.org/livretadherent.pdf</a>
Si tu souhaites t'investir activement, plusieurs possibilités s'offrent à toi car sur le bateau pirate les tâches ne manquent pas :

- Tu peux consulter la liste des missions à pourvoir
- Tu peux prendre contact avec ta section locale pour des actions de terrain

Si tu as des questions, n'hésite pas à nous écrire et nous te répondrons dès que possible.

Encore une fois, bienvenue à bord !

Le Parti Pirate";

// 	$subject = subjectEncode($subject);
// 	$from = $config["smtp"]["from.name"] . " <".$config["smtp"]["from.address"].">";

// 	if (sendMail($from, $transaction["tra_email"],
// 		$subject,
// 		str_replace("\n", "<br>\n", utf8_decode($mailMessage)),
// 		"",
// 		"",
// 		"")) {
// //		echo "Send member mail<br/>";
// 	}

	$mail = getMailInstance();

	$mail->setFrom($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
	$mail->addReplyTo($config["smtp"]["from.address"], $config["smtp"]["from.name"]);

	$mail->Subject = subjectEncode($subject);
	$mail->msgHTML(str_replace("\n", "<br>\n", utf8_decode($mailMessage)));
	$mail->AltBody = utf8_decode($mailMessage);

	$mail->addAddress($transaction["tra_email"]);

	$mail->SMTPSecure = "ssl";
	if ($mail->send()) {
//		echo "Send SN Mails<br/>";
	}

//	echo "Hook SN<br />";
}
?>