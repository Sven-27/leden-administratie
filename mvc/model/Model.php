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

  // Retrieves one bookyear from database
  public function getBookYear($number, $year = false) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      if (!$year) { 
        // We create the select query to which we pass a WHERE parameter in the boekjaar table
        $stmt = $conn->prepare("SELECT * FROM boekjaar WHERE ID = :id");

        // Link the bookyear number to the paramater ID.
        $stmt->execute([
          'id' => $number
        ]);
      } else {
        // We create the select query to which we pass a WHERE parameter in the boekjaar table
        $stmt = $conn->prepare("SELECT * FROM boekjaar WHERE ID = :jaar");

        // Link the bookyear number to the paramater Year.
        $stmt->execute([
          'jaar' => $year
        ]);
      }

    // Go through the result of the query that was executed
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Return the first best result in the bookyear class
        return new BookYear($row['ID'], $row['Jaar'], $row['Bedrag']);
    }
    } catch(PDOException $e) {
      // If an error occurred echo this and return null
      echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
      return null;
    }
  }

  // create a new bookyear in database
  public function addBookYear($year, $price) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // query to insert a new bookyear
      $statement = $conn->prepare('INSERT INTO boekjaar (Jaar, Bedrag)
      VALUES (:jaar, :bedrag)');

      // Add the year and amount to the 2 parameters requested in the query and then run the query
      $statement->execute([
        'jaar' => $year,
        'bedrag' => floatval($price),
      ]);

      // Retrieves all family members 
      $familyMembers = $this->getFamilyMembersList();
    
      // Go loop through all the members
      foreach($familyMembers as $familyMember) {
        // Run the addContribution function and assign the member's ID, 0 for paid dues and the last added ID as this is the fiscal year ID
        $this->addContribution($familyMember->ID, 0, $conn->lastInsertId());
      }

        // If all went well throw the successful message
        echo '<div class="message good"><p><strong>Succesvol </strong> het boekjaar toegevoegd</p></div>';
      } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
      }    
  }

  // edit a bookyear in database
  public function editBookYear($bookyear) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // We create an update query with the bookyear table and give all columns except the ID a SET with parameters and do this only for the rows that have the same ID as the ID parameter
      $statement = $conn->prepare('UPDATE boekjaar SET Jaar = :year, Bedrag = :price WHERE ID = :id');

      // Add the values to the parameters requested to be populated and then run the query
      $statement->execute([
        'id' => $bookyear->ID,
        'year' => $bookyear->year,
        'price' => $bookyear->price,
      ]);

        // If all went well throw the successful message
        echo '<div class="message good"><p><strong>Succesvol </strong> het boekjaar aangepast</p></div>';
      } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
      }
  }

  // delete a bookyear in database
  public function deleteBookYear($number) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // First run the deleteContribution function and pass as ID 0 and as bookyear parameter the ID we want to delete
      // We have to do this because first all contributions must be removed that are linked to this bookyear
      $this->deleteContribution(0, $number);

      // Create a delete query with the boekjaar table and give this a where paramater with the ID
      $statement = $conn->prepare('DELETE FROM boekjaar WHERE ID = :id');

      // Link the ID to the parameter and then run the query
      $statement->execute([
        'id' => $number,
      ]);

      // If all went well throw the successful message
      echo '<div class="message good"><p><strong>Succesvol </strong> het boekjaar verwijderd</p></div>';
    } catch(PDOException $e) {
      // If an error occurred echo this and return null
      echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
    }
  }

  // get all families from database
  public function getFamiliesList() {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    // The now empty array with families
    $families = array();

    try {
        // Create the select query with the database table familie
        $stmt = $conn->prepare("SELECT * FROM familie");
        // Run the query
        $stmt->execute();

        // set the resulting array to associative and loop through the results
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          // Add the new family objects to the previously created array
          $families[] = new Family($row['ID'], $row['Naam'], $row['Adres']);
        }
    } catch(PDOException $e) {
      // If an error occurred echo this and return null
      echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
      return null;            
    }

      // Return families if no errors occurred
      return $families;
  } 

  // The getFamily retrieves one family from the database using supplied ID and returns it to the user
  public function getFamily($number) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // We create the select query to which we pass a WHERE parameter in the familie table
      $stmt = $conn->prepare("SELECT * FROM familie WHERE ID = :id");
      
      // Link the family number to the paramater ID.
      $stmt->execute([
        'id' => $number
      ]);

        // Go through the result of the query that was executed
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          // Return the first best result in the family class
          return new Family($row['ID'], $row['Naam'], $row['Adres']);
        }
      } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
        return null;
      }
  }
    
  // insert a family in database
  public function addFamily($name, $address) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // Make an insert query to the database in the familie table and provide the name and address
      $statement = $conn->prepare('INSERT INTO familie (Naam, Adres)
      VALUES (:name, :address)');

      // Add the name and address to the 2 parameters requested in the query and then run the query
      $statement->execute([
        'name' => $name,
        'address' => $address,
      ]);

      // If all went well throw the successful message
      echo '<div class="message good"><p><strong>Succesvol </strong> de familie toegevoegd</p></div>';
    } catch(PDOException $e) {
      // If an error occurred echo this and return null
      echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
      return;
    }
  }

  // edit a family in database
  public function editFamily($family) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // We create an update query with the family table and give all columns except the ID a SET with parameters and do this only for the rows that have the same ID as the ID parameter
      $statement = $conn->prepare('UPDATE familie SET Naam = :name, Adres = :address WHERE ID = :id');

      // Add the values to the parameters requested to be populated and then run the query
      $statement->execute([
        'id' => $family->ID,
        'name' => $family->name,
        'address' => $family->address,
      ]);

      // If all went well throw the successful message
      echo '<div class="message good"><p><strong>Succesvol </strong> de familie aangepast</p></div>';
    } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
    }
  }

  // delete a family in database
  public function deleteFamily($number) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // First run the deleteFamilyMember function and pass as ID 0 and as family parameter the ID we want to delete
      // We have to do this because first all familie members must be removed that are linked to this family
      $this->deleteFamilyMember(0, $number);

      // Create a delete query with the family table and give this a where paramater with the ID
      $statement = $conn->prepare('DELETE FROM familie WHERE ID = :id');

        // Link the ID to the parameter and then run the query
        $statement->execute([
          'id' => $number,
        ]);

        // If all went well throw the successful message
        echo '<div class="message good"><p><strong>Succesvol </strong> de familie verwijderd</p></div>';
    } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
    }
  }

  // select all family members from database
  public function getFamilyMembersList() {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    // The now empty array with family members
    $familyMembers = array();

    try {
      // Create the select query with the database table familielid
      $stmt = $conn->prepare("SELECT * FROM familielid");
      // Run the query
      $stmt->execute();

      // set the resulting array to associative and loop through the results
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Add the new familymember objects to the previously created array
        $familyMembers[] = new FamilyMember($row['ID'], $row['Naam'], $row['Familie'], $row['Geboortedatum'], $row['SoortLid']);
      }
    } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
        return null;      
    }

    // Return familie members if no errors occurred
    return $familyMembers;
  } 

  // select a family member from database by ID
  public function getFamilyMember($number) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // We create the select query to which we pass a WHERE parameter in the familie member table
      $stmt = $conn->prepare("SELECT * FROM familielid WHERE ID = :id");
      
      // Link the family member number to the paramater ID.
      $stmt->execute([
          'id' => $number
      ]);

      // Go through the result of the query that was executed
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Return the first best result in the familymember class
        return new FamilyMember($row['ID'], $row['Naam'], $row['Familie'], $row['Geboortedatum'], $row['SoortLid']);
      }
    } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
        return null;
    }
  }

  // select a family member from database by id and family
  public function getFamilyMembers($family = 0, $memberType = 0) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    // We create an empty array that we can return later
    $familyMembers = [];

    try {
      // We create a SELECT query in the family member table with the where parameters family or membertype
      $stmt = $conn->prepare("SELECT * FROM familielid WHERE Familie = :family OR SoortLid = :membertype");
      
      // Add the values to the parameters of the query and then execute the query
      $stmt->execute([
        'family' => $family,
        'membertype' => $memberType
      ]);

      // Go through the query execution results
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Add the family members to the created array
        $familyMembers[] = new FamilyMember($row['ID'], $row['Naam'], $row['Familie'], $row['Geboortedatum'], $row['SoortLid']);
      }
    } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
        return null;            
    }

    // Return the array of family members if no errors occurred
    return $familyMembers;
  }
    
  // insert a family member in database
  public function addFamilyMember($name, $family, $birthdate, $memberType) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // Make an insert query to the database in the familie member table and provide the name, family ID, birthdate and memberType ID
      $statement = $conn->prepare('INSERT INTO familielid (Naam, Familie, Geboortedatum, SoortLid)
      VALUES (:name, :family, :birthdate, :memberType)');

      // Add the name, family, birthdate and membertype to the 4 parameters requested in the query and then run the query
      $statement->execute([
        'name' => $name,
        'family' => intval($family),
        'birthdate' => $birthdate,
        'memberType' => intval($memberType)
      ]);

      // If all went well throw the successful message
      echo '<div class="message good"><p><strong>Succesvol </strong> de familie lid toegevoegd</p></div>';
    } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
        return;
    }
    
    $bookyear = $this->model->getBookYear(false, date("Y"));
            
    $this->model->addContribution($conn->lastInsertId(), floatval(0), $bookyear->ID);
  }

  // edit a family member in database
  public function editFamilyMember($familyMember) {
      // Add the file that connects to the database
      include 'inc/process/connect.php';

      try {
        // We create an update query with the family member table and give all columns except the ID a SET with parameters and do this only for the rows that have the same ID as the ID parameter
        $statement = $conn->prepare('UPDATE familielid SET Naam = :name, Familie = :family, Geboortedatum = :birthdate, SoortLid = :memberType WHERE ID = :id');

        // Add the values to the parameters requested to be populated and then run the query
        $statement->execute([
          'id' => $familyMember->ID,
          'name' => $familyMember->name,
          'family' => $familyMember->family,
          'birthdate' => $familyMember->birthdate,
          'memberType' => $familyMember->memberType
        ]);

        // If all went well throw the successful message
        echo '<div class="message good"><p><strong>Succesvol </strong> de familie lid aangepast</p></div>';
      } catch(PDOException $e) {
          // If an error occurred echo this and return null
          echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
      }
  }

  // delete a family member from database by id and family
  public function deleteFamilyMember($number = 0, $family = 0) {
      // Add the file that connects to the database
      include 'inc/process/connect.php';

      try {
        // See if the family parameter has been passed along
        if (isset($family) && $family != 0) {
          // Run the getFamilyMembers function and provide the family ID and then loop through that result
          foreach($this->getFamilyMembers($family) as $familyMember) {
            // Delete the member's contributions by calling the deleteContribution function 
            $this->deleteContribution(0, 0, $familyMember->ID);
          }
        }

        // See if the number parameter is entered
        if (isset($number) && $number != 0) {
          // Remove the person's dues
          $this->deleteContribution(0, 0, $number);
        }

        // Create a delete query with the family member table and give this a where paramater with the ID
        $statement = $conn->prepare('DELETE FROM familielid WHERE ID = :id OR Familie = :family');

        // Link the ID to the parameter and then run the query
        $statement->execute([
          'id' => $number,
          'family' => $family,
        ]);

        // If all went well throw the successful message
        echo '<div class="message good"><p><strong>Succesvol </strong> het familie lid verwijderd</p></div>';
      } catch(PDOException $e) {
          // If an error occurred echo this and return null
          echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
      }
  }

  // The getContributionsList extracts all the contributions from the database with all the fields there and puts them into an array to return
  public function getContributionsList() {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    // The now empty array with contributions
    $contributions = array();

    try {
      // Create the select query with the database table contributie
      $stmt = $conn->prepare("SELECT * FROM contributie");
      // Run the query
      $stmt->execute();

      // set the resulting array to associative and loop through the results
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Add the new contribution objects to the previously created array
        $contributions[] = new Contribution($row['ID'], $row['Lid'], $row['Betaald'], $row['Boekjaar']);
      }
  } catch(PDOException $e) {
      // If an error occurred echo this and return null
      echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
      return null;      
    }

    // Return contributions if no errors occurred
    return $contributions;
  } 

  // select a contribution from the database by id
  public function getContribution($number) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // We create the select query to which we pass a WHERE parameter in the contribution table
      $stmt = $conn->prepare("SELECT * FROM contributie WHERE ID = :id");
      
      // Link the contribution number to the paramater ID.
      $stmt->execute([
        'id' => $number
      ]);

      // Go through the result of the query that was executed
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Return the first best result in the contribution class
        return new Contribution($row['ID'], $row['Lid'], $row['Betaald'], $row['Boekjaar']);
      }
    } catch(PDOException $e) {
      // If an error occurred echo this and return null
      echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
      return null;
    }
  }

  // select all contribution from the database by member
  public function getContributions($member) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    // We create an empty array that we can return later
    $contributions = [];

    try {
      // We create a SELECT query in the contributions table with the where parameter member
      $stmt = $conn->prepare("SELECT * FROM contributie WHERE Lid = :member");
      
      // Add the values to the parameter of the query and then execute the query
      $stmt->execute([
          'member' => $member
      ]);

      // Go through the query execution results
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          // Add the contributions to the created array
          $contributions[] = new Contribution($row['ID'], $row['Lid'], $row['Betaald'], $row['Boekjaar']);
      }
    } catch(PDOException $e) {
      // If an error occurred echo this and return null
      echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
      return null;            
    }

    // Return the array of contributions if no errors occurred
    return $contributions;
  }
  
  // insert a contribution into the database
  public function addContribution($member, $payed, $bookyear) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // Make an insert query to the database in the contributions table and provide the member ID, payed amount and bookyear ID
      $statement = $conn->prepare('INSERT INTO contributie (Lid, Betaald, Boekjaar)
      VALUES (:member, :payed, :bookyear)');

      // Add the member, payed and bookyear to the 3 parameters requested in the query and then run the query
      $statement->execute([
        'member' => intval($member),
        'payed' => floatval($payed),
        'bookyear' => intval($bookyear)
      ]);

      // If all went well throw the successful message
      echo '<div class="message good"><p><strong>Succesvol </strong> de contributie toegevoegd</p></div>';
    } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
    }
  }

  // edit a contribution in the database
  public function editContribution($contribution) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // We create an update query with the contribution table and give all columns except the ID a SET with parameters and do this only for the rows that have the same ID as the ID parameter
      $statement = $conn->prepare('UPDATE contributie SET Lid = :member, Betaald = :payed, Boekjaar = :bookyear WHERE ID = :id');

      // Add the values to the parameters requested to be populated and then run the query
      $statement->execute([
        'id' => $contribution->ID,
        'member' => intval($contribution->member),
        'payed' => floatval($contribution->payed),
        'bookyear' => intval($contribution->bookyear)
      ]);

      // If all went well throw the successful message
      echo '<div class="message good"><p><strong>Succesvol </strong> de contributie aangepast</p></div>';
    } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
    }
  }

  // delete a contribution from the database
  public function deleteContribution($number = 0, $bookyear = 0, $member = 0) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // Create a delete query with the contributions table and give this a where paramater with the ID or bookyear ID or the member ID
      $statement = $conn->prepare('DELETE FROM contributie WHERE ID = :id OR Boekjaar = :bookyear OR Lid = :member');

      // Link the ID to the parameter and then run the query
      $statement->execute([
        'id' => $number,
        'bookyear' => $bookyear,
        'member' => $member,
      ]);

      // If all went well throw the successful message
      echo '<div class="message good"><p><strong>Succesvol </strong> de contributie verwijderd</p></div>';
    } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
    }
  }

  // select all member types from the database
  public function getMemberTypesList() {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    // The now empty array with membertypes
    $memberTypes = array();

    try {
      // Create the select query with the database table soortlid
      $stmt = $conn->prepare("SELECT * FROM soortlid");
      // Run the query
      $stmt->execute();

      // set the resulting array to associative and loop through the results
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Add the new membertype objects to the previously created array
        $memberTypes[] = new MemberType($row['ID'], $row['Naam'], $row['ContributiePercentage'], $row['Omschrijving']);
      }
    } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
        return null;      
    }

    // Return contributions if no errors occurred
    return $memberTypes;
  } 

  // select a member type from the database by ID
  public function getMemberType($number) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // We create the select query to which we pass a WHERE parameter in the membertype table
      $stmt = $conn->prepare("SELECT * FROM soortlid WHERE ID = :id");
      
      // Link the membertype number to the paramater ID.
      $stmt->execute([
        'id' => $number
      ]);

      // Go through the result of the query that was executed
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          // Return the first best result in the membertype class
          return new MemberType($row['ID'], $row['Naam'], $row['ContributiePercentage'], $row['Omschrijving']);
      }
    } catch(PDOException $e) {
      // If an error occurred echo this and return null
      echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
      return null;
    }
  }
  
  // add a member type to the database
  public function addMemberType($name, $procentage, $description) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // Make an insert query to the database in the membertypes table and provide the name, procentage and description
      $statement = $conn->prepare('INSERT INTO soortlid (Naam, ContributiePercentage, Omschrijving)
      VALUES (:name, :procentage, :description)');

      // Add the name, procentage and description to the 3 parameters requested in the query and then run the query
      $statement->execute([
        'name' => $name,
        'procentage' => intval($procentage),
        'description' => $description
      ]);

      // If all went well throw the successful message
      echo '<div class="message good"><p><strong>Succesvol </strong> het abonnement toegevoegd</p></div>';
    } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
    }
  }

  // edit a member type in the database
  public function editMemberType($memberType) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    try {
      // We create an update query with the membertype table and give all columns except the ID a SET with parameters and do this only for the rows that have the same ID as the ID parameter
      $statement = $conn->prepare('UPDATE soortlid SET Naam = :name, ContributiePercentage = :procentage, Omschrijving = :description WHERE ID = :id');

      // Add the values to the parameters requested to be populated and then run the query
      $statement->execute([
          'id' => $memberType->ID,
          'name' => $memberType->name,
          'procentage' => floatval($memberType->procentage),
          'description' => $memberType->description
      ]);

      // If all went well throw the successful message
      echo '<div class="message good"><p><strong>Succesvol </strong> het abonnement aangepast</p></div>';
    } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
    }
  }

  // delete a member type from the database
  public function deleteMemberType($number) {
    // Add the file that connects to the database
    include 'inc/process/connect.php';

    // See if it is number 1 or while ID 1 if so throw an error message that it cannot be deleted
    if ($number === 1) {
      echo '<div class="message failure"><p><strong>Foutmelding:</strong> De Standaard abonnement kan niet worden verwijderd. Dit is omdat er altijd een abonnement beschikbaar moet zijn.</p></div>';
      return;
    }

    try {
      // Get all users who have the subscription number we want to remove and update their new subscription to the 1st subscription
      foreach($this->getFamilyMembers(0, intval($number)) as $member) {
        $member->memberType = 1;
        $this->editFamilyMember($member);
      }

      // Create a delete query with the membertype table and give this a where paramater with the ID
      $statement = $conn->prepare('DELETE FROM soortlid WHERE ID = :id');

      // Link the ID to the parameter and then run the query
      $statement->execute([
        'id' => $number,
      ]);

        // If all went well throw the successful message
        echo '<div class="message good"><p><strong>Succesvol </strong> het abonnement verwijderd</p></div>';
    } catch(PDOException $e) {
        // If an error occurred echo this and return null
        echo '<div class="message failure"><p><strong>Foutmelding:</strong> ' . $e->getMessage() . '</p></div>';
    }
  }
}
?>