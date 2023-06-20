<?php

require_once "connection.php";

$sql = "ALTER TABLE `profiles` ADD COLUMN `bio` TEXT";

if($conn->query($sql))
{
    echo "<p>Nova kolona je uspeno dodata</p>"; 
}
else
{
    header("Location: error.php?m=" . $conn->error);
}



?>