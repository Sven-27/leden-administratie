    <?php include './inc/templates/header.php'; ?>
        <section class="text-black">
            <p>Welkom op de leden administratie,</p>
            <p>Bekijk hieronder hoeveel er nog moet worden betaald aan contributie per lid. Heb je vragen of klachten dan kunt u dit melden bij het bestuur of de ontwikkelaar contacteren via <a href="mailto:rubendalebout@gmail.com">rubendalebout@gmail.com</a> bij uitsluitend klachten over de applicatie. Overige klachten mogen bij het bestuur worden neergelegd.</p>
        
            <p>De procentuele berekeningen worden individueel berekend en worden altijd afgerond op 2 decimalen.</p>

            <?php $controller->listDashboard(); ?>
        </section>
    <?php include './inc/templates/footer.php'; ?>
</body>
</html>
