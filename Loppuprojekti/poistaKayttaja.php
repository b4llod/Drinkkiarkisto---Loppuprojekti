<?php
session_start();
require_once "yhteys.php";

// Lomakkeen käsittely
if (isset($_POST["poista_id"])) {
    $poista_id = (int)$_POST["poista_id"];

    $stmt = $yhteys->prepare("DELETE FROM kayttaja WHERE kayttaja_id = ?");
    $stmt->bind_param("i", $poista_id);
    $stmt->execute();
    $stmt->close();
}

// Haetaan kaikki käyttäjät listaukseen
$tulos = $yhteys->query("SELECT kayttaja_id, kayttajatunus FROM kayttaja ORDER BY kayttajatunus");      
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrinkitNiklas - Poista käyttäjä</title>
    <link rel="stylesheet" href="tyyli.css">
</head>
<body>

<div class="layout">

    <aside class="sidebar">
        <h1>Drinkkiarkisto</h1>
        <p>Poista käyttäjä</p>
        <nav class="nav-links">
            <?php
                // Vain admin voi tulla sivulle
                if (!isset($_SESSION['rooli'])) {
                    header("Location: login.php");
                    exit();
                }
                if ($_SESSION['rooli'] == 1) {
                    include_once('naviAdmin.php');
                }
            ?>
        </nav>
    </aside>

    <main>
        <section class="form-section">
            <h2>Poista käyttäjä</h2>

            <?php if ($tulos->num_rows === 0): ?>
                <p>Ei poistettavia käyttäjiä.</p>
            <?php else: ?>

                <?php while ($kayttaja = $tulos->fetch_assoc()): ?>
                    <form method="post" style="margin-bottom: 12px;" onsubmit="return confirm('Haluatko varmasti poistaa tämän käyttäjän?');">

                        <strong><?= htmlspecialchars($kayttaja["kayttajatunus"]) ?></strong>

                        <button type="submit" name="poista_id" value="<?= $kayttaja["kayttaja_id"] ?>">Poista</button>
                    </form>
                <?php endwhile; ?>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
