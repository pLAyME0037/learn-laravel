<?php
$dbFile = __DIR__ . '/../database/database.sqlite';
if (! file_exists($dbFile)) {
    echo "SQLite DB not found at $dbFile\n";
    exit(1);
}

$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->query('SELECT * FROM login_histories ORDER BY id DESC LIMIT 1');
$row  = $stmt->fetch(PDO::FETCH_ASSOC);
if (! $row) {
    echo "No login history found\n";
    exit(0);
}

print_r($row);
