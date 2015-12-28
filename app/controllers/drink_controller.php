<?php

require 'app/models/drink.php';
require 'app/models/drink_ingredient.php';
require 'app/models/drink_type.php';
require 'app/models/ingredient.php';
require 'lib/utilities.php';

class DrinkController extends BaseController {

	public static function index() {
		$count = new Utilities();
		$count->countDrinks();
		$drinks = Drink::findAll();
		View::make('drink/index.html', array('drinks' => $drinks, 'count' => $count));
	}

	public static function show($id) {
		$drink = Drink::findOne($id);
		$drink_ingredients = DrinkIngredient::listDrinkIngredients($id);
		View::make('drink/specific_drink.html', array('drink' => $drink, 'drink_ingredients' => $drink_ingredients));
	}

	public static function addNew() {
		$drink_types = DrinkType::listDrinkTypes();
		View::make('drink/new.html', array('drink_types' => $drink_types));
	}

	public static function destroy($id) {
		$drink = Drink::findOne($id);
		$drink->destroy();
		Redirect::to('/drink', array('message' => 'Drink has been deleted.'));
	}

	public static function edit($id) {
		$drink = Drink::findOne($id);
		$drink_types = DrinkType::listDrinkTypes();
		$drink_ingredients = DrinkIngredient::listDrinkIngredients($id);
		View::make('drink/edit.html', array('attributes' => $drink, 'drink_types' => $drink_types, 'drink_ingredients' => $drink_ingredients));
	}

	public static function update($id) {
		$modifiedDrink = Drink::findOne($id);
		$name = $_POST['drink_name'];
		$ingredients = $_POST['ingredients'];
		$amounts = $_POST['amounts'];
    	$units = $_POST['units'];

    	$modifiedDrink->setDrink_name($name);
    	$modifiedDrink->setDrink_type($_POST['drink_type']);
    	$modifiedDrink->setInstructions($_POST['instructions']);
    	$modifiedDrink->update();

    	Redirect::to('/drink/' . $modifiedDrink->getDrink_id(), array('message' => 'Drink has been modified.'));
  	}

	public static function store() {
		$newDrink = new Drink();
		$name = $_POST['drink_name'];
		$ingredients = $_POST['ingredients'];
		$amounts = $_POST['amounts'];
    	$units = $_POST['units'];

    	$newDrink->setDrink_name($name);
    	$newDrink->setDrink_type($_POST['drink_type']);
    	$newDrink->setInstructions($_POST['instructions']);
    	$newDrink->save();

    	$i = 0;
    	foreach ($ingredients as $ingredient) {
    		$ingredient = strtolower($ingredient);
            if (Ingredient::alreadyInArchive($ingredient) > 0) {
                $ingredient_id = Ingredient::alreadyInArchive($ingredient);
            } else {
                $newIngredient = new Ingredient();
                $newIngredient->setIngredient_name($ingredient);
                $ingredient_id = $newIngredient->save();
            }

    		$newDrinkIngredient = new DrinkIngredient();
        	$newDrinkIngredient->setDrink_id($newDrink->getDrink_id());
        	$newDrinkIngredient->setIngredient_id($ingredient_id);
        	$newDrinkIngredient->setAmount($amounts[$i]);
        	$newDrinkIngredient->setUnit($units[$i]);
        	$newDrinkIngredient->save();
        	$i++;
    	}

    	Redirect::to('/drink/' . $newDrink->getDrink_id(), array('message' => 'Drink has been archived.'));
  	}
}