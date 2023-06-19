<?php

require_once "connection.php";

$sql = "CREATE TABLE IF NOT EXISTS `users`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE = InnoDB;
";

$sql .= "CREATE TABLE IF NOT EXISTS `profiles`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(255) NOT NULL,
    `gender` CHAR(1),
    `dob` DATE,
    `id_user` INT UNSIGNED NOT NULL UNIQUE,
    `profile_image` VARCHAR(255),
    PRIMARY KEY(`id`),
    FOREIGN KEY(`id_user`) REFERENCES `users`(`id`)
        ON UPDATE CASCADE ON DELETE NO ACTION
) ENGINE = InnoDB;
";

$sql .= "CREATE TABLE IF NOT EXISTS `followers`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_sender` INT UNSIGNED NOT NULL,
    `id_receiver` INT UNSIGNED NOT NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`id_sender`) REFERENCES `users`(`id`)
        ON UPDATE CASCADE ON DELETE NO ACTION,
    FOREIGN KEY(`id_receiver`) REFERENCES `users`(`id`)
        ON UPDATE CASCADE ON DELETE NO ACTION
) ENGINE = InnoDB;
";

$sql .= "ALTER TABLE `profiles` ADD COLUMN `profile_image` VARCHAR(255)";

// ukljucili smo file connection.php, tako da imamo na raspolaganju i objekat $conn
// kada imamo neki upit ka bazi uvek koristimo objekat $conn i pozivamo query ili multi_query, ove metode omogucavaju izvrsavanje SQL upita, jedan ili vise njih istovremeno
if($conn->multi_query($sql))
{
    echo "<p>Tables created successfully</p>"; // ako je upit u bazu izvrsen, ispisace se ova poruka
}
else
{
    header("Location: error.php?m=" . $conn->error); // ako nije, preusmeri mena error.php i ispisi gresku
}













?>