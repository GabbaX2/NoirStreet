<?php
session_start();

// Check if the add_to_cart form was submitted
if (isset($_POST['add_to_cart'])) {
    // Get product information from the form
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;
    $product_name = isset($_POST['product_name']) ? $_POST['product_name'] : null;
    $price = isset($_POST['price']) ? $_POST['price'] : null;
    $image = isset($_POST['image']) ? $_POST['image'] : null;
    $brand = isset($_POST['brand']) ? $_POST['brand'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;
    $size = isset($_POST['size']) ? $_POST['size'] : null;

    // Validate that we have all required information
    if ($product_id && $product_name && $price && $image && $brand && $size) {
        // Initialize cart if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        // Check if product already exists in cart
        $exists = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id_p'] == $product_id) {
                $item['quantity'] += 1;
                $exists = true;
                break;
            }
        }

        // If it doesn't exist, add it
        if (!$exists) {
            $cart_item = array(
                'id_p' => $product_id,
                'nome_p' => $product_name,
                'prezzo' => $price,
                'url_img' => $image,
                'nome_b' => $brand,
                'descrizione' => $description,
                'taglia' => $size,
                'quantity' => 1
            );

            $_SESSION['cart'][] = $cart_item;
        }

        // If user is logged in, add to cart in database as well
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];

            // Database connection
            $conn = new mysqli("localhost:8080", "root", "", "streetnoirdb");

            if (!$conn->connect_error) {
                $conn->set_charset("utf8");

                // Get next cart ID
                $id_query = "SELECT MAX(id_car) as max_id FROM carrelli";
                $result = $conn->query($id_query);
                $row = $result->fetch_assoc();
                $new_id = $row['max_id'] ? ($row['max_id'] + 1) : 1;

                // Insert into carrelli table
                $insert_query = "INSERT INTO carrelli (id_car, data_c, id_p) VALUES (?, NOW(), ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("ii", $new_id, $product_id);
                $stmt->execute();

                // Insert/update utente_carrello
                $check_query = "SELECT * FROM utente_carrello WHERE id_u = ? AND id_car = ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->bind_param("ii", $user_id, $new_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows == 0) {
                    // Get user information
                    $user_query = "SELECT nome, cognome, email FROM utenti WHERE id_u = ?";
                    $user_stmt = $conn->prepare($user_query);
                    $user_stmt->bind_param("i", $user_id);
                    $user_stmt->execute();
                    $user_result = $user_stmt->get_result();
                    $user = $user_result->fetch_assoc();

                    // Insert user-cart relation
                    $relation_query = "INSERT INTO utente_carrello (id_u, id_car, nome, cognome, email, data_c) 
                                      VALUES (?, ?, ?, ?, ?, NOW())";
                    $relation_stmt = $conn->prepare($relation_query);
                    $relation_stmt->bind_param("iisss", $user_id, $new_id, $user['nome'], $user['cognome'], $user['email']);
                    $relation_stmt->execute();
                }

                $conn->close();
            }
        }

        // Set success message
        $_SESSION['cart_message'] = "Product added to cart successfully!";
    } else {
        // Set error message
        $_SESSION['cart_message'] = "Error: Missing product information!";
    }

    // Redirect back to the page where the user came from
    $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Index.php';
    header('Location: ' . $redirect);
    exit();
} else {
    // If someone tries to access this file directly, redirect to home
    header('Location: Index.php');
    exit();
}
?>