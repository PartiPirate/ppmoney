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

// on définit trois variables externes : $email, $amount en centime et $reference

// On récupère la date au format ISO-8601

// On renseigne les différents paramètres de la requête de paiement
// Le montant en centime
$PBX_TOTAL = $amount;
// Devise en euro
$PBX_DEVISE = 978;
// Référence du paiement
$PBX_CMD = $reference;
// L'email du donateur
$PBX_PORTEUR = $email;
// Le format des données retour
$PBX_RETOUR = "Mt:M;Ref:R;Auto:A;Erreur:E";
// L'algorithme de hashage
$PBX_HASH = "SHA512";
// Date du paiement
$PBX_TIME = $dateTime;

$code = $reference . $amount;
$code = strtoupper(hash('md5', $code, false));

$acceptedUrl = $config["server"]["base"] . "do_paymentAccepted.php?code=$code";
$refusedUrl = $config["server"]["base"] . "do_paymentRefused.php";
$canceledUrl = $config["server"]["base"] . "do_paymentCanceled.php";
$ipnUrl = $config["server"]["base"] . "do_paymentCallback.php?code=$code";

// On crée la chaîne à hacher sans URLencodage ET ON LIMITE AUX CARTES BANCAIRES
$msg =
"PBX_SITE=" . $config["paybox"]["PBX_SITE"] .
"&PBX_RANG=" . $config["paybox"]["PBX_RANG"] .
"&PBX_IDENTIFIANT=" . $config["paybox"]["PBX_IDENTIFIANT"] .
"&PBX_TOTAL=$PBX_TOTAL".
"&PBX_DEVISE=$PBX_DEVISE".
"&PBX_CMD=$PBX_CMD".
"&PBX_PORTEUR=$PBX_PORTEUR".
"&PBX_RETOUR=$PBX_RETOUR".
"&PBX_HASH=$PBX_HASH".
"&PBX_TIME=$PBX_TIME".
"&PBX_TYPEPAIEMENT=CARTE".
"&PBX_TYPECARTE=CB".
"&PBX_EFFECTUE=".urlencode($acceptedUrl)."".
"&PBX_REFUSE=".urlencode($refusedUrl)."".
"&PBX_ANNULE=".urlencode($canceledUrl)."".
"&PBX_REPONDRE_A=".urlencode($ipnUrl)."".
"&PBX_3DS=N".
"";

// Clé de hashahge en ASCII, On la transforme en binaire
$binKey = pack("H*", $config["paybox"]["secretKey"]);
// On calcule l’empreinte (à renseigner dans le paramètre PBX_HMAC) grâce à la fonction hash_hmac et la clé binaire
// On envoie via la variable PBX_HASH l'algorithme de hachage qui a été utilisé (SHA512 dans ce cas)
$hmac = strtoupper(hash_hmac('sha512', $msg, $binKey));
// La chaîne sera envoyée en majuscules, d'où l'utilisation de strtoupper()
// On crée le formulaire à envoyer à Paybox System
// ATTENTION : l'ordre des champs est extrêmement important, il doit
// correspondre exactement à l'ordre des champs dans la chaîne hachée
?>

<form id="payboxForm" method="POST" action="https://<?php echo $config["paybox"]["server"]; ?>/cgi/MYchoix_pagepaiement.cgi">
<input type="hidden" name="PBX_SITE"        value="<?php echo $config["paybox"]["PBX_SITE"]; ?>" />
<input type="hidden" name="PBX_RANG"        value="<?php echo $config["paybox"]["PBX_RANG"]; ?>" />
<input type="hidden" name="PBX_IDENTIFIANT" value="<?php echo $config["paybox"]["PBX_IDENTIFIANT"]; ?>" />
<input type="hidden" name="PBX_TOTAL"       value="<?php echo $PBX_TOTAL; ?>" />
<input type="hidden" name="PBX_DEVISE"      value="<?php echo $PBX_DEVISE; ?>" />
<input type="hidden" name="PBX_CMD"         value="<?php echo $PBX_CMD; ?>" />
<input type="hidden" name="PBX_PORTEUR"     value="<?php echo $PBX_PORTEUR; ?>" />
<input type="hidden" name="PBX_RETOUR"      value="<?php echo $PBX_RETOUR; ?>" />
<input type="hidden" name="PBX_HASH"        value="<?php echo $PBX_HASH; ?>" />
<input type="hidden" name="PBX_TIME"        value="<?php echo $PBX_TIME; ?>" />
<input type="hidden" name="PBX_HMAC"        value="<?php echo $hmac; ?>" />
<input type="hidden" name="PBX_TYPEPAIEMENT"     value="CARTE" />
<input type="hidden" name="PBX_TYPECARTE"   value="CB" />
<input type="hidden" name="PBX_EFFECTUE"	value="<?php echo urlencode($acceptedUrl); ?>" />
<input type="hidden" name="PBX_REFUSE"		value="<?php echo urlencode($refusedUrl); ?>" />
<input type="hidden" name="PBX_ANNULE"		value="<?php echo urlencode($canceledUrl); ?>" />
<input type="hidden" name="PBX_REPONDRE_A"	value="<?php echo urlencode($ipnUrl); ?>" />
<input type="hidden" name="PBX_3DS"	    value="N" />
</form>
