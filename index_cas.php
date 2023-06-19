<?php 
    session_start();
    require_once "connection.php";
    require_once "validation.php";

    $poruka = "";
    if(isset($_GET["p"]) && $_GET["p"] == "ok")
    {
        $poruka = "You have successfully registered, please login to continue";
    }

    // moramo da proverimo da li je korisnik logovan tj da li postoji sesija
    $username = "anonymus";
    if(isset($_SESSION["username"])) // da li postoji ovaj kljuc u ovom nizu, tj da li je logovan korisnik
    {
        $username = $_SESSION["username"];
        $id = $_SESSION["id"]; // id logovanog korisnika
        $row = profileExists($id, $conn); // da li profil postoji
        $m = "";
        if($row === false)
        {
            // Logovani korisnik nema profil
            $m = "Create";
        }
        else
        {
            // Logovani korisnik ima profil
            $m = "Edit";
            $username = $row["first_name"] . " " . $row["last_name"];
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Network</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="success"> <!-- zameniti nekim elementom iz bootstrapa alert se koristi-->
        <?php echo $poruka ?>
    </div>
    <!-- slajder, pozadinska slika -->
    <h1>Welcome, <?php echo $username ?>, to our Social Network!</h1> <!-- ovde smo napisali if samo ako nije logovan da mu izadje register here i login here -->
    <?php if(!isset($_SESSION["username"])) { ?>
    <p>New to our site? <a href="register.php">Register here</a> to access our site!</p>
    <p>Already have the account? <a href="login.php">Login here</a> to continue to uor site</p>
    <?php } else { ?>
        <p><?php echo $m ?> a <a href="profile.php">profile</a>.</p>
        <p>See other members <a href="followers.php">here</a>.</p> 
        <p><a href="logout.php">Logout</a> from our site.</p>
    <?php } ?>
</body>
</html>