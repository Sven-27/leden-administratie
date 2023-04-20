<?php

class MemberType {
  public $ID; // ID of the member type in the database (int)
  public $name; // Name of the member type (string)
  public $percentage; // Percentage of the member type (int)
  public $description; // Description of the member type (string)

  public function __construct($ID, $name, $percentage, $description) {
    $this->ID = $ID;
    $this->name = $name;
    $this->percentage = $percentage;
    $this->description = $description;
  }
}