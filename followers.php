<?php 
    session_start();
    if(empty($_SESSION["id"])) // moramo da proverimo da li je logovan korisnik jer samo logovan moze da pristupi ovoj stranici
    {
        header("Location: index.php");
    }
    $id = $_SESSION["id"];
    require_once "connection.php";
    require_once 'header.php';

    // na proslom casu smo ova dva ifa pisali u posebne stranice follow.php i unfollow.php, i za pracenje pisali <a href='follow.php?friend_id=$friendId' class='follow-link'>Follow</a>
    // a za otpracivanje <a href='unfollow.php?friend_id=$friendId' class='follow-link'>Unfollow</a>
    // sad smo sve ovde ispisali i ta dva fajla nam vise ne trebaju, ali smo dole izmenili: 
    // <a href='followers.php?friend_id=$friendId' class='follow-link'>Follow</a>
    // <a href='followers.php?unfriend_id=$friendId' class='follow-link'>Unfollow</a>

    if(isset($_GET['friend_id'])) // da li postoji get od friend_id
    {
        // zahtev za pracenje drugog korisnika
        $friendId = $conn->real_escape_string($_GET["friend_id"]); // id korisnika kome upucujemo zahtev

        // da li postoji vec takvo pracenje u bazi
        $q = "SELECT * FROM `followers`
                WHERE `id_sender` = $id
                AND `id_receiver` = $friendId";

        $result = $conn->query($q);
        if($result->num_rows == 0) // tek ako ne postoji pracenje onda idemo INSERT INTO u bazu
        {
            $upit = "INSERT INTO `followers`(`id_sender`, `id_receiver`)
            VALUE ($id, $friendId)";
            $result1 = $conn->query($upit);
        }
    }

    if(isset($_GET['unfriend_id']))
    {
        // zahtev da se korisnik otprati
        $friendId = $conn->real_escape_string($_GET["unfriend_id"]); // id kome upucujemo zahtev

        $q = "DELETE FROM `followers`
                WHERE `id_sender` = $id
                AND `id_receiver` = $friendId";

        $conn->query($q);
    }

    // Da odredimo koje druge korisnike prati logovani korisnik
    $upit1 = "SELECT `id_receiver` FROM `followers` WHERE `id_sender` = $id";
    $res1 = $conn->query($upit1); // upit vraca sve korisnike koje logovani korisnik prati
    $niz1 = array(); //  prazan niz $niz1 koji ce sadrzati id_receiver korisnika koje logovani korisnik prati.
    while($row = $res1->fetch_array(MYSQLI_NUM)) // vraca obican niz i jednu vrednost vraca // Petlja while se koristi za iteriranje kroz rezultat $res1. Za svaki red u rezultatu, koristi se metoda fetch_array(MYSQLI_NUM) da bi se dobio običan numerički indeksni niz $row koji sadrži vrednosti iz reda.
    {
        $niz1[] = $row[0]; // Unutar petlje, vrednost $row[0] (prvi element niza $row) se dodaje na kraj niza $niz1 koristeći sintaksu $niz1[] = $row[0].
    }

    //var_dump($niz1);

    // Odrediti koji drugi korisnici prate logovanog korisnika
    $upit2 = "SELECT `id_sender` FROM `followers` WHERE `id_receiver` = $id";
    $res2 = $conn->query($upit2);
    $niz2 = array();
    while($row = $res2->fetch_array(MYSQLI_NUM)) // vraca obican niz i jednu vrednost vraca
    {
        $niz2[] = $row[0];
    }
    //echo "<br>";
    //var_dump($niz2);

    if(isset($_GET['id']))
    {
        $id = $_GET['id'];
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members of social network</title>
    <link rel="stylesheet" href="style.css">

     <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container" style="margin-top: 100px;">
        <div class="card">
                <h1 class="mb-4">See other members from our site</h1>
            <div class="card-body">
                <?php 
                // upit u bazi da izlista sve korisnike osim logovanog korisnika
                    $q = "SELECT `u`.`id`, `u`.`username`,
                            CONCAT(`p`.`first_name`, ' ', `p`.`last_name`) AS `full_name`,
                            `p`.`profile_image`, `p`.`gender`
                            FROM `users`AS `u`
                            LEFT JOIN `profiles` AS `p`
                            ON `u`.`id` = `p`.`id_user`
                            WHERE `u`.`id` != $id
                            ORDER BY `full_name`;
                            "; // prikazujem sve korisnike osim logovanog
                    $result = $conn->query($q);
                    if($result->num_rows == 0)
                    {
                ?>
                        <div class="error">No other users in database : </div>
                <?php  
                    }
                    else
                    {
                ?>
                        <table class="table mx-auto">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                <?php
                        while($row = $result->fetch_assoc())
                        {
                            echo "<tr><td>";
                            if($row["full_name"] !== NULL)
                            {
                                $userId = $row['id'];
                                $profileUrl = "show_profile.php?id=" . $userId;
                                echo '<a href="' . $profileUrl . '">' . $row["full_name"] . '</a>';
                            }
                            else
                            {
                                echo $row["username"];
                            }
                            echo "</td><td>";
                            // Prikazivanje slike ili avatara korisnika
                            if (!empty($row['profile_image'])) 
                            {
                                // Ako korisnik ima sliku profila, prikazi je
                                echo "<img src='{$row['profile_image']}' alt='Profile Image' class='profile-image' style='width: 100px;'>";
                            }
                            else 
                            {
                                // Ako korisnik nema sliku profila, prikazi odgovarajuci avatar na osnovu pola
                                $gender = $row['gender'] ?? ""; // null coalescing operator
        /*                         if (isset($row['gender'])) {
                                    $gender = $row['gender'];
                                } else {
                                    $gender = "";
                                } */
                                $defaultAvatar = "";

                                if ($gender === "m") 
                                {
                                    $defaultAvatar = "avatar/male_avatar.png";
                                } 
                                elseif ($gender === "f") 
                                {
                                    $defaultAvatar = "avatar/female_avatar.png";
                                } 
                                else 
                                {
                                    $defaultAvatar = "avatar/other_avatar.png";
                                }

                                echo "<img src='{$defaultAvatar}' alt='Default Avatar' class='profile-image' style='width: 100px;'>";
                            }
                            echo "</td>";
                            // ovde cemo linkove za pracenje korisnika
                            $friendId = $row["id"];
                            echo "<td>
                            <div class='follow-links'>";
                        
                            if (!in_array($friendId, $niz1)) // da li vrednost $friendId postoji u $niz1, ako ne potoji idemo u drugi if
                            {
                                if (!in_array($friendId, $niz2))  // da li vrednost $friendId, postoji u $niz2, ako ne, postavlja e text na Follow
                                {
                                    $text = "Follow";
                                } 
                                else 
                                {
                                    $text = "Follow back"; // Ako se vrednost $friendId nalazi u nizu $niz2, to znaci da korisnik prati korisnika sa $friendId, pa se postavlja vrednost varijable $text na "Follow back".
                                }
                                echo "<a href='followers.php?friend_id=$friendId' class='follow-link'>$text</a>";
                            } 
                            else // Ako se vrednost $friendId nalazi u nizu $niz1, to znaci da korisnik vec prati korisnika sa $friendId, pa se izvrsava kod unutar else bloka.
                            {
                                echo "<a href='followers.php?unfriend_id=$friendId' class='follow-link'>Unfollow</a>";
                            }
                            
                            echo "</div>
                            </td>";
                            echo "</tr>";
                        }
                ?>
                            </tbody>
                        </table>
                <?php
                    }
                ?>
            <div class="float-end">
                Return to <a href="index.php" class="btn btn-primary ml-2">home page</a>.
            </div>
        </div>
    </div>
</body>
</html>