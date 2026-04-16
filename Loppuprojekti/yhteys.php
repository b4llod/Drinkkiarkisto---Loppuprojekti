<?php
$palvelin = "localhost";
$kayttaja = "root";
$salasana = "";
$tietokanta = "lop_drinkitniklas";

$yhteys = new mysqli($palvelin, $kayttaja, $salasana, $tietokanta);

if ($yhteys->connect_error) {
    die("Tietokantayhteys epäonnistui: " . $yhteys->connect_error);
}

$yhteys->set_charset("utf8");
?>