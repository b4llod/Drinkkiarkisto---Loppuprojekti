<?php
session_start();
require_once "yhteys.php";

$ilmoitus = "";

// Haetaan kaikki ainesosat valikkoa varten
$ainekset = [];
$tulos = $yhteys->query("SELECT ainesosa_id, nimi FROM ainesosa ORDER BY nimi ASC");
while ($rivi = $tulos->fetch_assoc()) {
    $ainekset[] = $rivi;
}

// Lomakkeen käsittely
if (isset($_POST["lisaa"])) {

    // Drinkin tiedot
    $nimi = trim($_POST["nimi"]);
    $juomalaji = trim($_POST["juomalaji"]);
    $ohje = trim($_POST["ohje"]);

    // Valitut ainesosat ja määrät
    $aines1 = $_POST["aines1"];
    $aines2 = $_POST["aines2"];
    $aines3 = $_POST["aines3"];

    $maara1 = trim($_POST["maara1"]);
    $maara2 = trim($_POST["maara2"]);
    $maara3 = trim($_POST["maara3"]);

    // Perustarkistukset
    if ($nimi === "") {
        $ilmoitus = "Nimi ei voi olla tyhjä.";
    }
    else {
        // Tarkistetaan onko drinkki jo olemassa 
        $tarkistus = $yhteys->prepare("SELECT COUNT(*) FROM drinkki WHERE nimi = ?");
        $tarkistus->bind_param("s", $nimi);
        $tarkistus->execute();
        $tarkistus->bind_result($maara);
        $tarkistus->fetch();
        $tarkistus->close();

        if ($maara > 0) {
            $ilmoitus = "Drinkki on jo tietokannassa.";
        }
        elseif ($maara1 == "" && $maara2 == "" && $maara3 == "") {
            $ilmoitus = "Vähintää yhden raaka-aineen määrä on annettava.";
        }
        else {
            // Ehdotetaan drinkki tietokantaan
            $lisaaDrinkki = $yhteys->prepare("INSERT INTO drinkki (nimi, juomalaji, valmistusohje, hyvaksytty) VALUES (?, ?, ?, 0)");
            
            $lisaaDrinkki->bind_param("sss", $nimi, $juomalaji, $ohje);
            $lisaaDrinkki->execute();
            $drinkki_id = $yhteys->insert_id;
            $lisaaDrinkki->close();

            // Lisätään drinkin ainesosat
            $lisaaAines = $yhteys->prepare("INSERT INTO drinkki_ainesosa (drinkki_id, ainesosa_id, maara) VALUES (?, ?, ?)");

            if ($maara1 != "") {
                $lisaaAines->bind_param("iis", $drinkki_id, $aines1, $maara1);
                $lisaaAines->execute();
            }
            
            if ($maara2 != "") {
                $lisaaAines->bind_param("iis", $drinkki_id, $aines2, $maara2);
                $lisaaAines->execute();
            }

            if ($maara3 != "") {
                $lisaaAines->bind_param("iis", $drinkki_id, $aines3, $maara3);
                $lisaaAines->execute();
            }

            $lisaaAines->close();
            $ilmoitus = "Resepti ehdotus lähetetty.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrinkitNiklas - Resepti ehdotus</title>
    <link rel="stylesheet" href="tyyli.css">
</head>
<body>        
    <div class="layout">
        
        <aside class="sidebar">
            <h1>Drinkkiarkisto</h1>
            <p>Resepti ehdotus</p>
            <nav class="nav-links">
                <?php
                // Sivulle ei pääse kirjautumatta
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
            <h2>Ehdota uutta Reseptiä</h2>

            <?php if ($ilmoitus != ""): ?>
                <p><?= htmlspecialchars($ilmoitus) ?></p>
            <?php endif; ?>

            <form method="post" action="">
                <input type="text" id="nimi" name="nimi" placeholder="Nimi">
                <input type="text" id="juomalaji" name="juomalaji" placeholder="Juomalaji">    

                <p>Raaka-aine:</p>

                <select name="aines1" id="aines1">
                    <?php foreach ($ainekset as $r): ?>
                        <option value="<?= $r["ainesosa_id"] ?>"><?= htmlspecialchars($r["nimi"]) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="maara1" placeholder="Määrä"><br><br>

                <select name="aines2" id="aines2">
                    <?php foreach ($ainekset as $r): ?>
                        <option value="<?= $r["ainesosa_id"] ?>"><?= htmlspecialchars($r["nimi"]) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="maara2" placeholder="Määrä"><br><br>

                <select name="aines3" id="aines3">
                    <?php foreach ($ainekset as $r): ?>
                        <option value="<?= $r["ainesosa_id"] ?>"><?= htmlspecialchars($r["nimi"]) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="maara3" placeholder="Määrä"><br><br>

                <textarea name="ohje" placeholder="Ohjeet"></textarea><br><br>

                <button type="submit" name="lisaa">Lisää resepti</button>
            </form>
        </section>
    </main>
    </div>
</body>
</html>