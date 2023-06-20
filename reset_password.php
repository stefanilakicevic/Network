<?php

require_once "connection.php";
require_once "validation.php";
// zabranjujemo nekom NElogovanom korisniku da pristupi stranici
session_start();
if(!isset($_SESSION["id"]))
{
  header("Location: index.php");
}
$sucMessage = $errMessage = "";
$passwordNewError = $passwordOldError = $retypeError = "";

$id=$_SESSION["id"];
$qPass = "SELECT `password` FROM `users` WHERE `id` = $id;";

$result = $conn->query($qPass);

$row= $result->fetch_assoc(); 
$password = $row['password'];


if($_SERVER["REQUEST_METHOD"] == "POST")
{

  $passwordNew = $conn->real_escape_string($_POST['passwordNew']);
  $retype = $conn->real_escape_string($_POST['retype']);
  $passwordOld = $conn->real_escape_string($_POST['passwordOld']);

  $passwordNewError = passwordValidation($passwordNew);
  $passwordOldError = passwordValidation($passwordOld);
  $retypeError = passwordValidation($retype);

  if($passwordNewError == "" && $passwordOldError == "" && $retypeError =="")
  {
    $q = "";
    if (password_verify($passwordOld, $password))
    {
      if($passwordNew === $retype)
      {
        $passwordNew = password_hash($passwordNew, PASSWORD_DEFAULT);
        $q = "UPDATE `users`
        SET `password` = '$passwordNew' 
        WHERE `id` = $id;";

        if($conn->query($q))
        {
          $sucMessage = "You have changed your password";
        }
        else
        {
          // desila se greska u upitu
          $errMessage = "Error changing password: " . $conn->error;
        }
      }
      else
      {
        $retypeError = "You must enter two same passwords";
      }
    } 
    else
    {
      $passwordOldError = "Invalid password";
    }
  }
}

?>

<!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change password</title>
    <link rel="stylesheet" href="style.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  </head>
  <body>
    <?php if (!empty($sucMessage)) { ?>
      <div class="alert alert-success alert-dismissible fade show">
        <?php echo $sucMessage ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php } ?>
    <?php if (!empty($errMessage)) { ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $errMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php } ?>
      <div class="container">
        <div class="row justify-content-center" style="margin-top: 100px;">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h1 class="card-title text-center">Change password</h1>
              </div>
              <div class="card-body">
                <form action="reset_password.php" method="POST">
                  <div>
                    <div class="mb-3">
                      <label for="passwordOld" class="form-label">Your Old passwrod:</label>
                      <input class="form-control" type="password" name="passwordOld" id="passwordOld">
                      <span class="error">*<?php echo $passwordOldError ?></span>
                    </div>
                  </div>
                  <div>
                    <div class="mb-3">
                      <label for="passwordNew" class="form-label">Your New passwrod:</label>
                      <input class="form-control" type="password" name="passwordNew" id="passwordNew">
                      <span class="error">*<?php echo $passwordNewError ?></span>
                    </div> 
                  </div>
                  <div>
                    <div class="mb-3">
                      <label for="retype" class="form-label">Retype password:</label>
                      <input class="form-control"  type="password" name="retype" id="retype" >
                      <span class="error">*<?php echo $retypeError ?></span>
                    </div>
                  </div>
                  <div>
                    <div class="mb-3">
                      <input type="submit" value="Change password" class="btn btn-primary">
                    </div>
                  </div>
                </form>
                <div>
                  Return to <a href="index.php" class="btn btn-primary ml-2">home page</a>.
                </div>
          </div>
        </div>
      </div>
  </body>
  </html>