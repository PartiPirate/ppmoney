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

class GaugeBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new GaugeBo($pdo);
	}

	static function normalize(&$gauge) {
		$gauge["gau_number_of_transactions"] = count($gauge["transactions"]);
		$gauge["gau_amount"] = 0;

		$megaparts = explode(",", $gauge["gau_amount_path"]);
		foreach($megaparts as $megapart) {
			$parts = explode(">", $megapart);

			foreach($gauge["transactions"] as $transaction) {
				$purpose = json_decode($transaction["tra_purpose"], true);
				$amount = $purpose;
				foreach($parts as $part) {
					if (isset($amount[trim($part)])) {
						$amount = $amount[trim($part)];
					}
				}
				if (!$amount || !is_numeric($amount)) {
					$amount = $transaction["tra_amount"];
				}

				$gauge["gau_amount"] += $amount;
			}
		}

		if (isset($gauge["gau_amount_goal"]) && $gauge["gau_amount_goal"]) {
			$gauge["gau_percent_goal"] = $gauge["gau_amount"] * 100 / $gauge["gau_amount_goal"];
		}

		if (count($gauge["transactions"])) {
			$gauge["gau_average_amount"] = $gauge["gau_amount"] / count($gauge["transactions"]);
		}
	}
}