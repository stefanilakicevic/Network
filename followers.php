<?php 
    session_start();
    if(empty($_SESSION["id"])) // moramo da proverimo da li je logovan korisnik jer samo logovan moze da pristupi ovoj stranici
    {
        header("Location: index.php");
    }
    $id = $_SESSION["id"];
    require_once "connection.php";
    require_once 'header.php';

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
        <h1 class="mb-4">See other members from our site</h1>
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
                <table class="table">
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
                        echo $row["full_name"];
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
                    <div class='follow-links'>
                        <a href='follow.php?friend_id=$friendId' class='follow-link'>Follow</a>
                        <a href='unfollow.php?friend_id=$friendId' class='follow-link'>Unfollow</a>
                    </div>
                    </td>";
                    echo "</tr>";
                }
        ?>
                    </tbody>
                </table>
        <?php
            }
        ?>
        <div class="text-center">
            Return to <a href="index.php" class="btn btn-primary ml-2">home page</a>.
            <div class="mt-4"></div> <!-- Prazan prostor ispod dugmeta -->
        </div>
    </div>
</body>
</html>