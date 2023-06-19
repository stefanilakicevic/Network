<?php
// dodatni fajl za pomocne funkcije za validaciju polja

function usernameValidation($u, $c) // u fajlu register $username i objekat konekcije $conn jer je jedan uslov da mora da bude jedistven u bazi, zato moram da izvrsim upit ka bazi da vidimo da li postoji vec takav username u bazi i zato se o parametara prosledjuje $conn
{
    $query = "SELECT * FROM `users` WHERE `username` = '$u'"; // da li postoji red u tabeli users gde je vrednost kolone username $u
    $result = $c->query($query); // $c = $conn, koji poziva query, koji izvrsava upit nad bazom i rezultat se smesta u $result

    if(empty($u)) // da ne bude prazan
    {
        return "Username cannot be blank";
    }
    elseif(preg_match('/\s/', $u)) // prvi parametar -> trazim razmak, tj space karakter, na ovaj nacin se definise, uvek se stavlja na pocetku i kraju // i izmedju navodimo, a drugi parametar-> gde trazimo
    {
        return "Username cannot contain spaces";
    }
    elseif(strlen($u) < 5 || strlen($u) > 25) // duzina izmedju 5 i 25 karaktera
    {
        return "Username must be between 5 and 25 characters";
    }
    elseif($result->num_rows > 0) // tamo smo selektom dohvatili username i ako je num rows vece od 0 username je zauzeto. $result->num_rows se koristi za proveru broja redova koji su vraceni kao rezultat upita. 
    {
        return "Username is reserved, please choose another one";
    }
    else
    {
        return ""; // kada je sve u redu ostaje prazan string
    }

}

function passwordValidation($u) // u fajlu register $password
{
    if(empty($u)) // da ne bude prazan
    {
        return "Password cannot be blank";
    }
    elseif(preg_match('/\s/', $u)) // prvi parametar -> trazim razmak, tj space karakter, na ovaj nacin se definise, uvek se stavlja na pocetku i kraju // i izmedju navodimo, a drugi parametar-> gde trazimo
    {
        return "Password cannot contain spaces";
    }
    elseif(strlen($u) < 5 || strlen($u) > 50) // duzina izmedju 5 i 50 karaktera
    {
        return "Password must be between 5 and 50 characters";
    }
    else
    {
        return ""; // kada je sve u redu ostaje prazan string
    }
}
 
function nameValidation($n)
{
    $n = str_replace(' ', '', $n); // trazim string sa razamakom, menjam ga praznim stringom
    if (empty($n))
    {
        return "Name cannot be empty";
    }
    elseif (strlen($n) > 50)
    {
        return "Name cannot contain more than 50 characters";
    }
    elseif (preg_match("/^[a-zA-ZŠšĐđŽžČčĆć]+$/", $n) == false)
    {
        return "Name must contain only letters";
    }
    else
    {
        return "";
    }
}

function genderValidation($g)
{
    if($g != "m"  && $g != "f" && $g != "o")
    {
        return "Unknown gender";
    }
    else
    {
        return "";
    }
}

function dobValidation($d)
{
    if(empty($d)) // dozvoljeno je da bude nepopunjeno polje
    {
        return ""; // ok je da dob bude prazno
    }
    elseif($d < "1900-01-01")
    {
        return "Date of birth not valid";
    }
    else
    {
        return "";
    }
}

function profileExists($id, $conn) // moramo da proverimo da li profil vec postoji
{
    $q = "SELECT * FROM `profiles` WHERE `id_user` = $id";
    $result = $conn->query($q);
    if($result->num_rows == 0)
    {
        return false; // logovani korisnik nema profil
    }
    else
    {
        $row = $result->fetch_assoc(); // ako profil postoji vraca asocijativni niz
        return $row;
    }
}




?>