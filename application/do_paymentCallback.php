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
require_once("engine/bo/TransactionBo.php");

$code = $_REQUEST["code"];
$amount = $_REQUEST["Mt"];
$reference = $_REQUEST["Ref"];
$errorCode = $_REQUEST["Erreur"];

$computedCode = $reference . $amount;
$computedCode = strtoupper(hash('md5', $computedCode, false));

if ($computedCode != $code) {
	exit();
}

$transactionBo = TransactionBo::newInstance(openConnection());

$transaction = $transactionBo->getTransactionByReference($reference, $amount / 100);

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