<?php
$servername = "lista-zakupow-db.mysql.database.azure.com";
$username = "adminuser";
$password = "Haslo123";
$dbname = "shopping_list";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
