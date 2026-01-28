<?php
session_start();
require_once "yhteys.php";

// Hyväksy-nappi: asettaa hyväksytty = 1 
if (isset($_POST["hyvaksy"])) {
    $id = (int)$_POST["hyvaksy"];
    $stmt = $yhteys->prepare("UPDATE drinkki SET hyvaksytty = 1 WHERE drinkki_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();  
}

// Poista-nappi: poistetaan resepti
if (isset($_POST["hylkaa"])) {
    $id = (int)$_POST["hylkaa"];
    $stmt = $yhteys->prepare("DELETE FROM drinkki WHERE drinkki_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();  
}

// Haetaan kaikki hyväksyntää odottavat reseptit
$tulos = $yhteys->query("SELECT drinkki_id, nimi FROM drinkki WHERE hyvaksytty = 0 ORDER BY nimi ASC");

?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrinkitNiklas - Resepti hyväksy/hylkää</title>
    <link rel="stylesheet" href="tyyli.css">
</head>
<body>

<div class="layout">

    <aside class="sidebar">
        <h1>Drinkkiarkisto</h1>
        <p>Resepti hyväksy/hylkää</p>
        <nav class="nav-links">
            <?php
                // Vain admin pääsee tälle sivulle
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
            <h2>Resepti hyväksy/hylkää</h2>

            <?php if ($tulos->num_rows == 0): ?>
                <p>Ei odottavia reseptejä.</p>
            <?php else: ?>

            <?php while ($rivi = $tulos->fetch_assoc()): ?>
            <form method="post" style="margin-bottom: 10px;">
                <strong><?= htmlspecialchars($rivi["nimi"]) ?></strong>

                <button type="submit" name="hyvaksy" value="<?= $rivi["drinkki_id"] ?>">
                    Hyväksy
                </button>

                <button type="submit" name="hylkaa" value="<?= $rivi["drinkki_id"] ?>" onclick="return confirm('Haluatko varmasti hylätä tämän reseptin?');">
                    Hylkää
                </button>
            </form>
            <?php endwhile; ?>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>