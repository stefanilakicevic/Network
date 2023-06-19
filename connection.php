<?php
    //mysql_report(MYSQLI_REPORT_OFF);
    $server = "localhost";
    $database = "network";
    $username = "adminNet";
    $password = "adminNet123"; // za domaci dodati novog usera nad bazom

    $conn = new mysqli($server, $username, $password, $database); // mora ovakav raspored // $conn je objekat koji predstavlja vezu sa bazom
    // u nekim verzijama PHP-a, moze da dodje do greske prilikom pogresne konekcije, pa smo dopisali  drugu liniju koda, s tim sto meni nije pravilo gresku, pa sam zakomentarisala
    if($conn->connect_error)
    {
        // header("Location: error.php?m=" . $conn->connect_error); // ako je doslo do greske prebaci me na ovo stranicu
        die("Neuspela konekcija: " . $conn->connect_error); //ili umesto headera da stavimo die
    }
    $conn->set_charset("utf8"); // ako je sve u redu postavimo utf8 (iako smo postavili u mysqlu utf32)


    // Konekcija ka bazi, koju koristimo u svim ostalim fajlovima
    // Pre konekcije, morali smo da napravimo bazu `network` da bismo imali ka cemu da se konektujemo












?>