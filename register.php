<?php
    // ovoj stranici smeju da pristupe samo NElogovani korisnici
    session_start(); // sesija traje sve dok je otvoren brauser
    if(isset($_SESSION["id"]))
    {
        header("Location: index.php"); // ako je korisnik logovan samo ga redirektujemo na index.php
    }

    require_once "connection.php";
    require_once "validation.php";
    require_once 'header.php';

    $usernameError = ""; // kad dolazimo GET metodom na stranicu, nema nikakve greske, zato prazan string
    $passwordError = "";
    $retypeError = ""; // sad idemo u formu i ispod inputa ih primenjujemo
    $username = ""; // moramo da postavimo na prazno da tek kad dodjemo na stranicu sa GET da ne stoji nista u usernamu, a posle smo dole definisali u value inputa da kad korisnik upise userneme i stisne submit (POST metoda) i ako mu izadje neka greska ispise gresku ali ostavi username koji je ukucao da samo promeni
    $password = "";
    $retype = "";

    if($_SERVER["REQUEST_METHOD"] == "POST") //  proverava da li je trenutni zahtev iz forme poslat metodama POST.
    {
        // forma je poslata treba pokupiti vrednosti iz polja
        // Ako je forma poslata to znaci da su korisnikovi unosi dostupni u $_POST superglobalnoj promenljivoj
        $username = $conn->real_escape_string($_POST["username"]); // 'name' vrednost input polja u HTML formi
        $password = $conn->real_escape_string($_POST["password"]); // u ovim promenljivama pamtimo vrednosti koje korisnik unese
        $retype = $conn->real_escape_string($_POST["retype"]);

        // 1) Izvrsiti validaciju za $username

        $usernameError = usernameValidation($username, $conn); // u promenljivu greska treba da smestimo ovu validaciju, jer ova funkcija vraca gresku

        // 2) Izvrsiti validaciju za $password

        $passwordError = passwordValidation($password);

        // 3) Izvrsiti validaciju za $retype

        $retypeError = passwordValidation($retype); // ista validacija kao pass
        if($password !== $retype) // s tim sto dodatno moramo ovo da proverimo jer pass i retype moraju da budu isti
        {
            $retypeError = "You must enter two same passwords";
        }

        // 4.1) Ako su sva polja validna, onda treba dodati novog korisnika, tj treba izvrsiti INSERT upit nad tabelom `users`
        if($usernameError == "" && $passwordError == "" && $retypeError == "")
        {
            // ovde kasnije pisemo INSERT  // id, username, password, za id je autoinkrement pa ga ne pisemo, pass ne treba direktno da upisujemo, vec sifrirane pasvorde
            // lozinka treba prvo da se sifruje
            $hash = password_hash($password, PASSWORD_DEFAULT); // ugradjena funkc i prihvata 2 parametra, prvi je sifra,drugi nacin hashiranja 
            
            $q = "INSERT INTO `users`(`username`, `password`)
            VALUE
            ('$username', '$hash');"; 

            if($conn->query($q))
            {
                // Kreirali smo novog korisnika, vodi ga na stranicu za logovanje
                header("Location: index.php?p=ok"); // u index.php kad dodjemo GET metodom, ako je p = ok uspesno smo se registrovali
            }
            else
            {
                header("Location: error.php?" . http_build_query(['m' => "Greska kod kreiranje usera"])); // ova funkc transformise string koji pisemo kao recenice, nasa slova, znakove da moze da ih posalje preko URL-a
            }
        }

        // 4.2) Ako postoji neko polje koje nije validno ne raditi upit tj ne ne raditi insert, vec vracamo korisnika na stranicu tj na istu formu i prikazujemo odgovorajuce greske
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register new user</title>
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
                    <h1 class="card-title">Register to our site</h1>
                </div>
                <div class="card-body">
                    <form action="register.php" method="POST">     <!-- podaci se salju na istu stranicu POST metodom kad se klikne submit -->
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" name="username" id="username" value="<?php echo $username; ?>"> <!-- u value stavljamo ovako da bi ostao username koji korisnik ukuca kad klinke submit i pojavi se neka greska da mu ostane to sto je ukucao, ali ovo ce da vazi samo kad dodje post metodom, kada dodjemo prvi put na stranicu GET metodom moramo da definisemo promenljivu $username pre if-a kao ostale promenljive i postavimo a budu prazan string -->
                            <span class="error">* <?php echo $usernameError; ?></span>    <!-- zvezdica stoji kod polja kao obavezno polje i posle stavljam $usernameError i nema greske jer smo stigli GET metodom na stranicu i prazan je string, ali ukoliko dodjemo POST metodu ulazimo u validacije iako nesto nije dobro, menjace se i $usernameError i kad dodje do forme i ispise tu promenljivu $usernameError sa novim vrednostima-->
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" name="password" id="password" value=""> <!-- za pass value ostavljamo prazno, jer ako napravi gresku neka otkuca ponovo sve, da mu se ne pojavljuje to sto je otkucao -->
                            <span class="error">* <?php echo $passwordError; ?></span> <!-- KLASA error, vec smo je definisali i u cssu stavili crvenu boju -->
                        </div>
                        <div class="form-group">
                            <label for="retype">Retype password:</label>
                            <input type="password" class="form-control" name="retype" id="retype" value="">
                            <span class="error">* <?php echo $retypeError; ?></span>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Register me!">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<!-- prvi put kad dodjemo na stranicu register.php dolazimo GET metodom, ali cim korisnik unese podatke i klikne na submit-->
</html>
