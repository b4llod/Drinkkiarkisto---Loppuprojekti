<?php
session_start();
require_once "yhteys.php";

// Lomakkeen käsittely
if (isset($_POST["poista_id"])) {
    $poista_id = (int)$_POST["poista_id"];

    $sql = "DELETE FROM drinkki WHERE drinkki_id = ?";
    $stmt = $yhteys->prepare($sql);
    $stmt->bind_param("i", $poista_id);
    $stmt->execute();
    $stmt->close();
}

// Haetaan kaikki drinkit listaukseen
$tulos = $yhteys->query("SELECT drinkki_id, nimi FROM drinkki ORDER BY nimi");
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrinkitNiklas - Poista drinkki</title>
    <link rel="stylesheet" href="tyyli.css">
</head>
<body>

<div class="layout">

    <aside class="sidebar">
        <h1>Drinkkiarkisto</h1>
        <p>Poista drinkki</p>
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
            <h2>Poista drinkki</h2>

            <?php if ($tulos->num_rows === 0): ?>
                <p>Ei poistettavia drinkkejä.</p>
            <?php else: ?>

                <?php while ($drinkki = $tulos->fetch_assoc()): ?>
                    <form method="post" style="margin-bottom: 12px;" onsubmit="return confirm('Haluatko varmasti poistaa tämän drinkin?');">

                        <strong><?= htmlspecialchars($drinkki["nimi"]) ?></strong>

                        <button type="submit" name="poista_id" value="<?= $drinkki["drinkki_id"] ?>">Poista</button>
                    </form>
                <?php endwhile; ?>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
