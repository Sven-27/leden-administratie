<?php include './inc/templates/header.php'; ?>
  <section class="text-black">
    <p>Hier kunt u de boekjaren per jaar aanmaken.</p>
    <?php if(isset($_GET['action']) && $_GET['action'] === 'add') { ?>
      <?php $controller->addBookYearForm(); ?>
    <?php } elseif (isset($_GET['id']) && isset($_GET['action']) && ($_GET['action'] === 'update' || $_GET['action'] === 'view' || $_GET['action'] === 'delete')) { ?>
      <?php if(isset($_GET['action']) && $_GET['action'] === 'update') { ?>
        <?php $controller->editBookYearForm(); ?>
      <?php } ?>
      <?php if(isset($_GET['action']) && $_GET['action'] === 'delete') { ?>
        <?php $controller->deleteBookYearForm(); ?>
      <?php } ?>
      <?php if(isset($_GET['action']) && $_GET['action'] === 'view') { ?>
        <?php $controller->viewBookYearForm(); ?>
      <?php } ?>
    <?php } else { ?>
      <a href="./contributies.php?action=add" class="btn btn-primary">
        <button class="good">boekjaar toevoegen</button>  
      </a>
      <?php $controller->listBookYears(); ?>
    <?php } ?>
  </section>
<?php include './inc/templates/footer.php'; ?>
</body>
</html>