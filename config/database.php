<?php
// DB接続部
try {
    $addr = "mysql:host=localhost; dbname=programming_training; charset=utf8";
    $user = "root";
    $pass = "P@ssw0rd";
    $db = new PDO($addr, $user, $pass);
} catch (PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
    exit();
}
