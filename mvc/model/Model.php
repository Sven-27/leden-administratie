<?php
// including the models for the database
include_once './mvc/model/BookYear.php';
include_once './mvc/model/MemberType.php';
include_once './mvc/model/Family.php';
include_once './mvc/model/Contribution.php';
include_once './mvc/model/Member.php';

class Model {
  // All of the models for the crud functions 

  // Retrieves the bookyear list
  public function getBookYearList() {
    // include the database connection
    include 'inc/process/connect.php';

    // set an empty array for the bookyear list
    $bookyears = array();

    try{
      // fetch all data from the boekjaar table
      $stmt = $conn->prepare("SELECT * FROM boekjaar");
      // execute the query
      $stmt->execute();

      // check if there are any results
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // add the bookyear to the bookyear list
        $bookyears[] = new BookYear($row['ID'], $row['Jaar'], $row['Bedrag']);
      }
    } catch(PDOException $e) {
      // if an error occurs, return the error
      echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
      return null;
    }
    // return the bookyear list
    return $bookyears;
  }
}