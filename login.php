<?php

session_start(); // cim treba da korsitimo sesiju mora ova funkcija da se pozove, ova funkcija treba odmah na pocetku, kao prva, da se pozove
if(isset($_SESSION["id"]))
{
    header("Location: index.php"); // ako je korisnik logovan samo ga redirektujemo na index.php
}

require_once "connection.php";
require_once 'header.php';

$usernameError = "*";
$passwordError = "*";
$username = ""; // ako udje u if, menja se vrednost toj promenljivoj, ako ne udje if onda je koristimo u formi i kod inputa u value stavljamo usernam

if($_SERVER["REQUEST_METHOD"] == "POST") // proveravamo da li smo dosli post metodom ovde
{
    // korisnik je poslao username i pass i pokusava logovanje
    // $username = $_POST["username"]; // ovako smo juce dohvatali ali i u register.php sve to isto treba da uradimo i dodamo $conn->real_escape_sting


    // korisnik je poslao username i pass i pokusava logovanje
    $username = $conn->real_escape_string($_POST["username"]); // $conn->real_escape_sting ovo uvek pozivamo kada sakupljamo polja iz forme a te vrednosti stavljam u SQL upit, da bi na siguran nacin izvrsili sql upit i nikako direktno da hvatamo vrednosti koje je korisnik uneo, nego na ovaj nacin preko objekta konekcije
    $password = $conn->real_escape_string($_POST["password"]);

    // vrsimo razlicite validacije

    if(empty($username))
    {
        $usernameError = "Username cannot be blank!";
    }
    if(empty($password))
    {
        $passwordError = "Password cannot be blank!";
    }
    if($usernameError == "*" && $passwordError == "*")
    {
        // ovde mozemo da pokusamo da logujemo korisnika, ako svi kredencijali za logovanje se podudaraju
        $q = "SELECT * FROM `users` WHERE `username` = '$username'"; // pass je hesiran, zato ne mozemo direktno da proverimmo
        $result = $conn->query($q);
        if($result->num_rows == 0) // moramo da proverimo ako je u bazi broj redova 0, takav username ne postoji
        {
            $usernameError = "This username doesnt exist!";
        }
        else
        {
            // postoji takav korisnik, proveriti lozinke
            $row = $result->fetch_assoc(); // postoji tacno jedan korisnik koji ima odredjeno korisnicko ime i tacno ga ovde hvatamo
            $dbPassword = $row["password"]; // hesirana vrednost iz baze
            if(!password_verify($password, $dbPassword)) // da bismo proveril da li se sifra poklapa sa hashiranom vrednoscu
            {
                // poklopili su se username ali lozinka nije dobra
                $passwordError = "Wrong password, try again!";
            }
            else
            {
                // dobri su i username i password, izvrsi logovanje
                // echo "<p>Sve je u redu, moze korisnik da se loguje!</p>";
                // da bismo ogli da pristupamo sesiji na vrhu stranice moramo da pozovemo funkciju  session_start(); i sesiji pristupamo preko superglobalne promenljive $_SESSION
                // logovati korisnika znaci dati mu dozvolu da pristupa nekim razlicitim stranicama, taj korisnik ima neki svoj id u bazi podataka i taj id treba da pamtimo u svim tim stranicama, te stranice treba da pristupaju istoj meoriji, koja pamti id logovanog korisnika i to su sesije
                $_SESSION["id"] = $row["id"]; // memorija zajednicka za sve php stranice u nasem kodu i ona izgleda kao asocijativni niz
                $_SESSION["username"] = $row["username"]; // moze i username da se pamti u sesiju
                header("Location: index.php");
            }
        }
    }
    //ako je sve u redu, loguj korisnika
   
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center" style="margin-top: 100px;">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title">Please login</h1>
                    </div>
                    <div class="card-body">
                        <form action="#" method="post"> <!-- forma se salje na istu stranicu, post metodom-->
                            <div class="mb-3">
                                <label for="username" class="form-label">Username:</label>
                                <input type="text" name="username" id="username" class="form-control" value="<?php echo $username; ?>">
                                <span class="error"><?php echo $usernameError; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" name="password" id="password" class="form-control" value="">
                                <span class="error"><?php echo $passwordError; ?></span>
                            </div>
                            <div class="mb-3">
                                <input type="submit" value="Login" class="btn btn-primary float-end">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>