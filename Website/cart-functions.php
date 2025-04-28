<?php
function ensureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Initialize the cart if it doesn't exist
function initializeCart() {
    ensureSession();
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

// Add product to cart
function addToCart($productId, $productName, $price, $image, $brand, $size) {
    initializeCart();

    // Check if product already exists in cart
    $productExists = false;

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $productId && $item['size'] == $size) {
            // Increment quantity if product already exists
            $_SESSION['cart'][$key]['quantity']++;
            $productExists = true;
            break;
        }
    }

    // Add new product if it doesn't exist in cart
    if (!$productExists) {
        $cartItem = [
            'id' => $productId,
            'name' => $productName,
            'price' => $price,
            'image' => $image,
            'brand' => $brand,
            'size' => $size,
            'quantity' => 1
        ];
        array_push($_SESSION['cart'], $cartItem);
    }

    // Set a flash message for the user
    $_SESSION['cart_message'] = "Product added to cart successfully!";
}

// Remove product from cart
function removeFromCart($index) {
    ensureSession();
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        // Reindex array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        $_SESSION['cart_message'] = "Product removed from cart.";
    }
}

// Update quantity of product in cart
function updateCartQuantity($index, $quantity) {
    ensureSession();
    if (isset($_SESSION['cart'][$index])) {
        if ($quantity > 0) {
            $_SESSION['cart'][$index]['quantity'] = $quantity;
            $_SESSION['cart_message'] = "Cart updated successfully.";
        } else {
            removeFromCart($index);
        }
    }
}

// Get total number of items in cart
function getCartCount() {
    initializeCart();
    $count = 0;

    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }

    return $count;
}

// Get total price of cart
function getCartTotal() {
    initializeCart();
    $total = 0;

    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    return $total;
}

// Get cart contents
function getCartContents() {
    initializeCart();
    return $_SESSION['cart'];
}

// Clear cart
function clearCart() {
    ensureSession();
    $_SESSION['cart'] = [];
    $_SESSION['cart_message'] = "Cart has been cleared.";
}

// Get and clear flash message
function getFlashMessage() {
    ensureSession();
    $message = isset($_SESSION['cart_message']) ? $_SESSION['cart_message'] : '';
    unset($_SESSION['cart_message']);
    return $message;
}
?>