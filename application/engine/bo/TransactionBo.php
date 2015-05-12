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

class TransactionBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new TransactionBo($pdo);
	}

	function create(&$transaction) {
		$query = "	INSERT INTO transactions () VALUES ()	";

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		try {
			$statement->execute();
			$transaction["tra_id"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function update($transaction) {
		$query = "	UPDATE transactions SET ";

		$separator = "";
		foreach($transaction as $field => $value) {
			$query .= $separator;
			$query .= $field . " = :". $field;
			$separator = ", ";
		}

		$query .= "	WHERE tra_id = :tra_id ";

//		echo showQuery($query, $transaction);

		$statement = $this->pdo->prepare($query);
		$statement->execute($transaction);
	}

	function save(&$transaction) {
		if (!isset($transaction["tra_id"]) || !$transaction["tra_id"]) {
			$this->create($transaction);

			// create reference
			$transaction["tra_reference"] = "" . $transaction["tra_id"];
			while(strlen($transaction["tra_reference"]) < 8) {
				$transaction["tra_reference"] = "0" . $transaction["tra_reference"];
			}
			$transaction["tra_reference"] = "PP" . $transaction["tra_reference"];
		}

		$this->update($transaction);
	}

	function getTransactionByReference($reference, $amount = null) {
		$args = array("tra_reference" => $reference);

		$query = "	SELECT *
					FROM
						transactions
					WHERE
						tra_reference = :tra_reference ";

		if ($amount) {
			$args["tra_amount"] = $amount;
			$query .= "	AND	tra_amount = :tra_amount ";
		}

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			if (count($results)) {
				$transaction = $results[0];

				foreach($transaction as $field => $value) {
					if (is_numeric($field)) {
						unset($transaction[$field]);
					}
				}

				return $transaction;
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}

	function getTransactions($filters) {
		$args = array();

		$query = "	SELECT *
					FROM
						transactions
					WHERE
						1 = 1 \n";

		if (isset($filters["tra_status"])) {
			$args["tra_status"] = $filters["tra_status"];
			$query .= " AND tra_status = :tra_status \n";
		}

		if (isset($filters["tra_confirmed"])) {
			$args["tra_confirmed"] = $filters["tra_confirmed"];
			$query .= " AND tra_confirmed = :tra_confirmed \n";
		}

		if (isset($filters["tra_from_date"])) {
			$args["tra_from_date"] = $filters["tra_from_date"];
			$query .= " AND tra_date > :tra_from_date \n";
		}

		if (isset($filters["tra_to_date"])) {
			$args["tra_to_date"] = $filters["tra_to_date"];
			$query .= " AND DATE_FORMAT(tra_date, '%Y-%m-%d') <= :tra_to_date \n";
		}

		if (isset($filters["tra_like_purpose"])) {
			$args["tra_like_purpose"] = $filters["tra_like_purpose"];
			$query .= "AND tra_purpose LIKE :tra_like_purpose \n";
		}

		$query .= "	ORDER BY tra_date ASC ";

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			return $results;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function getStats($filters) {
		$args = array();

		$query = "	SELECT
						DATE_FORMAT(tra_date, '%Y-%m-%d') AS tra_transaction_date,
						COUNT(*) AS tra_number,
						SUM(IF(LOCATE('join', tra_purpose) > 0, 1, 0)) AS tra_number_adhesions,
						SUM(tra_amount) AS tra_amounts,
						AVG(tra_amount) AS tra_avg_amount,
						MIN(tra_amount) AS tra_min_amount,
						MAX(tra_amount) AS tra_max_amount
					FROM  `transactions`
					WHERE  `tra_status` =  'accepted'
					AND tra_confirmed =1 ";

		if (isset($filters["tra_from_date"])) {
			$query .= " AND DATE_FORMAT(tra_date, '%Y-%m-%d') >=  :tra_from_date ";
			$args["tra_from_date"] = $filters["tra_from_date"];
		}

		if (isset($filters["tra_to_date"])) {
			$query .= " AND DATE_FORMAT(tra_date, '%Y-%m-%d') <=  :tra_to_date ";
			$args["tra_to_date"] = $filters["tra_to_date"];
		}

		$query .= "	GROUP BY tra_transaction_date
					WITH ROLLUP		";

		$statement = $this->pdo->prepare($query);

//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			return $results;
		}
		catch(Exception $e){
			$gauge["transactions"] = array();
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return array();
	}

	function getGaugeTransactions(&$gauge) {
		$args = array();
		$args["tra_like_purpose"] = "%" .$gauge["gau_searched_purpose"]. "%";

		$query = "	SELECT *
					FROM
						transactions
					WHERE
						tra_status = 'accepted'
					AND	tra_confirmed = 1
					AND tra_purpose LIKE :tra_like_purpose ";

		if (isset($gauge["gau_from_date"])) {
			$query .= " AND tra_date > :tra_from_date ";
			$args["tra_from_date"] = $gauge["gau_from_date"];
		}

		if (isset($gauge["gau_to_date"])) {
			$query .= " AND tra_date < :tra_to_date ";
			$args["tra_to_date"] = $gauge["gau_to_date"];
		}

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			$gauge["transactions"] = $results;
		}
		catch(Exception $e){
			$gauge["transactions"] = array();
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return $gauge["transactions"];
	}
}