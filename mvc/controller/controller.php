<?php
// Add the model class to the file
include_once("./mvc/model/Model.php");

class Controller {
  public $model;

  // When initializing the object for this from
  public function __construct() {
    // Initialize the model 
    $this->model = new Model(); 
  }

  // The ListDashboard feature was created to provide insight into family contributions using a table
  public function listDashboard() {
    // Retrieve all families registered in the records
    $families = $this->model->getFamiliesList();

    // Go through all the families
    foreach($families as $family) {
      // Get all family members and put them as objects in the members variable
      $family->members = $this->model->getFamilyMembers($family->ID);

      // Set for the family the open contribution at standard 0
      $family->open_contribution = 0;
      // Set for the family the payed contribution at standard 0
      $family->payed_contribution = 0;
      // Set for the family the total contribution at standard 0
      $family->total_contribution = 0;

    // Go through all family members
    foreach($family->members as $member) {
      // Retrieve the corresponding subscription from this member
      $member->memberType = $this->model->getMemberType($member->memberType);
      // Collect all contributions from this member
      $member->contributions = $this->model->getContributions($member->ID);

      // Set for the member the open contribution at standard 0
      $member->open_contribution = 0;
      // Set for the member the payed contribution at standard 0
      $member->payed_contribution = 0;
      // Set for the family the total contribution at standard 0
      $member->total_contribution = 0;

      // Go through all member contributions
      foreach($member->contributions as $contribution) {
        // Retrieve corresponding fiscal year of contribution
        $contribution->bookyear = $this->model->getBookYear($contribution->bookyear);

        // Set the family outstanding dues to the new value using this sum
        // Sum: open_contribution = current open_contribution from family + (bookyear_price - payed contribution of this contribution)
        $family->open_contribution = floatval($family->open_contribution) + (floatval($contribution->bookyear->agePrice($member->age(), $member->memberType->procentage)) - floatval($contribution->payed));
        // Set the family paid contribution to the new value using this sum
        // Sum: payed_contribution = current payed_contribution from family + payed contribution of this contribution
        $family->payed_contribution = floatval($family->payed_contribution) + floatval($contribution->payed);
        // Set the family total contribution to the new value using this sum
        // Sum: total_contribution = current total_contribution from family + price of this contribution
        $family->total_contribution = floatval($family->total_contribution) + floatval($contribution->bookyear->agePrice($member->age(), $member->memberType->procentage));
    
        // Put the family member's outstanding dues according to this sum
        // Sum: open_contribution = current open_contribution from family member + (bookyear_price - payed contribution of this contribution)
        $member->open_contribution = floatval($member->open_contribution) + (floatval($contribution->bookyear->agePrice($member->age(), $member->memberType->procentage)) - floatval($contribution->payed));
        // Put the family member's paid dues against this sum
        // Sum: payed_contribution = current payed_contribution from family member + payed contribution of this contribution
        $member->payed_contribution = floatval($member->payed_contribution) + floatval($contribution->payed);
        // Put the family member's total contributions using this sum
        // Sum: total_contribution = current total_contribution from family member + price of this contribution
        $member->total_contribution = floatval($member->total_contribution) + floatval($contribution->bookyear->agePrice($member->age(), $member->memberType->procentage));

        // Put the outstanding dues from the current dues using this sum
        // Sum: current open_contribution = bookyear_price - payed contribution of this contribution
        $contribution->open_contribution = (floatval($contribution->bookyear->agePrice($member->age(), $member->memberType->procentage)) - floatval($contribution->payed));
        // Using this sum, put the total contribution from the current contribution
        // Sum: current total_contribution = bookyear_price
        $contribution->total_contribution = floatval($contribution->bookyear->agePrice($member->age(), $member->memberType->procentage));
       }
     }
   }

    // Add the file to this function that takes care of the table so that it is comprehensible to the user
    include './mvc/view/dashboardlist.php';
  }

  // The listBookYears feature was created to provide insight into all the bookyears using a table
  public function listBookYears() {
    // Retrieve all fiscal years 
    $BookYears = $this->model->getBookYearsList();

    // Add the file that provides a table so we can make the fiscal years transparent
    include './mvc/view/bookyearlist.php';
  }

  // This feature creates the view of detail page
  public function viewBookYear() {
    // Check whether a financial year has been requested using a GET request
    if (isset($_GET) && isset($_GET['id'])) {
      // Get the requested bookyear
      $BookYears = [$this->model->getBookYear($_GET['id'])];
      
      // Add the file that provides a table so we can make the fiscal years transparent
      include './mvc/view/bookyearlist.php';
    } else {
        // Send the error code that there isnt a bookyear requested
        echo '<div class="message failure"><p>Geen boekjaar opgevraagd</p></div>';
    }
  }
  
  // The addBookYearForm function was created to provide form handling and insight into the form
  public function addBookYearForm() {
    // Add the file where the connection to the database is made.
    include './inc/process/connect.php';

    // See if a POST request was made and if so if this is the accounting form and if all fields are included
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['bookyear_submit']) && isset($_POST['bookyear_year']) && isset($_POST['bookyear_submit']) && isset($_POST['bookyear_price'])) {

      // See if the bookyear number is even a number if not throw an error
      if (!is_numeric($_POST['bookyear_year'])) {
        $error_year = 'Het jaar is geen valide nummer.';
      } else {
          // Check if the year is between 1900 and this year
          if ($_POST['bookyear_year'] < 1900 || $_POST['bookyear_year'] > intval(date("Y"))) {
            $error_year = 'Het ingevoerde jaar moet tussen 1900 en '.date("Y").' zijn';
          }
      }
          
      // Check if the bookyear price is even a number if not throw an error
      if (!is_numeric($_POST['bookyear_price'])) {
        $error_price = 'Het ingevoerde bedrag voor het boekjaar is geen valide nummer.';   
      } else {
          // Check if the bookyear price is negative, if it is throw an error
          if ($_POST['bookyear_price'] <= 100) {
            $error_price = 'Het ingevoerde bedrag mag niet negatief en onder de 100 zijn.';
          }   
      }
          
      // Before adding the bookyear check if there is an error occured
      if (!isset($error_year) && !isset($error_price)) {
        // Add the new bookyear
        $this->model->addBookYear($_POST['bookyear_year'], $_POST['bookyear_price']);
      }
    }

    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      include './mvc/view/addBookYearForm.php';
    } else {
      // There is no database connection. Show a text that this is so.
      echo '<div class="message failure"><p>Je kunt het toevoeg formulier niet bekijken, zolang er geen database verbinding is.</p></div>';
    }
  }
  
  // With the editBookYearForm you can edit a financial year and get the form to edit a financial year
  public function editBookYearForm() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';
    // Add the class to of a fiscal year
    include_once('./mvc/model/BookYear.php');

    // Check whether the editBookYear form made a POST request and whether an ID was provided
    if (isset($_POST) && isset($_POST['bookyear_edit']) && isset($_POST['bookyear_id'])) {
      // See if there is a also a GET request to check the financial year ID
      if (isset($_GET) && isset($_GET['id'])) {
        // Check whether the ID of the GET and POST request match
        if ($_GET['id'] != $_POST['bookyear_id']) {
          echo '<div class="message failure"><p>Het ID komt niet overeen met meegegeven ID</p></div>';        
        }
      }

        // Check that the given financial year price is a number
        if (!is_numeric($_POST['bookyear_price'])) {
          $error_price = 'Het ingevoerde bedrag voor het boekjaar is geen valide nummer.';   
        } else {
            // Check that the number provided is not a negative number
            if ($_POST['bookyear_price'] < 0) {
              $error_price = 'Het ingevoerde bedrag mag niet negatief zijn.';
            }   
        }

        // Check if there are no errors occured
        if (!isset($error_price)) {
          $this->model->editBookYear(new BookYear($_POST['bookyear_id'], $_POST['bookyear_year'], $_POST['bookyear_price']));
        }
      }

      // Check whether a financial year has been requested using a GET request
      if (isset($_GET) && isset($_GET['id'])) {
        // Get the requested bookyear
        $bookyear = $this->model->getBookYear($_GET['id']);
        // Include the file with the form to edit a bookyear
        include './mvc/view/editBookYearForm.php';
      } else {
        // Send the error code that there isnt a bookyear requested
        echo '<div class="message failure"><p>Geen boekjaar opgevraagd</p></div>';
      }
  }
  
  // The deleteBookYearForm is there to display a form that allows you to delete a financial year and or control the functions behind it should you delete it
  public function deleteBookYearForm() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';

    // Check that a POST request was made and that it comes from the correct form with corresponding ID
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['bookyear_delete']) && isset($_POST['bookyear_id'])) {
      // See if there is also a GET request with a given ID
      if (isset($_GET) && isset($_GET['id'])) {
        // Check that the ID of the GET request matches the POST ID
        if ($_GET['id'] != $_POST['bookyear_id']) {
          echo '<div class="message failure"><p>Het ID komt niet overeen met meegegeven ID</p></div>';        
        }
      }
          
        // If the ID was good, this can be implemented. Deletes a financial year permanently
        $this->model->deleteBookYear($_POST['bookyear_id']);
    }

      // See if the conn variable is declared and if it has a value. 
      if (isset($conn) && $conn) {
        include './mvc/view/deleteBookYearForm.php';
      } else {
        // There is no database connection. Show a text that this is so.
        echo '<div class="message failure"><p>Je kunt het toevoeg formulier niet bekijken, zolang er geen database verbinding is.</p></div>';
      }
  }

  // Use the listFamilies function to retrieve all families and display them neatly in a table
  public function listFamilies() {
    // Retrieve all families from the database
    $Families = $this->model->getFamiliesList();

    // Add the file that puts all families neatly into a table
    include './mvc/view/familieslist.php';
  }

  // This feature creates the view of detail page
  public function viewFamily() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';
    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      // Check if no error messages occurred, if not view the family
      if (isset($_GET) && isset($_GET['id'])) {
        // Retrieve the family with the requested ID
        $Families = [$this->model->getFamily($_GET['id'])];
        // Add the table to view the family
        include './mvc/view/familieslist.php';
      } else {
        // Send error message
        echo '<div class="message failure"><p>Geen familie opgevraagd</p></div>';
      }
    } else {
        echo '<div class="message failure"><p>Je kunt geen detail pagina kijken, zolang er geen verbinding is.</p></div>';
    }
  }
  
  // With the addFamilyForm, you add a new family using a form that is added when connected to a database
  public function addFamilyForm() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';

    // Check that a POST request has been made and that all required fields exist 
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['family_submit']) && isset($_POST['family_name']) && isset($_POST['family_address'])) {

      // Check that the family name is not empty, it is mandatory that it is filled in
      if (empty($_POST['family_name'])) {
        $error_name = 'De ingevoerde familie naam is leeg';
      }

      // Check that the address is not empty, it is mandatory that it is filled in
      if (empty($_POST['family_address'])) {
        $error_address = 'De ingevoerde adres is leeg';
      }

      // If an error occurred then the family may be added to the database
      if (!isset($error_name) && !isset($error_address)) {
        // Add the family to the database
        $this->model->addFamily($_POST['family_name'], $_POST['family_address']);
      }
    }

    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      include './mvc/view/addFamiliesForm.php';
    } else {
      // There is no database connection. Show a text that this is so.
      echo '<div class="message failure"><p>Je kunt het toevoeg formulier niet bekijken, zolang er geen database verbinding is.</p></div>';
    }
  }
  
  // The editFamilyForm function is there to display a form that allows you to edit a family, it also checks if there is a request to edit a family
  public function editFamilyForm() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';
    // Add the family class
    include_once('./mvc/model/Family.php');

    // Check whether a POST request has been made to modify a family and whether an ID has been given to which family it refers to
    if (isset($_POST) && isset($_POST['family_edit']) && isset($_POST['family_id'])) {
      // See if a GET request was also made with an ID
      if (isset($_GET) && isset($_GET['id'])) {
        // Check whether the ID of the GET and POST request match each other
        if ($_GET['id'] != $_POST['family_id']) {
          echo '<div class="message failure"><p>Het ID komt niet overeen met meegegeven ID</p></div>';          
        }
      }

      // Check that at least one family name has been entered, as it is mandatory
      if (empty($_POST['family_name'])) {
        $error_name = 'De ingevoerde familie naam is leeg';
      }

      // Check whether an address has been entered with the family, as this is also mandatory
      if (empty($_POST['family_address'])) {
        $error_address = 'De ingevoerde adres is leeg';
      }

      // Check if no error messages occurred, if not adjust the family
      if (!isset($error_name) && !isset($error_address)) {
        $this->model->editFamily(new Family($_POST['family_id'], $_POST['family_name'], $_POST['family_address']));
      }
    }

    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      // Check if no error messages occurred, if not adjust the family
      if (isset($_GET) && isset($_GET['id'])) {
        // Retrieve the family with the requested ID
        $family = $this->model->getFamily($_GET['id']);
        // Add the form used to modify a family
        include './mvc/view/editFamilyForm.php';
      } else {
        // Send error message
        echo '<div class="message failure"><p>Geen familie opgevraagd</p></div>';
      }
    } else {
      echo '<div class="message failure"><p>Je kunt het bijewerk formulier niet bekijken, zolang er geen database verbinding is.</p></div>';
    }    
  }
  
  // The deleteFamilyForm is to support a family removal process using a form and functions to delete a family should this be requested
  public function deleteFamilyForm() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';

    // See if a POST request id done with fields that are required
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['family_delete']) && isset($_POST['family_id'])) {
      // Check whether a GET request was made with a given ID
      if (isset($_GET) && isset($_GET['id'])) {
        // Check whether the ID given in the POST request matches the GET request
        if ($_GET['id'] != $_POST['family_id']) {
          echo '<div class="message failure"><p>Het ID komt niet overeen met meegegeven ID</p></div>';              
        }
      }
        
      // If all went well, the family may be removed
      $this->model->deleteFamily($_POST['family_id']);
    }

    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
        include './mvc/view/deleteFamilyForm.php';
    } else {
      // There is no database connection. Show a text that this is so.
      echo '<div class="message failure"><p>Je kunt het verwijder formulier niet bekijken, zolang er geen database verbinding is.</p></div>';
    }
  }

  // The listFamilyMembers function is used to retrieve all family members and make them visible through a table
  public function listFamilyMembers() {
    // Collect all family members
    $FamilyMembers = $this->model->getFamilyMembersList();

    // Add the file with the table that puts down all family members
    include './mvc/view/familymemberslist.php';
  }

  // This feature creates the view of detail page
  public function viewFamilyMember() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';
    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      // Check if no error messages occurred, if not view the family member
      if (isset($_GET) && isset($_GET['id'])) {
        // Retrieve the family with the requested ID
        $FamilyMembers = [$this->model->getFamilyMember($_GET['id'])];
        // Add the form used to modify a family
        include './mvc/view/familymemberslist.php';
      } else {
        // Send error message
        echo '<div class="message failure"><p>Geen familie opgevraagd</p></div>';
      }
    } else {
      echo '<div class="message failure"><p>Je kunt geen detail pagina kijken, zolang er geen verbinding is.</p></div>';
    }
  }
  
  // The addFamilyMemberForm function is there to add a form where family members can be created and or if there is a POST request to create it execute it
  public function addFamilyMemberForm() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';

    // Check that a POST request has been made to add a family member and that all fields are included
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['familymember_submit']) && isset($_POST['familymember_name']) && isset($_POST['familymember_family']) && isset($_POST['familymember_birthdate']) && isset($_POST['familymember_membertype'])) {
      
      // Check whether a family name has been entered, if not give an error message
      if (empty($_POST['familymember_name'])) {
        $error_name = 'De ingevoerde naam is leeg';
      }

      //  Check that family is filled in and that this is a number given that it is linked to the family ID
      if (empty($_POST['familymember_family']) || !is_numeric($_POST['familymember_family'])) {
        $error_family = 'De ingevoerde familie is niet valide';
      }

      // Check that the birthdate field is entered and that it is a real date
      if (empty($_POST['familymember_birthdate']) || !validateDate($_POST['familymember_birthdate'])) {
        $error_birthdate = 'De ingevoerde geboortedatum is incorrect';
      }

      // Check that the membertype field is not empty and that it has a numeric value
      if (empty($_POST['familymember_membertype']) || !is_numeric($_POST['familymember_membertype'])) {
        $error_membertype = 'De ingevoerde lidmaatschap is niet valide';
      }

      // If no error messages have occurred, the addFamilyMember function may be executed and the values are sent with it
      if (!isset($error_name) && !isset($error_family) && !isset($error_birthdate) && !isset($error_membertype)) {
        $this->model->addFamilyMember($_POST['familymember_name'], intval($_POST['familymember_family']), $_POST['familymember_birthdate'], intval($_POST['familymember_membertype']));
      }
    }

    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      // Get all the families needed for in the selection field, so we can choose from them which family the user belongs to
      $families = $this->model->getFamiliesList();
      // Get all the member types needed for in the selection field, so we can choose from them which one the user gets
      $membertypes = $this->model->getMemberTypesList();

      // Add the add form for familymembers
      include './mvc/view/addFamilyMemberForm.php';
    } else {
      // There is no database connection. Show a text that this is so.
      echo '<div class="message failure"><p>Je kunt het toevoeg formulier niet bekijken, zolang er geen database verbinding is.</p></div>';
    }
  }
  
  // The editFamilyMemberForm allows us to visually modify users in the database
  public function editFamilyMemberForm() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';
    // Add the familymember class
    include_once('./mvc/model/FamilyMember.php');

    // Check that a POST request is made and that all fields that are required are included 
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['familymember_edit']) && isset($_POST['familymember_id']) && isset($_POST['familymember_name']) && isset($_POST['familymember_family']) && isset($_POST['familymember_birthdate']) && isset($_POST['familymember_membertype'])) {   
      // See if a GET request is active and if it contains a key with an ID
      if (isset($_GET) && isset($_GET['id'])) {
        // See if the 2 IDs of the GET and POST match if not throw an error
        if ($_GET['id'] != $_POST['familymember_id']) {
          echo '<div class="message failure"><p>Het ID komt niet overeen met meegegeven ID</p></div>';            
        }
      }

        // See if a name is entered, if not throw an error message
        if (empty($_POST['familymember_name'])) {
          $error_name = 'De ingevoerde naam is leeg';
        }

        // See if a family is provided and if it is a numeric number, because you are providing the ID if not throw an error message
        if (empty($_POST['familymember_family']) || !is_numeric($_POST['familymember_family'])) {
          $error_family = 'De ingevoerde familie is niet valide';
        }

        // Check that the date of birth is entered and that this is a real date if not throw an error message
        if (empty($_POST['familymember_birthdate']) || !validateDate($_POST['familymember_birthdate'])) {
          $error_birthdate = 'De ingevoerde geboortedatum is incorrect';
        }

        // Check that the membertype is not empty and that the value entered is a number if not throw an error message
        if (empty($_POST['familymember_membertype'])) {
          $error_membertype = 'De ingevoerde lidmaatschap is niet valide';
        }

        // Check that no error messages have occurred if not then the editFamilyMember function may be executed and all values will be sent with it
        if (!isset($error_name) && !isset($error_family) && !isset($error_birthdate) && !isset($error_membertype)) {
          $this->model->editFamilyMember(new FamilyMember($_POST['familymember_id'], $_POST['familymember_name'], intval($_POST['familymember_family']), $_POST['familymember_birthdate'], intval($_POST['familymember_membertype'])));
        }
      }

    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      // Check if there is a GET request and if a key is provided with the ID of requested family member
      if (isset($_GET) && isset($_GET['id'])) {
        // Get the list of all families, so we can choose from them when customizing the user
        $families = $this->model->getFamiliesList();
        // Get the list of all member types, so we can choose from them when customizing the user
        $membertypes = $this->model->getMemberTypesList();
        // Retrieve the specifically queried user from the database
        $familyMember = $this->model->getFamilyMember($_GET['id']);

        // Add the customization form to the file
        include './mvc/view/editFamilyMemberForm.php';
        } else {
          echo '<div class="message failure"><p>Geen familie lid opgevraagd</p></div>';
        }
    } else {
      // There is no database connection. Show a text that this is so.
      echo '<div class="message failure"><p>Je kunt het bijwerk formulier niet bekijken, zolang er geen database verbinding is.</p></div>';
    }
  }
  
  // The deleteFamilyMemberForm function is to make it visual when we are going to delete a user this ensures that a user does not need to be in the database
  public function deleteFamilyMemberForm() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';

    // See if a POST request was made with mandatory fields included
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['familymember_delete']) && isset($_POST['familymember_id'])) {
      // See if a GET request was made with an ID key
      if (isset($_GET) && isset($_GET['id'])) {
        // Compare the GET and POST ID and if they don't match throw an error message
        if ($_GET['id'] != $_POST['familymember_id']) {
          echo '<div class="message failure"><p>Het ID komt niet overeen met meegegeven ID</p></div>';           
        }
      }
          
      // If no error messages occurred run the deleteFamilyMember function with ID entered
      $this->model->deleteFamilyMember($_POST['familymember_id']);
    }

    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      include './mvc/view/deleteFamilyMemberForm.php';
    } else {
      // There is no database connection. Show a text that this is so.
      echo '<div class="message failure"><p>Je kunt het verwijder formulier niet bekijken, zolang er geen database verbinding is.</p></div>';
    }
  }

  // The listContributions function is to view all contributions in 1 table by extracting them from the database
  public function listContributions() {
    // Get all contributions from the database
    $Contributions = $this->model->getContributionsList();

    // Add the table where all contributions are put in
    include './mvc/view/contributionslist.php';
  }

  // This feature creates the view of detail page
  public function viewContribution() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';
    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      // Check if no error messages occurred, if not view the contribution
      if (isset($_GET) && isset($_GET['id'])) {
        // Retrieve the contributio with the requested ID
        $Contributions = [$this->model->getContribution($_GET['id'])];
        // Add the form used to modify a contribution
        include './mvc/view/contributionslist.php';
      } else {
        // Send error message
        echo '<div class="message failure"><p>Geen contributie opgevraagd</p></div>';
      }
      } else {
        echo '<div class="message failure"><p>Je kunt geen detail pagina kijken, zolang er geen verbinding is.</p></div>';
    }
  }
  
  // The addContributionForm is there to display a form that the user can use to add a contribution and be shot into the database with it
  public function addContributionForm() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';
    
    // Check that a POST request has been made and all required fields are included
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['contribution_submit']) && isset($_POST['contribution_member']) && isset($_POST['contribution_payed']) && isset($_POST['contribution_bookyear'])) {
        
      // Check that the member field is not empty and that this is a numeric field if not throw an error message
      if (empty($_POST['contribution_member']) || !is_numeric($_POST['contribution_member'])) {
        $error_member = 'De ingevoerde lid is leeg';
      }

      // check that the payment field is numeric and not negative, because 0 is also empty if something is wrong throw an error message
      if (!is_numeric($_POST['contribution_payed']) || $_POST['contribution_payed'] < 0) {
        $error_payed = 'De ingevoerde betaalde bedrag is leeg';
      }

      // See if there is a fiscal year entered and if it is a number given that you include the ID of the fiscal year
      if (empty($_POST['contribution_bookyear']) && !is_numeric($_POST['contribution_bookyear'])) {
        $error_bookyear = 'De ingevoerde boekjaar is leeg';
      }

      // If no error messages occurred enter the addContribution function and pass in the values entered
      if (!isset($error_member) && !isset($error_payed) && !isset($error_bookyear)) {
        $this->model->addContribution($_POST['contribution_member'], floatval($_POST['contribution_payed']), $_POST['contribution_bookyear']);
      }
    }

    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      // Retrieve all members from the website so that these can be chosen from in the selection field
      $members = $this->model->getFamilyMembersList();
      // Retrieve all fiscal years from the website so that these can be chosen from in the selection field
      $bookyears = $this->model->getBookYearsList();

      // Add the form that allows you to create a fiscal year
      include './mvc/view/addContributionForm.php';
    } else {
      // There is no database connection. Show a text that this is so.
      echo '<div class="message failure"><p>Je kunt het toevoeg formulier niet bekijken, zolang er geen database verbinding is.</p></div>';
    }
  }
  
  // The editContributionForm function is there to visually edit a contribution in its entirety and then forward it to the database should everything be entered correctly
  public function editContributionForm() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';
    // Add the contribution class
    include_once('./mvc/model/Contribution.php');

    // Check that a POST request was made and that all required fields were included
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['contribution_edit']) && isset($_POST['contribution_id']) && isset($_POST['contribution_member']) && isset($_POST['contribution_payed']) && isset($_POST['contribution_bookyear'])) {
      // See if a GET request was made and if an ID key exists
      if (isset($_GET) && isset($_GET['id'])) {
        // Compare the GET and POST ID if they do not match throw an error message
        if ($_GET['id'] != $_POST['contribution_id']) {
          echo '<div class="message failure"><p>Het ID komt niet overeen met meegegeven ID</p></div>';             
        }
      }

      // See if the member field is filled in and if this is a numeric field since you are including the ID if this is not the case throw an error message
      if (empty($_POST['contribution_member']) || !is_numeric($_POST['contribution_member'])) {
        $error_member = 'De ingevoerde lid is leeg';
      }

      // See if the pay field is a number and if it is not negative if 1 of the 2 is the case throw an error message
      if (!is_numeric($_POST['contribution_payed']) || $_POST['contribution_payed'] < 0) {
        $error_payed = 'De ingevoerde betaalde bedrag is leeg';
      }

      // See if a fiscal year has been entered and if it is a numeric number since you are providing an ID of the fiscal year
      if (empty($_POST['contribution_bookyear']) || !is_numeric($_POST['contribution_bookyear'])) {
        $error_bookyear = 'De ingevoerde boekjaar is leeg';
      }

      // If no error messages occurred, the editContribution function may be executed and the values entered may be included
      if (!isset($error_member) && !isset($error_payed) && !isset($error_bookyear)) {
        $this->model->editContribution(new Contribution($_POST['contribution_id'], $_POST['contribution_member'], floatval($_POST['contribution_payed']), $_POST['contribution_bookyear']));
      }
    }

    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      // See if a GET request was made and an ID was provided
      if (isset($_GET) && isset($_GET['id'])) {
        // Retrieve all members from the website so that these can be chosen from in the selection field
        $members = $this->model->getFamilyMembersList();
        // Retrieve all fiscal years from the website so that this can be chosen from in the selection field
        $bookyears = $this->model->getBookYearsList();

        // Retrieve requested contribution
        $contribution = $this->model->getContribution($_GET['id']);

        // Add the update form so you can visually adjust a contribution
        include './mvc/view/editContributionForm.php';
      } else {
          echo '<div class="message failure"><p>Geen contributie opgevraagd</p></div>';
      }
    } else {
      // There is no database connection. Show a text that this is so.
      echo '<div class="message failure"><p>Je kunt het bijwerk formulier niet bekijken, zolang er geen database verbinding is.</p></div>';
    }  
  }
  
  // The deleteContributionForm is there to make it visually transparent to delete a contribution from the database
  public function deleteContributionForm() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';

    // Check that a POST request was made and that all required fields were included
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['contribution_delete']) && isset($_POST['contribution_id'])) {
      // See if a GET request was made and if an ID key was provided
      if (isset($_GET) && isset($_GET['id'])) {
        // Compare the GET and POST ID and if they don't match you throw an error message
        if ($_GET['id'] != $_POST['contribution_id']) {
          echo '<div class="message failure"><p>Het ID komt niet overeen met meegegeven ID</p></div>';             
        }
      }

      // If no errors occurred execute the deleteContribution function and pass in the ID
      $this->model->deleteContribution($_POST['contribution_id']);
    }

    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      include './mvc/view/deleteContributionForm.php';
    } else {
      // There is no database connection. Show a text that this is so.
      echo '<div class="message failure"><p>Je kunt het verwijder formulier niet bekijken, zolang er geen database verbinding is.</p></div>';
    }
  }

  // The listMemberTypes function is there to visually see which memberTypes are all in the database through a table
  public function listMemberTypes() {
    // Retrieve all memberTypes from the database so they can be incorporated into the table
    $MemberTypes = $this->model->getMemberTypesList();

    // Add the table in which all memberTypes are processed
    include './mvc/view/membertypeslist.php';
  }

  // This feature creates the view of detail page
  public function viewMemberType() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';
    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      // Check if no error messages occurred, if not view the membertype
      if (isset($_GET) && isset($_GET['id'])) {
        // Retrieve the membertype with the requested ID
        $MemberTypes = [$this->model->getMemberType($_GET['id'])];
        // Add the form used to modify a membertypes
        include './mvc/view/membertypeslist.php';
      } else {
        // Send error message
        echo '<div class="message failure"><p>Geen abonnement opgevraagd</p></div>';
      }
    } else {
      echo '<div class="message failure"><p>Je kunt geen detail pagina kijken, zolang er geen verbinding is.</p></div>';
    }
  }
  
  // The addMemberTypeForm is there to visually add a subscription or memberType to the database through a form
  public function addMemberTypeForm() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';
    // Check that a POST request was made and that all required fields are present
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['membertype_submit']) && isset($_POST['membertype_name']) && isset($_POST['membertype_procentage']) && isset($_POST['membertype_description'])) {
        
      // See if the name field is filled in if not throw an error message
      if (empty($_POST['membertype_name'])) {
        $error_name = 'De ingevoerde naam is niet geldig';
      }

      // See if the percent field is filled in and if it is a numeric field and if it is a numeric field and if it is a numeric field if not throw an error message
      if (empty($_POST['membertype_procentage']) || !is_numeric($_POST['membertype_procentage'])) {
        $error_procentage = 'De ingevoerde percentage is niet geldig';
      }

      // See if a description is entered if not throw an error message            
      if (empty($_POST['membertype_description'])) {
        $error_description = 'De ingevoerde beschrijving is niet geldig';
      }

      // If no error messages occurred run the addMemberTypes function and pass entered values
      if (!isset($error_name) && !isset($error_procentage) && !isset($error_description)) {
        $this->model->addMemberType($_POST['membertype_name'], intval($_POST['membertype_procentage']), $_POST['membertype_description']);
      }
    }

    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      include './mvc/view/addMemberTypeForm.php';
    } else {
      // There is no database connection. Show a text that this is so.
      echo '<div class="message failure"><p>Je kunt het toevoeg formulier niet bekijken, zolang er geen database verbinding is.</p></div>';
    }
  }
  
  // The editMemberType function is there to visually modify a memberType without the user having to go into the database 
  public function editMemberTypeForm() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';
    // Add the memberType class
    include_once('./mvc/model/MemberType.php');

    // Check that a POST request was made and that all required fields were included
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['membertype_edit']) && isset($_POST['membertype_id']) && isset($_POST['membertype_name']) && isset($_POST['membertype_procentage']) && isset($_POST['membertype_description'])) {
        
      // See if a GET request was made and if an ID was provided
      if (isset($_GET) && isset($_GET['id'])) {
        // Compare the ID of the GET and POST request with each other if they do not match throw an error message
        if ($_GET['id'] != $_POST['membertype_id']) {
          echo '<div class="message failure"><p>Het ID komt niet overeen met meegegeven ID</p></div>';        
        }
      }

      // See if the name field is filled in if not throw an error message
      if (empty($_POST['membertype_name'])) {
        $error_name = 'De ingevoerde naam is niet geldig';
      }

      // See if the percent field is filled in and if it is a numeric field and if it is a numeric field and if it is a numeric field if not throw an error message
      if (empty($_POST['membertype_procentage']) || !is_numeric($_POST['membertype_procentage'])) {
        $error_procentage = 'De ingevoerde percentage is niet geldig';
      }

      // See if a description is entered if not throw an error message            
      if (empty($_POST['membertype_description'])) {
        $error_description = 'De ingevoerde beschrijving is niet geldig';
      }

      // If there are no error messages, the editMemberType function may be executed and the values entered may be included
      if (!isset($error_name) && !isset($error_procentage) && !isset($error_description)) {
        $this->model->editMemberType(new MemberType($_POST['membertype_id'], $_POST['membertype_name'], intval($_POST['membertype_procentage']), $_POST['membertype_description']));
      }
    }

    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      // See if a GET request was made and if an ID was provided
      if (isset($_GET) && isset($_GET['id'])) {
        // Retrieve the memberType from the database so it can be processed in the form
        $memberType = $this->model->getMemberType($_GET['id']);
        // Add the form where we can start modifying the memberType
        include './mvc/view/editMemberTypeForm.php';
      } else {
        echo '<div class="message failure"><p>Geen abonnement opgevraagd</p></div>';
      }
    } else {
      // There is no database connection. Show a text that this is so.
      echo '<div class="message failure"><p>Je kunt het toevoeg formulier niet bekijken, zolang er geen database verbinding is.</p></div>';
    }        
  }
  
  // The deleteMemberTypeForm function is there to make it visually visible when you want to delete a memberType from the database with a confirmation basically 
  public function deleteMemberTypeForm() {
    // Add the file that controls the connection to the database
    include './inc/process/connect.php';

    // Check that a POST request was made and that all required fields were entered
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST) && isset($_POST['membertype_delete']) && isset($_POST['membertype_id'])) {
      // See if a GET request was made with an ID key
      if (isset($_GET) && isset($_GET['id'])) {
        // Compare the GET and POST ID and if they don't match you throw an error message
        if ($_GET['id'] != $_POST['membertype_id']) {
          echo '<div class="message failure"><p>Het ID komt niet overeen met meegegeven ID</p></div>';        
        }
      }
      
    // If there are no error messages run the deleteMemberType function and provide the ID to be deleted
    $this->model->deleteMemberType($_POST['membertype_id']);
    }

    // See if the conn variable is declared and if it has a value. 
    if (isset($conn) && $conn) {
      include './mvc/view/deleteMemberTypeForm.php';
    } else {
      // There is no database connection. Show a text that this is so.
      echo '<div class="message failure"><p>Je kunt het verwijder formulier niet bekijken, zolang er geen database verbinding is.</p></div>';
    }
  }
}

function validateDate($date, $format = 'Y-m-d'){
  $d = DateTime::createFromFormat($format, $date);
  // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
  return $d && $d->format($format) === $date;
}