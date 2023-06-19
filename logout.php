<?php
    session_start(); // pristupamo sesiji
    session_unset(); // ili ovako $_SESSION = array(); // podesimo da niz bude prazna
    session_destroy(); // brise sve podatke iz sesije

    header("Location: index.php");
?>