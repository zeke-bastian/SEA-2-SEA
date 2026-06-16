<?php
$host = 'sql204.infinityfree.com';
$dbname = 'if0_42165908_sea2sea';
$username = 'if0_42165908';
$password = 'viREvfnxdy5jT4J';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>