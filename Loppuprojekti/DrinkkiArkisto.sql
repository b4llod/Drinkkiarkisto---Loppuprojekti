CREATE TABLE IF NOT EXISTS kayttaja (
    kayttaja_id INT AUTO_INCREMENT PRIMARY KEY,
    kayttajatunus VARCHAR(50) NOT NULL,
    salasana VARCHAR(255) NOT NULL,
    sahkoposti VARCHAR(100),
    rooli VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS drinkki (
    drinkki_id INT AUTO_INCREMENT PRIMARY KEY,
    nimi VARCHAR(100) NOT NULL,
    juomalaji VARCHAR(50),
    valmistusohje TEXT,
    luoja_id INT,
    hyvaksytty TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (luoja_id) REFERENCES kayttaja(kayttaja_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS ainesosa (
    ainesosa_id INT AUTO_INCREMENT PRIMARY KEY,
    nimi VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS drinkki_ainesosa (
    drinkki_id INT,
    ainesosa_id INT,
    maara VARCHAR(50),
    PRIMARY KEY (drinkki_id, ainesosa_id),
    FOREIGN KEY (drinkki_id) REFERENCES drinkki(drinkki_id) ON DELETE CASCADE,
    FOREIGN KEY (ainesosa_id) REFERENCES ainesosa(ainesosa_id) ON DELETE CASCADE
);