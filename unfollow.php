<?php 
    // Onaj ko salje zahtev za otrpacivanje: to je logovan korisnik
    // Onaj kome je upucen zahtev za otpracivanje: dohvatamo iz URL-a

    session_start();
    if(empty($_SESSION["id"]))
    {
        header("Location: index.php");
    }
    $id = $_SESSION["id"];
    require_once "connection.php";

    if(empty($_GET["friend_id"])) // ako u url nema friendId
    {
        header("Location: index.php"); // redirektuj ga na index.php
    }
    $friendId = $conn->real_escape_string($_GET["friend_id"]); // id kome upucujemo zahtev

    $q = "DELETE FROM `followers`
            WHERE `id_sender` = $id
            AND `id_receiver` = $friendId";

    $conn->query($q);

    header("Location: followers.php");
?>