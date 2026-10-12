<?php
try {
    $p = new PDO('mysql:host=127.0.0.1;port=3309;dbname=quan_ly_nha_tro', 'smartroom', 'smartroom');
    echo "PDO OK! User count: " . $p->query("SELECT COUNT(*) FROM users")->fetchColumn();
} catch (Exception $e) {
    echo "PDO ERR: " . $e->getMessage();
}
