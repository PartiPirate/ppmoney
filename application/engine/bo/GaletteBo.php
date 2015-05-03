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

class GaletteBo {
	var $pdo = null;

	function __construct($pdo) {
		$this->pdo = $pdo;
	}

	static function newInstance($pdo) {
		return new GaletteBo($pdo);
	}

	function getMemberByMail($email) {
		$query = "	SELECT *
					FROM galette_adherents
					WHERE 1 = 1
					AND email_adh = :email_adh";
		$args = array("email_adh" => $email);

		$statement = $this->pdo->prepare($query);

//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			if (count($results)) {
				$member = $results[0];

				foreach($member as $field => $value) {
					if (is_numeric($field)) {
						unset($member[$field]);
					}
				}

				return $member;
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}

	function getSectionByName($sectionName) {
		return $this->getGroupByName($sectionName);
	}

	function getGroupByName($groupName) {
		$query = "	SELECT *
					FROM galette_groups
					WHERE 1 = 1
					AND group_name = :group_name";
		$args = array("group_name" => $groupName);

		$statement = $this->pdo->prepare($query);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

			if (count($results)) {
				$group = $results[0];

				foreach($group as $field => $value) {
					if (is_numeric($field)) {
						unset($group[$field]);
					}
				}

				return $group;
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}

	function getStatusByLabel($statusLabel) {
		$query = "	SELECT *
					FROM galette_statuts
					WHERE 1 = 1
					AND libelle_statut = :libelle_statut";
		$args = array("libelle_statut" => $statusLabel);

		$statement = $this->pdo->prepare($query);

//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

//			print_r($results);

			if (count($results)) {
				$status = $results[0];

				foreach($status as $field => $value) {
					if (is_numeric($field)) {
						unset($status[$field]);
					}
				}

				return $status;
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}

	function createMember(&$member) {
		$query = "	INSERT INTO galette_adherents (id_statut) VALUES (:id_statut)	";
		$args = array("id_statut" => $member["id_statut"]);

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$member["id_adh"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function updateMember(&$member) {
		$query = "	UPDATE galette_adherents SET ";

		$separator = "";
		foreach($member as $field => $value) {
			$query .= $separator;
			$query .= $field . " = :". $field;
			$separator = ", ";
		}

		$query .= "	WHERE id_adh = :id_adh ";

//		echo showQuery($query, $member);

		$statement = $this->pdo->prepare($query);
		$statement->execute($member);
	}

	function saveMember(&$member) {
		if (!isset($member["id_adh"]) || !$member["id_adh"]) {
			$this->createMember($member);
		}

		$this->updateMember($member);
	}

	function insertMemberInGroup($memberInGroup) {
		$query = "	INSERT INTO galette_groups_members (id_group, id_adh) VALUES (:id_group, :id_adh)	";

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $memberInGroup);

		try {
			$statement->execute($memberInGroup);

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function insertTransaction(&$transaction) {
		$query = "	INSERT INTO galette_transactions
		(trans_date, trans_amount, trans_desc, id_adh)
		VALUES
		(:trans_date, :trans_amount, :trans_desc, :id_adh)	";

		$statement = $this->pdo->prepare($query);
		//		echo showQuery($query, $args);

		try {
			$statement->execute($transaction);
			$transaction["trans_id"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function insertCotisation(&$cotisation) {
		$query = "	INSERT INTO galette_cotisations
						(id_adh, id_type_cotis, montant_cotis, type_paiement_cotis,
						info_cotis, date_enreg, date_debut_cotis,
						date_fin_cotis, trans_id)
					VALUES
						(:id_adh, :id_type_cotis, :montant_cotis, :type_paiement_cotis,
						:info_cotis, :date_enreg, :date_debut_cotis,
						:date_fin_cotis, :trans_id)	";

		$statement = $this->pdo->prepare($query);
//		echo showQuery($query, $cotisation);

		try {
			$statement->execute($cotisation);
			$cotisation["id_cotis"] = $this->pdo->lastInsertId();

			return true;
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return false;
	}

	function getTypeCotisationByLabel($label) {
		$query = "	SELECT *
					FROM galette_types_cotisation
					WHERE 1 = 1
					AND libelle_type_cotis = :libelle_type_cotis";
		$args = array("libelle_type_cotis" => $label);

		$statement = $this->pdo->prepare($query);

//		echo showQuery($query, $args);

		try {
			$statement->execute($args);
			$results = $statement->fetchAll();

//			print_r($results);

			if (count($results)) {
				$typeCotisation = $results[0];

				foreach($typeCotisation as $field => $value) {
					if (is_numeric($field)) {
						unset($typeCotisation[$field]);
					}
				}

				return $typeCotisation;
			}
		}
		catch(Exception $e){
			echo 'Erreur de requète : ', $e->getMessage();
		}

		return null;
	}
}