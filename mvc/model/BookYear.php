<?php

class BookYear {
  public $ID; // ID of the book year in the database (int)
  public $year; // Year of the book year (int)
  public $price; // Price of the book year (float)

  public function __construct($ID, $year, $price) {
    $this->ID = $ID;
    $this->year = $year;
    $this->price = $price;
  }

  public function agePrice($age, $percentage = 100) {
    if($age < 8) {
      $price = ($this->price / 100) * 50;
    } elseif($age >= 8 && $age <= 12) {
      $price = ($this->price / 100) * 60;
    } elseif ($age >= 13 && $age <= 17) {
      $price = ($this->price / 100) * 75;
    } elseif ($age >= 18 && $age <= 50) {
      $price = ($this->price / 100) * 100;
    } elseif ($age >= 51) {
      $price = ($this->price / 100) * 55;
    } else {
      $price = $this->price;
    }
    return ($price / 100) * $percentage;
  }
}