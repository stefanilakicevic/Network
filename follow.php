<?php 
    // Onaj ko salje zahtev to je logovan korisnik
    // Onaj kome je upucen zahtev: dohvatamo iz URL-a

    session_start();
    if(empty($_SESSION["id"])) // moramo da proverimo da li je logovan korisnik jer samo logovan moze da pristupi ovoj stranici
    {
        header("Location: index.php");
    }
    $id = $_SESSION["id"]; // id korisnika koji salje zahtev
    require_once "connection.php";

    if(empty($_GET["friend_id"])) // get metodom saljemo ovaj zahtev iz followers.php: <a href='follow.php?friend_id=$friendId' >
    {
        header("Location: index.php");
    }
    $friendId = $conn->real_escape_string($_GET["friend_id"]); // id korisnika kome upucujemo zahtev

    $q = "SELECT * FROM `followers`
            WHERE `id_sender` = $id
            AND `id_receiver` = $friendId";

    $result = $conn->query($q);
    if($result->num_rows == 0) // prvo treba da proverimo da i vec postoji pracenje, ako ne postoji onda idemo INSERT INTO u bazu
    {
        $upit = "INSERT INTO `followers`(`id_sender`, `id_receiver`)
        VALUE ($id, $friendId)";
        $result1 = $conn->query($upit);
    }
    header("Location: followers.php");
?>