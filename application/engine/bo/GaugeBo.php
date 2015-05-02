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

	function create(&$gauge) {
		$query = "	INSERT INTO gauges () VALUES ()	";

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		try {
			$statement->execute();
			$gauge["gau_id"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function update($gauge) {
		$query = "	UPDATE gauges SET ";

		$separator = "";
		foreach($gauge as $field => $value) {
			$query .= $separator;
			$query .= $field . " = :". $field;
			$separator = ", ";
		}

		$query .= "	WHERE gau_id = :gau_id ";

//		echo showQuery($query, $gauge);

		$statement = $this->pdo->prepare($query);
		$statement->execute($gauge);
	}

	function save(&$gauge) {
		if (!isset($gauge["gau_id"]) || !$gauge["gau_id"]) {
			$this->create($gauge);

			// create reference
			$gauge["gau_reference"] = "" . $gauge["gau_id"];
			while(strlen($gauge["gau_reference"]) < 8) {
				$gauge["gau_reference"] = "0" . $gauge["gau_reference"];
			}
			$gauge["gau_reference"] = "PP" . $gauge["gau_reference"];
		}

		$this->update($gauge);
	}

	function getTransactionByReference($reference, $amount = null) {
		$args = array("gau_reference" => $reference);

		$query = "	SELECT *
					FROM
						gauges
					WHERE
						gau_reference = :gau_reference ";

		if ($amount) {
			$args["gau_amount"] = $amount;
			$query .= "	AND	gau_amount = :gau_amount ";
		}

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			if (count($results)) {
				$gauge = $results[0];

				foreach($gauge as $field => $value) {
					if (is_numeric($field)) {
						unset($gauge[$field]);
					}
				}

				return $gauge;
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
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