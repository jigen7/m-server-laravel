<?php
$server_name = "localhost";
$db_name     = "masarap_local";
$username    = "local_dev";
$password    = "XTx4J5bWTvnEGFb9";

try {
    $conn = new PDO("mysql:host=$server_name;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>