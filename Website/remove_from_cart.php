<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'] ?? null;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'ID prodotto mancante']);
    exit;
}

$conn = new mysqli("localhost:8080", "root", "", "streetnoirdb");
$user_id = $_SESSION['user_id'];

// Rimuovi il prodotto dal carrello dell'utente
$result = $conn->query("DELETE c FROM carrelli c JOIN utente_carrello uc ON c.id_car = uc.id_car WHERE uc.id_u = $user_id AND c.id_p = $product_id");

if ($conn->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Prodotto non trovato nel carrello']);
}

$conn->close();
?>