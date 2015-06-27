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
// $filename = "test_" . mktime() . ".log";
// $handle = fopen($filename, "w");
// fwrite($handle, "Call initiated\n");

include_once("config/database.php");
include_once("config/paybox.php");
require_once("engine/bo/TransactionBo.php");

$code = $_REQUEST["code"];
$amount = $_REQUEST["Mt"];
$reference = $_REQUEST["Ref"];
$errorCode = $_REQUEST["Erreur"];
$signature = $_REQUEST["Sign"];

// fwrite($handle, "Signature : $signature\n");

$signature = base64_decode($signature);
$query = $_SERVER["QUERY_STRING"];
$signedData = substr($query, strpos($query, "&Mt") + 1, strpos($query, "&Sign") - strpos($query, "&Mt") - 1);

// fwrite($handle, "Query : $query\n");
// fwrite($handle, "Signed data : $signedData\n");

// fwrite($handle, "PEM : ".$config["paybox"]["pem"]."\n");

$isSigned = openssl_verify($signedData, $signature, $config["paybox"]["pem"]);

// fwrite($handle, "Is signed : $isSigned\n");

if (!$isSigned) {
	exit();
}

$computedCode = $reference . $amount;
$computedCode = strtoupper(hash('md5', $computedCode, false));

if ($computedCode != $code) {
	exit();
}

// fwrite($handle, "Computed code : $computedCode\n");

$payboxIp = $_SERVER["REMOTE_ADDR"];
if (!$payboxIp && $payboxIp == "127.0.0.1") {
	$payboxIp = $_SERVER["HTTP_X_REAL_IP"];
}
$allowed = false;

// fwrite($handle, "Paybox ip : $payboxIp\n");

// Open the connection, may be used in the hooks
$connection = openConnection();
$transactionBo = TransactionBo::newInstance($connection);

$transaction = $transactionBo->getTransactionByReference($reference, $amount / 100);

// fwrite($handle, "Authorized IP : ".json_encode($config["paybox"]["allowed_ips"])."\n");

foreach($config["paybox"]["allowed_ips"] as $allowedIp) {
	if ($allowedIp == $payboxIp) {
		$allowed = true;
		break;
	}
}

if (!$allowed) {
	fwrite($handle, "Not Allowed\n");
	fclose($handle);
	exit();
}

// fwrite($handle, "Allowed\n");

fclose($handle);

if ($transaction) {

	if ($errorCode == "99999") {
		// Still waiting
	}
	else if ($errorCode != "00000") {
		$transaction["tra_status"] = "refused";
		$transaction["tra_confirmed"] = "1";
		$transactionBo->update($transaction);
	}
	else {
		$transaction["tra_status"] = "accepted";
		$transaction["tra_confirmed"] = "1";
		$transactionBo->update($transaction);

		// Call the hooks
		$directoryHandler = dir("hooks");
		$hookEnabled = true;
		while(($fileEntry = $directoryHandler->read()) !== false) {
			if($fileEntry != '.' && $fileEntry != '..' && strpos($fileEntry, ".php")) {
				error_log("Call $fileEntry hook");
				include_once("hooks/". $fileEntry);
			}
		}
		$directoryHandler->close();
	}
}

?>