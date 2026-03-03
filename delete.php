<?php
require 'config.php';

if (isset($_GET['delete'])) {
    $orders_id = $_GET['delete']; 

    $stmt = $pdo->prepare("DELETE FROM orders WHERE orders_id = ?");
    $stmt->execute([$orders_id]);

    header("Location: landing.php");
    exit;
}
?>