<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrinkitNiklas - Tietosuojaseloste</title>
    <link rel="stylesheet" href="tyyli.css">
</head>
<body>

    <div class="layout">
        
        <aside class="sidebar">
            <h1>Drinkkiarkisto</h1>
            <p>Tietosuojaseloste</p>
            <nav class="nav-links">
                <?php
                // näytetään navigaatio käyttäjän roolin mukaan
                if (isset($_SESSION['rooli'])) {
                    if ($_SESSION['rooli'] == 1) {
                        include_once('naviAdmin.php');
                    }
                    elseif ($_SESSION['rooli'] == 'user') {
                        include_once('naviUser.php');
                    }
                }
                else {
                    include_once('naviQuest.php');
                }
                ?>
            </nav>
        </aside>
    <main>

        <section class="form-section">
            <h2>Tietosuojaseloste - drinkkiarkiston käyttäjille</h2>

            <form method="post" action="">
                <h3>Rekisterin nimi</h3>
                <p>Drinkkiarkiston käyttäjärekisteri</p>

                <h3>Rekisterinpitäjä</h3>
                <p>Espoon seudun kuntakoulutusryhmä Omnia<br>
                Upseerinkatu 11, Leppävaara, Espoo</p>

                <h3>Yhteyshenkilö rekisteriä koskevissa asioissa</h3>
                <p>Niklas Jurvelin<br>
                0401267154<br>
                niklas.jurvelin@gmail.fi</p>

                <h3>Henkilötietojen käsittelyn tarkoitus ja oikeusperuste</h3>
                <p>Drinkkiarkiston verkkosovellus kerää ja käsittelee käyttäjätietoja, 
                    jotta käyttäjät voivat rekisteröityä palveluun, hakea ja ehdottaa drinkki- ja ainesosatietoja.</p>
                <p>Henkilötietoja käsitellään koulutustarkoituksessa osana tietojenkäsittely- 
                    ja ohjelmistokehitysprojekteja. Käsittelyn oikeusperuste perustuu käyttäjän 
                    suostumukseen ja koulutustarkoituksen toteuttamiseen.</p>

                <h3>Käsiteltävät henkilötiedot</h3>
                <ul>
                    <li>Käyttäjätunnus</li>
                    <li>Sähköpostiosoite</li>
                    <li>Salasanasuojatut tiedot</li>
                </ul>
            </form>
        </section>
    </main>
    </div>
</body>
</html>