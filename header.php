<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><Menu></Menu></title>
    <link rel="stylesheet" href="style.css?ver=1">
</head>
<body>
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <?php
        //session_start();
        if (isset($_SESSION["id"])) 
        {
            // Logovan korisnik
            echo '
                <li><a href="profile.php">Profile</a></li>
                <li><a href="followers.php">Connections</a></li>
                <li><a href="logout.php">Logout</a></li>
                <li><a href="reset_password.php">Change password</a></li>
                <li><a href="show_profile.php">Show profile</a></li>
            ';
        
        } 
        else 
        {
            // Nelogovan korisnik
            echo '
                <li><a href="register.php">Register</a></li>
                <li><a href="login.php">Login</a></li>
            ';
        }
        ?>
    </ul>
</nav>
</body>
</html>