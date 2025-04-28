<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with return URL
    $_SESSION['redirect_after_login'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Index.php';
    $_SESSION['wishlist_message'] = "Please log in to add items to your wishlist.";
    header('Location: Sign-In.php');
    exit();
}

// Check if the add_to_wishlist form was submitted
if (isset($_POST['add_to_wishlist']) && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];

    // Database connection
    $conn = new mysqli("localhost:8080", "root", "", "streetnoirdb");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");

    // Check if item already exists in wishlist
    $check_query = "SELECT id_w FROM wishlist WHERE id_u = ? AND id_p = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Get next wishlist ID
        $id_query = "SELECT MAX(id_w) as max_id FROM wishlist";
        $id_result = $conn->query($id_query);
        $id_row = $id_result->fetch_assoc();
        $new_id = $id_row['max_id'] ? ($id_row['max_id'] + 1) : 1;

        // Get user information
        $user_query = "SELECT nome FROM utenti WHERE id_u = ?";
        $user_stmt = $conn->prepare($user_query);
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $user = $user_result->fetch_assoc();

        // Create wishlist name based on user's name
        $wishlist_name = $user['nome'] . "'s Wishlist";

        // Insert into wishlist table
        $insert_query = "INSERT INTO wishlist (id_w, nome_w, data_w, id_u, id_p) 
                        VALUES (?, ?, NOW(), ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("isii", $new_id, $wishlist_name, $user_id, $product_id);

        if ($insert_stmt->execute()) {
            // Insert into inserire table (many-to-many relation)
            $relation_query = "INSERT INTO inserire (id_p, id_w) VALUES (?, ?)";
            $relation_stmt = $conn->prepare($relation_query);
            $relation_stmt->bind_param("ii", $product_id, $new_id);
            $relation_stmt->execute();

            $_SESSION['wishlist_message'] = "Product added to wishlist successfully!";
        } else {
            $_SESSION['wishlist_message'] = "Error adding product to wishlist.";
        }
    } else {
        $_SESSION['wishlist_message'] = "This product is already in your wishlist.";
    }

    $conn->close();

    // Redirect back to the page where the user came from
    $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
    header('Location: ' . $redirect);
    exit();
} else {
    // If someone tries to access this file directly, redirect to home
    header('Location: Index.php');
    exit();
}
?>