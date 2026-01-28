<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrinkitNiklas - Etusivu</title>
    <link rel="stylesheet" href="tyyli.css">
</head>
<body>

    <div class="layout">
        
        <aside class="sidebar">
            <h1>Drinkkiarkisto</h1>
            <p>Etusivu</p>

            <nav class="nav-links">
                <?php
                // Sivulle ei pääse ilman kirjautumista
                if (!isset($_SESSION['rooli'])) {
                    header("Location: login.php");
                    exit();
                }
                // Navigaatiot käyttäjän roolin mukaan
                if ($_SESSION['rooli'] == 1) {
                    include_once('naviAdmin.php');
                }
                elseif ($_SESSION['rooli'] == 'user') {
                    include_once('naviUser.php');
                }
                ?>
            </nav>
        </aside>
    <main>

        <section class="front-page">
            <h1>Tervetuloa drinkkiarkistoon!</h1>
            <form method="post" action="">
                <p> <strong>Mitä voit tehdä Drinkkiarkistossa?</strong><br><br>
                🔍 Hakea drinkkejä nimen tai raaka-aineiden perusteella<br><br>
                🍹 Tutkia klassikkoja ja uusia suosikkeja<br><br>
                ➕ Lisätä omia drinkkejä ja raaka-aineita arkistoon<br><br>
                🧑‍🍳 Oppia uusia yhdistelmiä ja kokeilla rohkeasti erilaisia makuja<br><br>
                🚫 Löytää alkoholittomia vaihtoehtoja (mocktailit)<br><br>
                </p>
            </form>
        </section>
    </main>
    </div>
</body>
</html>