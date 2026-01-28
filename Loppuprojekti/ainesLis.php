<?php
session_start();
require_once "yhteys.php";

// Ilmoitus teksti käyttäjälle
$ilmoitus = "";

// Lomakkeen käsittely
if(isset($_POST["lisaa"])) {
    
    $aines = trim($_POST["aines"]);

    // Tyhjän syötteen tarkastus
    if ($aines == "") {
        $ilmoitus = "Aines ei saa olla tyhjä.";
    }

    else {
        // Tarkistus onko syöte jos olemassa
        $haku = $yhteys->prepare("SELECT COUNT(*) FROM ainesosa WHERE nimi = ?");
        $haku->bind_param("s", $aines);
        $haku->execute();
        $haku->bind_result($maara);
        $haku->fetch();
        $haku->close();

        if ($maara > 0) {
            $ilmoitus = "Aines on jo tietokannassa.";
        }
        else {
            // Lisätään uusi aines
            $lisaaAines = $yhteys->prepare("INSERT INTO ainesosa (nimi) VALUES (?)");
            $lisaaAines->bind_param("s", $aines);

            if ($lisaaAines->execute()) {
                $ilmoitus = "Aines Lisätty";
            }
            else {
                $ilmoitus = "Lisäys epäonnistui!";
            }
            $lisaaAines->close();
        }
    }
};

// Haetaan kaikki ainesosat
$ainesetsi = $yhteys->query("SELECT nimi FROM ainesosa ORDER BY nimi ASC");
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrinkitNiklas - Aineksen lisäys</title>
    <link rel="stylesheet" href="tyyli.css">
</head>
<body>        
    <div class="layout">
        
        <aside class="sidebar">
            <h1>Drinkkiarkisto</h1>
            <p>Aineksen lisäys</p>
            <nav class="nav-links">
                <?php
                // Sivulle ei pääse ilman kirjautumista
                if (!isset($_SESSION['rooli'])) {
                    header("Location: login.php");
                    exit();
                }

                // Navigaatio käyttäjän roolin mukaan
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

        <section class="form-section">
            <h2>Lisää uusi ainesosa</h2>

            <?php if ($ilmoitus != ""): ?>
                <p style="color:white; font-weight:600;">
                    <?= htmlspecialchars($ilmoitus) ?>
                </p>
            <?php endif; ?>

            <form method="post" action="">
                <input type="text" id="aines" name="aines" placeholder="Anna aines Esim. Vodka">

                <button type="submit" name="lisaa">Lisää</button>
            </form>
        </section>

        <section class="list-section">
            <h2>Kaikki ainesosat</h2>

            <ul class="aineslista">
                <?php while ($rivi = $ainesetsi->fetch_assoc()): ?>
                    <li><?= htmlspecialchars($rivi["nimi"]) ?></li>
                <?php endwhile; ?>
            </ul>
        </section>
    </main>
    </div>
</body>
</html>