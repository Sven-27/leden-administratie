<?php

class FamilyMember {
  public $ID; // ID of the family member in the database (int)
  public $name; // First name of the family member (string)
  public $family; // Family of the family member (int)
  public $birthDate; // Birth date of the family member (string)
  public $memberType; // Member type of the family member (int)

  public function __construct($ID, $name, $family, $birthDate, $memberType) {
    $this->ID = $ID;
    $this->name = $name;
    $this->family = $family;
    $this->birthDate = $birthDate;
    $this->memberType = $memberType;
  }

  public function age() {
    $birthDate = date('d-m-Y', strtotime($this->birthDate));
    $currentDate = date('d-m-Y');
    $age = diff(date_create($birthDate), date_create($currentDate));
    return $age->format('%y');
  }
}
?>