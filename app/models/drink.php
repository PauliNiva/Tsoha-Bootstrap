<?php

class Drink {

	private $drink_id, $drink_name, $instructions, $date, $adder_id, $drink_type;

	public function _construct($drink_id, $drink_name, $instructions, $date, $adder_id, $drink_type) {
		$this->drink_id = $drink_id;
		$this->drink_name = $drink_name;
		$this->instructions = $instructions;
		$this->date = $date;
		$this->adder_id = $adder_id;
		$this->drink_type = $drink_type;
	}

	public static function findAll() {
		$query = DB::connection()->prepare('SELECT * FROM Drinks');
		$query->execute();
		$rows = $query->fetchAll(PDO::FETCH_OBJ);
		$drinks = array();

		foreach ($rows as $row) {
			$drinks[] = Drink::fillAttributesFromQuery($row);
		}
		return $drinks;
	}

	public static function findOne($drink_id) {
		$query = DB::connection()->prepare('SELECT * FROM Drinks WHERE drink_id = :drink_id');
		$query->execute(array($drink_id));
		$row = $query->fetch(PDO::FETCH_OBJ);

		if($row) {
			return Drink::fillAttributesFromQuery($row);
		} else {
			return null;
		}
	}

	public function save() {
		$query = DB::connection()->prepare('INSERT INTO
			Drinks(drink_name, instructions, date, adder_id, drink_type) VALUES
			(?, ?, ?, ?, ?) RETURNING drink_id');
		$executionSuccess = $query->execute(array($this->getDrink_name(), $this->getInstructions(),
			'NOW()', $this->getAdder_id(), $this->getDrink_type()));
		if($executionSuccess) {
			$this->drink_id = $query->fetch();
		}
		return $executionSuccess;
	}
 
	public function fillAttributesFromQuery($row) {
		$drink = new Drink();

		$drink->setDrink_id($row->drink_id);
		$drink->setDrink_name($row->drink_name);
		$drink->setInstructions($row->instructions);
		$drink->setDate($row->date);
		$drink->setAdder_id($row->adder_id);
		$drink->setDrink_type($row->drink_type);

		return $drink;
	}

	public static function countDrinks() {
		$query = DB::connection()->prepare('SELECT count(*) FROM Drinks');
		$query->execute();
		$count = $query->fetchColumn();
		return $count;
	}

	public function getDrink_id() {
		return $this->drink_id;
	}

	public function setDrink_id($drink_id) {
		$this->drink_id = $drink_id;
	}

	public function getDrink_name() {
		return $this->drink_name;
	}

	public function setDrink_name($drink_name) {
		$this->drink_name = $drink_name;
	}

	public function getInstructions() {
		return $this->instructions;
	}

	public function setInstructions($instructions) {
		$this->instructions = $instructions;
	}

	public function getDate() {
		return $this->date;
	}

	public function setDate($date) {
		$this->date = $date;
	}

	public function getAdder_id() {
		return $this->adder_id;
	}

	public function setAdder_id($adder_id) {
		$this->adder_id = $adder_id;
	}

	public function getDrink_type() {
		return $this->drink_type;
	}

	public function setDrink_type($drink_type) {
		$this->drink_type = $drink_type;
	}
}
?>