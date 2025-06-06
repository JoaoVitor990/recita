<?php
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'recita';

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die('Conexão falhou: ' . $conn->connect_error);
}
?>
