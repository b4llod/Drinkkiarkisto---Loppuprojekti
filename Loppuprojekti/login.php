<?php
session_start();
include "yhteys.php";

$ilmoitus = "";

// Lomakkeen käsittely
if (isset($_POST["kirjaudu"])) {

    $ktunnus = $_POST["ktunnus"];
    $salasana = $_POST["salasana"];

    // Haetaan käyttäjät tietokannasta
    $sql = "SELECT * FROM kayttaja WHERE kayttajatunus = ?";
    $stmt = $yhteys->prepare($sql);
    $stmt->bind_param("s", $ktunnus);
    $stmt->execute();
    $tulos = $stmt->get_result();

    if ($tulos->num_rows == 1) {
        $kayttaja = $tulos->fetch_assoc();

        // Tarkistetaan salasana hash:in avulla
        if (password_verify($salasana, $kayttaja["salasana"])) {
            $_SESSION["rooli"] = $kayttaja["rooli"];
            $_SESSION["kayttaja_id"] = $kayttaja["kayttaja_id"];

            // Uudelleen ohjataan etusivulle
            header("Location: etusivu.php");
            exit;
        }
        else {
            $ilmoitus = "Väärä salasana";
        }
    } 
    else {
        $ilmoitus = "Käyttäjätunnusta ei löydy";
    }
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrinkitNiklas - Kirjaudu sisään</title>
    <link rel="stylesheet" href="tyyli.css">
</head>
<body>

    <div class="layout">
        
        <aside class="sidebar">
            <h1>Drinkkiarkisto</h1>
            <p>Kirjaudu sisään</p>
            <nav class="nav-links">
                <?php
                include_once('naviQuest.php');
                ?>
            </nav>
        </aside>
    <main>

        <section class="form-section">
            <h2>Kirjaudu sisään</h2>

            <?php if ($ilmoitus != ""): ?>
                <p style="color:white; font-weight:600;">
                    <?= htmlspecialchars($ilmoitus) ?>
                </p>
            <?php endif; ?>

            <form method="post" action="">
                <input type="text" id="ktunnus" name="ktunnus" placeholder="Käyttäjätunnus">
                <input type="password" id="salasana" name="salasana" placeholder="Salasana">

                <button type="submit" name="kirjaudu">Kirjaudu</button>
                <p>Etkö ole rekisteröitynyt vielä? <a href="rekisteri.php">Paina tästä.</a></p>
            </form>
        </section>
    </main>
    </div>
</body>
</html>