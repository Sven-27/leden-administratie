<?php include './inc/templates/header.php'; ?>
  <section class="text-black">
    <p>Hier kunt u de contributies bewerken.</p>
    <?php if(isset($_GET['action']) && $_GET['action'] === 'add') { ?>
      <?php $controller->addContributionForm(); ?>
    <?php } elseif (isset($_GET['id']) && isset($_GET['action']) && ($_GET['action'] === 'update' || $_GET['action'] === 'view' || $_GET['action'] === 'delete')) { ?>
      <?php if(isset($_GET['action']) && $_GET['action'] === 'update') { ?>
        <?php $controller->editContributionForm(); ?>
      <?php } ?>
      <?php if(isset($_GET['action']) && $_GET['action'] === 'delete') { ?>
        <?php $controller->deleteContributionForm(); ?>
      <?php } ?>
      <?php if(isset($_GET['action']) && $_GET['action'] === 'view') { ?>
        <?php $controller->viewContributionForm(); ?>
      <?php } ?>
    <?php } else { ?>
      <a href="./contributies.php?action=add" class="btn btn-primary">
        <button class="good">Contributie toevoegen</button>  
      </a>
      <?php $controller->listContributions(); ?>
    <?php } ?>
  </section>
<?php include './inc/templates/footer.php'; ?>
</body>
</html>