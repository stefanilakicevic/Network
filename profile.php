<?php 
    // zabranjujemo nekom NElogovanom korisniku da pristupi stranici
    session_start();
    if(!isset($_SESSION["id"]))
    {
        header("Location: index.php");
    }

    // ako je logovan korisnik
    $id = $_SESSION["id"];
    $firstName = $lastName = $dob = $gender = $profile_image = $bio = ""; // u ove promenljive pamtimo odgovarajuce vrednosti iz forme
    $firstNameError = $lastNameError = $dobError = $genderError = $profileImageError = $bioError = "";
    $sucMessage = "";
    $errMessage = "";

    require_once "connection.php";
    require_once "validation.php";
    require_once 'header.php';

    $profileRow = profileExists($id, $conn);
    // $profileRow = false, ako profil ne postoji
    // $profileRow = assoc niz ako profil postoji
    if($profileRow !== false) // da li postoji profil 
    {
        $firstName = $profileRow["first_name"]; // stavljamo vrednosti koje je korisnik uneo u promenljive i kad idemo na edit profil ostace zapamceno sve sto je uneo
        $lastName = $profileRow["last_name"];
        $gender = $profileRow["gender"];
        $dob = $profileRow["dob"];
        $profile_image = $profileRow["profile_image"];
        $bio = $profileRow["bio"];
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") // proveravamo da li dolazimo na stranicu POST metodom
    {
        $firstName = $conn->real_escape_string($_POST["first_name"]);
        $lastName = $conn->real_escape_string($_POST["last_name"]);
        $gender = $conn->real_escape_string($_POST["gender"]);
        $dob = $conn->real_escape_string($_POST["dob"]);
        $bio = $conn->real_escape_string($_POST["bio"]);

        // vrsimo validaciju polja

        $firstNameError = nameValidation($firstName);
        $lastNameError = nameValidation($lastName);
        $genderError = genderValidation($gender);
        $dobError = dobValidation($dob);

        if ($_FILES["profile_image"]["error"] === UPLOAD_ERR_OK) // proveravamo da li je slika uspesno otpremljena, ako jeste nastavlja se sa daljim kodom provere
        {
           // Provera da li korisnik već ima sliku
            if (!empty($profileRow['profile_image'])) 
            {
                // Korisnik već ima sliku, treba je ukloniti pre postavljanja nove
                if (file_exists($profileRow['profile_image'])) 
                {
                    unlink($profileRow['profile_image']);
                }
            }
            // informacije o otpremljenoj fotografiji
            $fileTmpPath = $_FILES["profile_image"]["tmp_name"];  // privremeno ime datokeke tj putanja
            $fileName = $_FILES["profile_image"]["name"]; // originalno ime 
            $fileSize = $_FILES["profile_image"]["size"]; // velicina
            $fileType = $_FILES["profile_image"]["type"]; // tip
            $fileNameCmps = explode(".", $fileName); // sting $filename delimo na delove, koristeci tacku kao separator, tj ova funkcija će rezultovati nizom npr ["1", "png"]. 
            $fileExtension = strtolower(end($fileNameCmps)); // end($fileNameCmps) se koristi da bi se dobio poslednji element niza, u nasem slucaju to je "png". strtolower() se koristi da bi se ekstenzija pretvorila u mala slova.
    
            // Definisemo direktorijum za cuvanje slika
            $uploadDir = "profile_images/";
    
            // Generisemo jedinstveno ime za sliku
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension; // Ovo je vazno kako bi se izbegli eventualni problemi sa preklapanjem imena i mogucim gubitkom ili prebrisavanjem slika.
    
            // Konacna putanja do slike
            $destFilePath = $uploadDir . $newFileName;
    
            // Provera velicine i tipa slike
            $allowedExtensions = array("jpg", "jpeg", "png");
            $maxFileSize = 5 * 1024 * 1024; // 5MB
    
            if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) // funkcija in_array se koristi za proveru da li se odredjena vrednost nalazi u nizu, tj da li se $fileExtension nalazi u nizu $allowedExstensions.
            {
                if (move_uploaded_file($fileTmpPath, $destFilePath)) // move_uploaded_file() se koristi za premestanje otpremljenog fajla na zeljenu odredisnu lokaciju na serveru.
                {
                    // Slika je uspesno otpremljena
                    $profile_image = $destFilePath;

                    // Azuriramo vrednost $profileRow['profile_image'] na novu putanju slike
                    //$profileRow['profile_image'] = $profile_image; // ovako sam resila problem da kada izaberem sliku i stisnem edit, odmah mi se prikaze ta nova slika

                } 
                else 
                {
                    // Greska pri otpremanju slike
                    $profileImageError = "Error uploading profile image.";
                }
            } 
            else 
            {
                // Slika nije u dozvoljenom formatu ili prelazi maksimalnu velicinu
                $profileImageError = "Invalid image format or size.";
            }
        }

        // ako je sve u redu ubacujemo novi red u tabelu `profiles`
        if($firstNameError == "" && $lastNameError == "" && $genderError == "" && $dobError == "" && $profileImageError == "") // id_user nam je zapravo $id tj logovani korisnik
        {
            $q = "";
            if($profileRow === false) // gore smo pozvali funkciju i stavili je u promenljivu $profileRow = profileExists($id, $conn); znaci ako profil ne postoji, ubaci podatke u tabelu tj kreiraj profil
            {
                $q = "INSERT INTO `profiles`(`first_name`, `last_name`, `gender`, `dob`, `profile_image`, `bio`, `id_user`) 
                VALUE
                ('$firstName', '$lastName', '$gender', '$dob', '$profile_image', '$bio', $id)";
            }
            else // ako pofil vec postoji, izmeni profil
            {
                $q = "UPDATE `profiles`
                SET `first_name` = '$firstName',
                `last_name` = '$lastName',
                `gender` = '$gender',
                `dob` = '$dob',
                `profile_image` = '$profile_image',
                `bio` = '$bio'
                WHERE `id_user` = $id
                ";
            }

            if($conn->query($q))
            {
                // uspesno kreiran ili editovan profil
                if($profileRow !== false)
                {
                    $sucMessage = "You have edited your profile"; // ako postoji profil editovali smo ga
                }
                else
                {
                    $sucMessage = "You have created your profile"; // ako ne kreirali smo ga
                }
            }
            else
            {
                // desila se greska u upitu
                $errMessage = "Error creating profile: " . $conn->error;
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container" style="margin-top: 100px;">
    <div class="card">
        <div class="card-body"> <!-- zelim da mi prikazuje alert samo kad ima neka poruka u njemu-->
            <?php if (!empty($sucMessage)) { ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $sucMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <?php if (!empty($errMessage)) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $errMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>
            <h1>Please fill the profile details</h1>
            <form action="#" method="post" enctype="multipart/form-data"> <!-- ovaj deo enctype je vazan posle za koriscenje FILE globalnih promenljivih za slike -->
                <div class="mb-3">
                    <label for="first_name" class="form-label">First name:</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo $firstName; ?>">
                    <span class="error">* <?php echo $firstNameError; ?></span>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last name:</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo $lastName; ?>">
                    <span class="error">* <?php echo $lastNameError; ?></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Gender:</label>
                    <div class="form-check">
                        <input type="radio" name="gender" id="m" value="m" class="form-check-input" <?php if($gender == "m") {echo "checked"; } ?>>
                        <label for="m" class="form-check-label">Male</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="gender" id="f" value="f" class="form-check-input" <?php if($gender == "f") {echo "checked"; } ?>>
                        <label for="f" class="form-check-label">Female</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="gender" id="o" value="o" class="form-check-input" <?php if($gender == "o" || $gender == "") {echo "checked"; } ?>>
                        <label for="o" class="form-check-label">Other</label>
                    </div>
                    <span class="error"><?php echo $genderError; ?></span>
                </div>
                <div class="mb-3">
                    <label for="dob" class="form-label">Date of birth:</label>
                    <input type="date" name="dob" id="dob" class="form-control" value="<?php echo $dob; ?>">
                    <span class="error"><?php echo $dobError; ?></span>
                </div>
                <div class="mb-3">
                    <label for="profile_image" class="form-label">Profile image:</label>
                    <?php if (!empty($profile_image) && file_exists($profile_image)) { ?>
                        <img src="<?php echo $profile_image; ?>" alt="Profile Image" style="width: 25%" class="mt-2">
                    <?php } ?>
                    <input type="file" name="profile_image" id="profile_image" class="form-control">
                    <span class="error"><?php echo $profileImageError; ?></span>
                </div>
                <div class="mb-3">
                    <label for="bio" class="form-label">Biography:</label>
                    <textarea name="bio" id="bio" class="form-control"><?php echo $bio; ?></textarea>
                    <span class="error"><?php echo $bioError; ?></span>
                </div>
                <div class="mb-3">
                    <?php
                    // moze i ovako da se se napise zajedn sa zakomentarisanim inputom ili amo jedan ovaj input sa ternanrnim operatorom
                       /*  $poruka;
                        if($profileRow === false)
                        {
                            $poruka = "Create profile";
                        }
                        else
                        {
                            $poruka = "Edit profile";
                        } */
                    ?>
                     <!-- <input type="submit" value="<?php //echo $poruka ?>"> -->
                   <input type="submit" name="edit_profile" value="<?php echo ($profileRow !== false) ? 'Edit profile' : 'Create profile' ?>" class="btn btn-primary">
                </div>
            </form>
            <div class="float-end">
                Go back to <a href="index.php" class="btn btn-secondary">home page</a>.
            </div>
        </div>
    </div>
</div>
</body>
</html>