<?php 

    session_start();
    require_once "connection.php";
    require_once "validation.php";
    require_once 'header.php';

    $poruka = "";
    if(isset($_GET["p"]) && $_GET["p"] == "ok") // ako je postavljena vrednost 'p' i ako je ta vrednost 'ok' (to smo definisali u register.php, tj kada od register.php dolazim na ovu stranicu)
    {
        $poruka = "You have successfully registered, please login to continue";
    }

    // moramo da proverimo da li je korisnik logovan tj da li postoji sesija. Falj login.php (tu smo preusmerili na index.php)
    $username = "anonymus"; // kada nije logovan korisnik
    if(isset($_SESSION["username"])) // da li postoji ovaj kljuc u ovom nizu
    {
        $username = $_SESSION["username"]; // ako postoji cuvamo ga u $username
        $id = $_SESSION["id"]; // id logovanog korisnika
        // ovaj deo moze da stoji dole u formi izmedju if i else grane
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
            $username = $row["first_name"] . " " . $row["last_name"]; // ovaj kod nam omogucava da kad nam pise welcome, posle toga imamo ime i prezime korisnika profila u naslovu
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?ver=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <title>Social Network</title>
</head>
<body>
<div class="carousel-container">
    <div id="myCarousel" class="carousel slide" data-bs-ride="carousel">
        <ol class="carousel-indicators">
            <li data-bs-target="#myCarousel" data-bs-slide-to="0" class="active"></li>
            <li data-bs-target="#myCarousel" data-bs-slide-to="1"></li>
            <li data-bs-target="#myCarousel" data-bs-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="img/1.png" alt="Slide 1">
            </div>
            <div class="carousel-item">
                <img src="img/9a.png" alt="Slide 2">
            </div>
            <div class="carousel-item">
                <img src="img/12.png" alt="Slide 3">
            </div>
        </div>
        <a class="carousel-control-prev" href="#myCarousel" role="button" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </a>
        <a class="carousel-control-next" href="#myCarousel" role="button" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </a>
    </div>
</div>    
    <?php if(!empty($poruka)) { ?>
        <div class="alert alert-success alert-dismissible fade show"> <!-- kad smo prvi put dosli na stranicu poruka je prazan string, kada smo dosli iz register.php poruka ce se ispisati-->
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <?php echo $poruka ?>
        </div>
    <?php } ?>
    <!-- slajder, pozadinska slika -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <img src="img/6.png" class="card-img-top mt-4" alt="Image">
            </div>
            <div class="col-md-7 ms-md-auto">
                <div class="card mt-5">
                <div class="card-header">
                        <h1 class="card-title">Welcome, <?php echo $username ?>, to our Social Network!</h1> <!-- kada nije logovan korisnik pisace anoniymus, kada je logovan korisnik pisace njegov username -->
                    </div>  
                    <div class="card-body"> <!-- ova dava linka ispod treba da se prikazuju samo kad korisnik nije logovan, dok u else grani prikazujemo linkove kada je korisnik logovan -->
                        <?php if(!isset($_SESSION["username"])) { ?>
                            <div>New to our site? <a href="register.php" class="btn btn-primary">Register here</a> to access our site!</div>
                            <div style="margin-top: 20px;">Already have an account? <a href="login.php" class="btn btn-primary">Login here</a> to continue to our site.</div>
                        <?php } else { ?>
                            <div><?php echo $m ?> a <a href="profile.php" class="btn btn-primary">profile</a>.</div>
                            <div>See other members <a href="followers.php" class="btn btn-primary">here</a>.</div> 
                            <div><a href="logout.php" class="btn btn-primary">Logout</a> from our site.</div>
                        <?php } ?>
                    </div>
                    <img class="card-img-bottom" src="img/9.png" alt="Card image" style="width:100%">
                </div>
            </div>
        </div>
    </div>

</body>
</html>