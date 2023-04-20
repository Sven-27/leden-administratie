    <?php include './inc/templates/header.php'; ?>
        <section class="text-black">
            <p>Welkom op de leden administratie,</p>
            <p>Bekijk hieronder hoeveel er nog moet worden betaald aan contributie per lid. Heeft u vragen neem gerust contact op. <a href="mailto:sven.notermans@live.nl">sven.notermans@live.nl</a></p>
        
            <p>De procentuele berekeningen worden individueel berekend en worden altijd afgerond op 2 decimalen.</p>

            <?php $controller->listDashboard(); ?>
        </section>
    <?php include './inc/templates/footer.php'; ?>
</body>
</html>
