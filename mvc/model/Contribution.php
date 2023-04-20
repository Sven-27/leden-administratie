<?php

class Contribution {
  public $ID; // ID of the contribution in the database (int)
  public $member; // Member of the contribution (int)
  public $payed; // Contribution memberType (int)
  public $bookYear; // Amount of contribution (float)

  public function __construct($ID, $member, $payed, $bookYear) {
    $this->ID = $ID;
    $this->member = $member;
    $this->payed = $payed;
    $this->bookYear = $bookYear;
  }
}
?>