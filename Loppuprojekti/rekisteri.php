<?php
include "yhteys.php";

$ilmoitus = "";

// Lomakkeen käsittely
if (isset($_POST["rekisteroidy"])) {

    // Poistetaan turhat välilyönnit kentistä ja määritellään ne
    $sposti = trim($_POST['sposti']);
    $ktunnus = trim($_POST['ktunnus']);
    $salasana = trim($_POST['salasana']);

    // Tarkistetaan onko käyttäjätunnus jo tietokannassa
    if (empty($ktunnus)) {
        $ilmoitus = "Käyttäjätunnus ei voi olla tyhjä";
    }
    else {
        $haku = $yhteys->prepare("SELECT kayttaja_id FROM kayttaja WHERE kayttajatunus = ?");
        $haku->bind_param("s", $ktunnus);
        $haku->execute();
        $haku->store_result();

        if ($haku->num_rows > 0) {
            $ilmoitus = "Käyttäjätunnus on jo käytössä!";
        }
        else {
            // Luodaan salasana turvallisesti hash-muodossa
            $hashedPassword = password_hash($salasana, PASSWORD_DEFAULT);

            // Lisätään uusi käyttäjä tietokantaan
            $haku = $yhteys->prepare("INSERT INTO kayttaja (sahkoposti, kayttajatunus, salasana, rooli) VALUES (?, ?, ?, 'user')");
            $haku->bind_param("sss", $sposti, $ktunnus, $hashedPassword);

            if ($haku->execute()) {
                $ilmoitus = "Rekisteröinti onnistui!";
            }
            else {
                $ilmoitus = "Virhe rekisteröinnissä";
            }
        }
        $haku->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrinkitNiklas - Rekisteröinti</title>
    <link rel="stylesheet" href="tyyli.css">
</head>
<body>

    <div class="layout">
        
        <aside class="sidebar">
            <h1>Drinkkiarkisto</h1>
            <p>Rekisteröinti</p>
            <nav class="nav-links">
                <?php             
                include_once('naviQuest.php');
                ?>
            </nav>
        </aside>
    <main>

        <section class="form-section">
            <h2>Rekisteröidy</h2>

            <?php if ($ilmoitus != ""): ?>
                <p style="color:white; font-weight:600;">
                    <?= htmlspecialchars($ilmoitus) ?>
                </p>
            <?php endif; ?>

            <form method="post" action="">
                <input type="text" id="sposti" name="sposti" placeholder="Sähköposti">
                <input type="text" id="ktunnus" name="ktunnus" placeholder="Käyttäjätunnus">
                <input type="password" id="salasana" name="salasana" placeholder="Salasana">

                <button type="submit" name="rekisteroidy">Rekisteröidy</button>
            </form>
        </section>
    </main>
    </div>
</body>
</html>