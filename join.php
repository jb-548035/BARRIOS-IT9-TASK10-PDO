<?php
require 'config.php';

$stmt = $pdo->prepare("
    SELECT 
        u.user_id,
        u.name,
        u.email,
        o.product,
        o.amount
    FROM users u
    INNER JOIN orders o 
        ON u.user_id = o.user_id
");

$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>