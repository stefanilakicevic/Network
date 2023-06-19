<?php 
// ovaj fajl se odnosi na connection.php za deo ako konekcija nije uspela: header("Location: error.php?m=" . $conn->connect_error);
// i na fajl database.php, ako upis u bazu nije izvrsen
// s tim sto je meni zakomentarisan, ipak smo se odlucili za die, umesto header

    $poruka = ""; // ako nemam prosledjeni parametar `m` poruka je prazna
    // da li sam dosla GET metodom i da li imam neki prosledjeni parametar `m`
    if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['m']))
    {
        // ako imam proslednjeni parametar `m`, onda imamo promenljivu poruka 
        $poruka = $_GET['m'];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Network</title>
    <link rel="stylesheet" href="style.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>
<div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h1>Ooops! An error occured!</h1>
                <div class="error">
                    <?php echo $poruka; ?>     <!-- ovde mi ispisi poruku, koju smo definisali gore-->
                </div>
                <p>
                    Return to <a href="index.php" class="btn btn-primary">home page</a>.
                </p>
            </div>
        </div>
    </div> 
</body>
</html>