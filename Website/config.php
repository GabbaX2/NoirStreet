<?php
session_start();

$host = 'localhost:8080';
$dbname = 'streetnoirdb';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

session_start();

if (!isset($_SESSION['user_id'])) {
    // Se l'utente non è loggato, puoi decidere di non mostrare i prodotti
    // oppure reindirizzare a una pagina di login
    header("Location: Sign-In.php");
    exit();
}

$conn->set_charset("utf8");
?>