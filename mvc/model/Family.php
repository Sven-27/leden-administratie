<?php

class Family {
  public $ID; // Family ID in the database (int)
  public $name; // Family name (string)
  public $address; // Family address (string)

  public function __construct($ID, $name, $address) {
    $this->ID = $ID;
    $this->name = $name;
    $this->address = $address;
  }
}
?>