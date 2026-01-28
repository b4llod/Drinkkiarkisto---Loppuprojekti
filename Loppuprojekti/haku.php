<?php
session_start();
require_once "yhteys.php";


$tulokset = [];
$haku = "";
$hakutyyppi = "nimi";

// Lomakkeen käsittely
if (isset($_POST["hae"])) {
    $haku = trim($_POST["haku"]);
    $hakutyyppi = trim($_POST["hakutyyppi"]);

    // Haku drinkin nimellä
    if ($hakutyyppi === "nimi") {

        if ($haku === "") {
            // Haetaan kaikki drinkit
            $sql = "SELECT d.drinkki_id, d.nimi, d.juomalaji, d.valmistusohje, a.nimi AS aines, da.maara
                    FROM drinkki d
                    LEFT JOIN drinkki_ainesosa da ON d.drinkki_id = da.drinkki_id
                    LEFT JOIN ainesosa a ON da.ainesosa_id = a.ainesosa_id
                    ORDER BY d.nimi";
            $stmt = $yhteys->prepare($sql);
        }

        else {
            // Haetaam drinkit nimen preusteella
            $sql = "SELECT d.drinkki_id, d.nimi, d.juomalaji, d.valmistusohje, a.nimi AS aines, da.maara
                    FROM drinkki d
                    LEFT JOIN drinkki_ainesosa da ON d.drinkki_id = da.drinkki_id
                    LEFT JOIN ainesosa a ON da.ainesosa_id = a.ainesosa_id
                    WHERE d.nimi LIKE ?
                    ORDER BY d.nimi";
            $stmt = $yhteys->prepare($sql);
            $param = "%$haku%";
            $stmt->bind_param("s", $param);
        }
    }    

    else {
        // Haku ainesosan perusteella
        if ($haku === "") {
            $sql = "SELECT d.drinkki_id, d.nimi, d.juomalaji, d.valmistusohje, a.nimi AS aines, da.maara
                    FROM drinkki d
                    LEFT JOIN drinkki_ainesosa da ON d.drinkki_id = da.drinkki_id
                    LEFT JOIN ainesosa a ON da.ainesosa_id = a.ainesosa_id
                    ORDER BY d.nimi";
            $stmt = $yhteys->prepare($sql);
        } 
        else {
            $sql = "SELECT d.drinkki_id, d.nimi, d.juomalaji, d.valmistusohje, a.nimi AS aines, da.maara
                    FROM drinkki d
                    JOIN drinkki_ainesosa da ON d.drinkki_id = da.drinkki_id
                    JOIN ainesosa a ON da.ainesosa_id = a.ainesosa_id
                    WHERE a.nimi LIKE ?
                    ORDER BY d.nimi";
            $stmt = $yhteys->prepare($sql);
            $param = "%$haku%";
            $stmt->bind_param("s", $param);
        }
    }

    // Sueritetaan kysely ja rakennetaan tulosrakenne
    $stmt->execute();
    $result = $stmt->get_result();

    while ($r = $result->fetch_assoc()) {
        $id = $r["drinkki_id"];

        if (!isset($tulokset[$id])) {
            $tulokset[$id] = ["nimi" => $r["nimi"],
                            "juomalaji" => $r["juomalaji"],
                            "ohje" => $r["valmistusohje"],
                            "ainekset" => []];
        }

        if ($r["aines"]) {
            $tulokset[$id]["ainekset"][] = $r["aines"] . " " . $r["maara"];
        }
    } 
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrinkitNiklas - Haku</title>
    <link rel="stylesheet" href="tyyli.css">
</head>
<body>        
    <div class="layout">
        
        <aside class="sidebar">
            <h1>Drinkkiarkisto</h1>
            <p>Haku</p>
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
            <h2>Haku</h2>

            <form method="post" action="">
                <input type="text" name="haku" value="<?= htmlspecialchars($haku) ?>">

                <br>
                <label>
                <input type="radio" name="hakutyyppi" value="nimi" <?= $hakutyyppi === "nimi" ? "checked" : "" ?>>
                    Nimi
                </label>
                <label>
                    <input type="radio" name="hakutyyppi" value="aines" <?= $hakutyyppi === "aines" ? "checked" : "" ?>>
                    Ainesosa
                </label>
                <br><br>

                <button type="submit" name="hae">Hae</button>
            </form>

            <hr>

            <?php foreach ($tulokset as $drinkki): ?>
                <h3>Nimi: <?= htmlspecialchars($drinkki["nimi"]) ?></h3>
                <p>Juomalaji: <?= htmlspecialchars($drinkki["juomalaji"]) ?></p>

                <strong>Ainesosat:</strong>
                <ul>
                    <?php foreach ($drinkki["ainekset"] as $a): ?>
                        <li><?= htmlspecialchars($a) ?></li>
                    <?php endforeach; ?>
                </ul>

                <strong>Valmistusohje:</strong>
                <p><?= nl2br(htmlspecialchars($drinkki["ohje"])) ?></p>
                <hr>
            <?php endforeach; ?>
        </section>
    </main>
    </div>
</body>
</html>