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

$computedCode = $reference . $amount;
$computedCode = strtoupper(hash('md5', $computedCode, false));

if ($computedCode != $code) {
	exit();
}

$transactionBo = TransactionBo::newInstance(openConnection());

$transaction = $transactionBo->getTransactionByReference($reference, $amount / 100);

if ($transaction) {
	$transaction["tra_status"] = "accepted";
	$transactionBo->update($transaction);
}

header("location: done.php");
exit();
?>