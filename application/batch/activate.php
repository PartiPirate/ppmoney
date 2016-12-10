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

require_once("engine/bo/GaletteBo.php");
require_once("engine/libs/password.php");
require_once("engine/discourse/DiscourseAPI.php");


$discourseApi = new richp10\discourseAPI\DiscourseAPI("127.0.0.1:480", $config["discourse"]["api_key"], "http");

function computePassword($password) {
	return password_hash($password, PASSWORD_BCRYPT);
}

$connection = openConnection();

$galetteDatabase = isset($config["galette"]["database"]) ? $config["galette"]["database"] : $config["database"]["database"];

$galetteBo = GaletteBo::newInstance($connection, $galetteDatabase);

$status = $galetteBo->getStatusByLabel("Active member");


$filters = array();
$filters["mdp_adh"] = "";
$filters["activite_adh"] = 1;
$filters["id_statut"] = $status["id_statut"];

$members = $galetteBo->getMembers($filters);

$numberOfMembers = 0;

$chars = array();
for($index = 0; $index < 26; $index++) {
	if ($index < 10) {
		$chars[] = $index;
	}
	$chars[] = chr(65 + $index);
	$chars[] = chr(97 + $index);
}

$nbChars = count($chars);

echo count($members) . " members to handle\n";
$toDo = 2;

foreach($members as $member) {
	echo "Handling " . $member["prenom_adh"] . " " . $member["nom_adh"] . "\n";

//	exit();
	
	$member["login_adh"] = $member["pseudo_adh"] ? $member["pseudo_adh"] : $member["email_adh"];
	
	$password = "";
	for($index = 0; $index < 32; $index++) {
		$password .= $chars[rand(0, $nbChars - 1)];
	}
	$member["mdp_adh"] = $password;
	
//	print_r($member);
//	echo "\n";

	$member["mdp_adh"] = computePassword($password);

//	echo "Encrypted password : " . $member["mdp_adh"];
//	echo "\n";

	$mail = getMailInstance();

	$mail->setFrom($config["smtp"]["from.address"], $config["smtp"]["from.name"]);
	$mail->addReplyTo($config["smtp"]["from.address"], $config["smtp"]["from.name"]);

	$subject = "[PartiPirate] Vos accès";
	$mailMessage = "Bonjour,\n\nVoici vos identifiants pour accéder aux outils du Parti Pirate.\n\n";
	$mailMessage .= "Identifiant : " . $member["login_adh"] . "\n";
	$mailMessage .= "Mot de passe : " . $password . "\n";
	$mailMessage .= "\nNous vous suggérons de changer votre mot de passe en vous connectant à cette addresse : ";
	$mailMessage .= "<a href=\"https://gestion.partipirate.org/\">https://gestion.partipirate.org/</a>, ";
	$mailMessage .= "allez ensuite sur « Modification » puis à la partie « Informations relatives à Galette » pour le changer.";
	
	$mailMessage .= "\n\nSi vous avez des questions, n'hésitez pas à nous écrire et nous vous répondrons dès que possible.";
	$mailMessage .= "\n\nEncore une fois, bienvenue à bord !";
	$mailMessage .= "\n\nLe Parti Pirate";
	
	$mail->Subject = subjectEncode($subject);
	$mail->msgHTML(str_replace("\n", "<br>\n", utf8_decode($mailMessage)));
	$mail->AltBody = utf8_decode($mailMessage);

	$mail->addAddress($member["email_adh"], $member["prenom_adh"] . " " . $member["nom_adh"]);

	// Discourse access
	$result = $discourseApi->getUserByEmail($member["email_adh"]);
	if (!$result) {
		echo "Creation of discourse account needed !\n";
		$result = $discourseApi->createUser($member["nom_adh"] . " " . $member["prenom_adh"], GaletteBo::showIdentity($member), $member["email_adh"], $password, true);
	}
	else {
		echo "Creation of discourse already done !\n";
	}

	$mail->SMTPDebug = 2;
//	$mail->SMTPSecure = "ssl";

	echo "Will be sent\n";

	
//	if ($member["email_adh"] == "contact@levieuxcedric.com" && $mail->send()) {
	if ($mail->send()) {
//	if (sendMail($config["smtp"]["from.address"], $member["email_adh"], $mail->Subject, $mail->AltBody)) {
		$galetteBo->updateMember($member);
		echo "Mail sent\n";
	}

	$toDo--;

	if (!$toDo) {
		echo "Batch end\n";

		exit();
	}
	
	$numberOfMembers++;
}

echo "Number of handled members : $numberOfMembers \n";

echo "Batch end\n";

?>
