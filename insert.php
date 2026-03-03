<?php
require 'config.php';

if (isset($_POST['add'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $product = $_POST['product'];
    $amount = str_replace(',', '', $_POST['amount']);

    // Check if user already exists
    $check = $pdo->prepare("SELECT user_id FROM users WHERE name = ? AND email = ?");
    $check->execute([$name, $email]);
    $existingUser = $check->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        // User exists → use existing ID
        $user_id = $existingUser['user_id'];
    } else {
        // User does not exist → insert new
        $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$name, $email]);

        $user_id = $pdo->lastInsertId();
    }

    $stmt2 = $pdo->prepare("
        INSERT INTO orders (user_id, product, amount) 
        VALUES (?, ?, ?)
    ");
    $stmt2->execute([$user_id, $product, $amount]);

    header("Location: landing.php");
    exit;
}
?>