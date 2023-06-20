<?php
 session_start();
require_once "connection.php";
//require_once 'header.php';

if(isset($_GET['id']))
{
    $id = $_GET['id'];
}

if(isset($_SESSION["username"])) 
{
    $username = $_SESSION["username"];
    $id = $_SESSION["id"];
}

$q = "SELECT `u`.`username`,
`p`.`first_name`, 
`p`.`last_name`,
`p`.`gender`,
`p`.`dob`,
`p`.`bio`
FROM `users`AS `u`
LEFT JOIN `profiles` AS `p`
ON `u`.`id` = `p`.`id_user`
WHERE `u`.`id` = $id";

$result = $conn->query($q);

if($result && $result->num_rows > 0)
{
    $row = $result->fetch_assoc();
    $firstName = $row['first_name'];
    $lastName = $row['last_name'];
    $username = $row['username'];
    $dob = $row['dob'];
    $gender = $row['gender'];
    $bio = $row['bio'];

}
else
{
    $firstName = "";
    $lastName = "";
    $username = "";
    $dob = "";
    $gender = "";
    $bio = "";
}

/* $paragraphClass = "";
if($gender === 'm')
{
    $paragraphClass = 'm';
}
elseif($gender === 'f')
{
    $paragraphClass = 'f';
}
else
{
    $paragraphClass = 'o';
} */

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show profile</title>
    <link rel="stylesheet" href="style.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div>
        <h1>Prikaz profila korisnika</h1>
        <?php if($firstName !== "" && $lastName !== "") { ?>
            <p>Pozdrav, <?php echo $firstName . " " . $lastName; ?>!</p>
        <?php } ?>

        <?php if($firstName !== "" && $lastName !== "") { ?>
            <table>
                <tr>
                    <th>First name</th>
                    <td class="<?php echo ($gender === 'm') ? 'm' : (($gender === 'f') ? 'f' : 'o'); ?>"><?php echo $firstName; ?></td>
                </tr>
                <tr>
                    <th>Last name</th>
                    <td class="<?php echo ($gender === 'm') ? 'm' : (($gender === 'f') ? 'f' : 'o'); ?>"><?php echo $lastName; ?></td>
                </tr>
                <tr>
                    <th>Username</th>
                    <td class="<?php echo ($gender === 'm') ? 'm' : (($gender === 'f') ? 'f' : 'o'); ?>"><?php echo $username; ?></td>
                </tr>
                <tr>
                    <th>Date of birth</th>
                    <td class="<?php echo ($gender === 'm') ? 'm' : (($gender === 'f') ? 'f' : 'o'); ?>"><?php echo $dob; ?></td>
                </tr>
                <tr>
                    <th>Gender</th>
                    <td class="<?php echo ($gender === 'm') ? 'm' : (($gender === 'f') ? 'f' : 'o'); ?>"><?php echo $gender; ?></td>
                </tr>
                <tr>
                    <th>About me</th>
                    <td class="<?php echo ($gender === 'm') ? 'm' : (($gender === 'f') ? 'f' : 'o'); ?>"><?php echo $bio; ?></td>
                </tr>
            </table>
        <?php } else { ?>
            <p>Korisnik ne postoji u bazi.</p>
            <?php } ?>
    </div>
    <div>
        Go back to <a href="followers.php" class="btn btn-secondary">Followers</a>.
    </div>
</body>
</html>