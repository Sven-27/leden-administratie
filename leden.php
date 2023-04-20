<?php include './inc/templates/header.php'; ?>
        <section class="text-black">
            <p>Hier kunt u de familieleden bewerken.</p>

            <?php if (isset($_GET['action']) && $_GET['action'] === 'add') { ?>
                <?php $controller->addFamilyMemberForm(); ?>
            <?php } elseif (isset($_GET['id']) && isset($_GET['action']) && ($_GET['action'] === 'update' || $_GET['action'] === 'view' || $_GET['action'] === 'delete')) { ?>
                <?php if ($_GET['action'] === 'update') { ?>
                    <?php $controller->editFamilyMemberForm(); ?>
                <?php } ?>
                <?php if ($_GET['action'] === 'delete') { ?>
                    <?php $controller->deleteFamilyMemberForm(); ?>
                <?php } ?>
                <?php if ($_GET['action'] === 'view') { ?>
                    <?php $controller->viewFamilyMember(); ?>
                <?php } ?>
            <?php } else { ?>
            <a href="./leden.php?action=add">
                <button class="good">Familie lid toevoegen</button>
            </a>
            <?php $controller->listFamilyMembers(); ?>
            <?php } ?>
        </section>
    <?php include './inc/templates/footer.php'; ?>
</body>
</html>
