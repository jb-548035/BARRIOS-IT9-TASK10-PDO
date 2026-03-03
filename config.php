<?php

$host = "localhost";
$dbname = "pdo";
$username = "root";
$password = "";
$port = "3308"; //My XAMPP uses 3308 instead of the default 3306

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>