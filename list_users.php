<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3309;dbname=quan_ly_nha_tro', 'smartroom', 'smartroom');
$stmt = $pdo->query("SELECT id, name, username, email, role, status FROM users ORDER BY id ASC LIMIT 50");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
