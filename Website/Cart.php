<?php
session_start();

// Database connection
$conn = new mysqli("localhost:8080", "root", "", "streetnoirdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Remove from cart functionality
if (isset($_POST['remove_from_cart']) && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Find the key of the item in the cart array
    $index = array_search($product_id, array_column($_SESSION['cart'], 'id_p'));

    // If the item exists in the cart, remove it
    if ($index !== false) {
        unset($_SESSION['cart'][$index]);
        // Reindex the array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }

    // Redirect to avoid form resubmission
    header('Location: Cart.php');
    exit;
}

// Clear cart functionality
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = array();
    header('Location: Cart.php');
    exit;
}

// Process checkout
if (isset($_POST['checkout']) && !empty($_SESSION['cart']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Insert new order
    $address = $conn->real_escape_string($_POST['address']);
    $city = $conn->real_escape_string($_POST['city']);
    $zipcode = $conn->real_escape_string($_POST['zipcode']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);

    // Genera un nuovo ID per l'ordine
    $result = $conn->query("SELECT MAX(id_o) as max_id FROM ordini");
    $row = $result->fetch_assoc();
    $new_order_id = ($row['max_id'] ?? 0) + 1;

    // Aggiorna la query per includere id_o
    $order_query = "INSERT INTO ordini (id_o, id_u, indirizzo, data_o, citta, cap, metodo_pag) 
               VALUES (?, ?, ?, NOW(), ?, ?, ?)";

    $stmt = $conn->prepare($order_query);
    // Aggiorna bind_param per includere $new_order_id
    $stmt->bind_param("iissss", $new_order_id, $user_id, $address, $city, $zipcode, $payment_method);

    if ($stmt->execute()) {
        $order_id = $new_order_id;

        // Insert products into the order
        $insert_products = "INSERT INTO acquistare (id_o, id_p) VALUES (?, ?)";
        $prod_stmt = $conn->prepare($insert_products);
        $prod_stmt->bind_param("ii", $order_id, $product_id);

        // Add each product to the order
        foreach ($_SESSION['cart'] as $item) {
            $product_id = $item['id_p'];
            $prod_stmt->execute();
        }

        // Clear the cart
        $_SESSION['cart'] = array();

        // Show success message
        $checkout_success = true;
    } else {
        $checkout_error = "Failed to process your order. Please try again.";
    }
}

// Calculate cart total
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['prezzo'] * ($item['quantity'] ?? 1);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NoirStreet | Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Playfair+Display:wght@400;700&display=swap">
    <style>
        body {
            background-color: #000000;
            color: #ffffff;
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .font-playfair {
            font-family: 'Playfair Display', serif;
        }

        /* Fix for content being cut off */
        .container {
            width: 100%;
            max-width: 1280px;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Darker elements */
        .bg-noir {
            background-color: #0d0d0d;
        }

        .border-noir {
            border-color: #1a1a1a;
        }

        /* Improve form elements visibility */
        .input, .select {
            background-color: #0d0d0d !important;
            border-color: #333 !important;
            color: #fff !important;
        }

        /* Make buttons more visible */
        .btn-noir {
            background-color: #1a1a1a;
            color: #fff;
            border: 1px solid #333;
        }

        .btn-noir:hover {
            background-color: #333;
        }

        /* Fix for potential overflows */
        .overflow-fix {
            overflow: hidden;
        }
    </style>
</head>

<body>
<?php include("nav.php"); ?>

<div class="container mx-auto py-12">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Cart Items -->
        <div class="md:w-2/3 w-full px-4">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-playfair font-bold text-white">Shopping Cart</h1>
                <p class="text-sm text-gray-400"><?php echo count($_SESSION['cart']); ?> items</p>
            </div>

            <?php if (empty($_SESSION['cart'])): ?>
                <div class="bg-noir p-12 text-center">
                    <h3 class="text-xl font-playfair mb-4">Your cart is empty</h3>
                    <p class="text-gray-400 mb-6">Add some products to your cart and they will appear here.</p>
                    <a href="index.php" class="btn btn-outline rounded-none">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="flex flex-col md:flex-row bg-noir p-4 overflow-fix">
                            <div class="md:w-1/4 w-full aspect-square">
                                <img src="<?php echo htmlspecialchars($item['url_img'] ?? ''); ?>"
                                     alt="<?php echo htmlspecialchars($item['nome_p'] ?? ''); ?>"
                                     class="product-image">
                            </div>
                            <div class="md:w-3/4 w-full md:pl-6 pt-4 md:pt-0 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start flex-wrap">
                                        <div class="mb-2">
                                            <h3 class="font-semibold"><?php echo htmlspecialchars($item['nome_p'] ?? ''); ?></h3>
                                            <p class="text-sm text-gray-400"><?php echo htmlspecialchars($item['nome_b'] ?? ''); ?></p>
                                            <p class="text-xs text-gray-500 mt-1">Size: <?php echo htmlspecialchars($item['taglia'] ?? ''); ?></p>
                                        </div>
                                        <p class="font-semibold">€<?php echo number_format($item['prezzo'] ?? 0, 2); ?></p>
                                    </div>
                                    <p class="text-sm text-gray-400 mt-2"><?php echo htmlspecialchars($item['descrizione'] ?? ''); ?></p>
                                </div>
                                <div class="flex justify-end items-center mt-4">
                                    <form method="post" action="cart.php">
                                        <input type="hidden" name="product_id" value="<?php echo $item['id_p']; ?>">
                                        <button type="submit" name="remove_from_cart" class="btn btn-sm btn-noir rounded-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-8 flex justify-between flex-wrap gap-4">
                    <a href="Index.php" class="btn btn-outline rounded-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Continue Shopping
                    </a>

                    <form method="post" action="Cart.php">
                        <button type="submit" name="clear_cart" class="btn btn-noir rounded-none">Clear Cart</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <!-- Order Summary -->
        <div class="md:w-1/3 w-full px-4">
            <div class="bg-noir p-6 sticky top-8">
                <h2 class="text-xl font-semibold mb-6 text-white">Order Summary</h2>

                <div class="space-y-4">
                    <div class="flex justify-between">
                        <p class="text-gray-400">Subtotal</p>
                        <p>€<?php echo number_format($cart_total, 2); ?></p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-400">Shipping</p>
                        <p><?php echo $cart_total > 0 ? '€10.00' : '€0.00'; ?></p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-400">Tax</p>
                        <p>€<?php echo number_format($cart_total * 0.22, 2); ?></p>
                    </div>
                    <div class="border-t border-noir pt-4 mt-4">
                        <div class="flex justify-between font-semibold">
                            <p>Total</p>
                            <p>€<?php echo number_format($cart_total + ($cart_total > 0 ? 10 : 0) + ($cart_total * 0.22), 2); ?></p>
                        </div>
                    </div>
                </div>

                <?php if (!empty($_SESSION['cart'])): ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="post" action="Cart.php" class="mt-6 space-y-4">
                            <input type="text" name="address" placeholder="Shipping Address" required class="input input-bordered w-full rounded-none">
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" name="city" placeholder="City" required class="input input-bordered rounded-none">
                                <input type="text" name="zipcode" placeholder="ZIP Code" required pattern="[0-9]{5}" class="input input-bordered rounded-none">
                            </div>
                            <select name="payment_method" required class="select select-bordered w-full rounded-none">
                                <option value="" disabled selected>Payment Method</option>
                                <option value="Paypal">PayPal</option>
                                <option value="Carta di Credito">Credit Card</option>
                                <option value="Bitcoin">Bitcoin</option>
                                <option value="Alla consegna">Cash on Delivery</option>
                            </select>
                            <button type="submit" name="checkout" class="btn btn-outline btn-accent rounded-none w-full">Checkout</button>
                        </form>
                    <?php else: ?>
                        <div class="mt-6 text-center">
                            <p class="text-gray-400 mb-4">Please log in to checkout</p>
                            <a href="Sign-In.php" class="btn btn-outline btn-accent rounded-none w-full">Log In</a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

<!-- Footer -->
<footer class="bg-black text-white border-t border-noir py-12 mt-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="font-playfair text-lg mb-4">NoirStreet.</h3>
                <p class="text-sm text-gray-400">Where streetwear meets luxury. A curated selection of premium urban fashion.</p>
            </div>
            <div>
                <h4 class="font-medium mb-4">Shop</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-white">New Arrivals</a></li>
                    <li><a href="Tees.php" class="hover:text-white">Tees</a></li>
                    <li><a href="Hoodies.php" class="hover:text-white">Hoodies</a></li>
                    <li><a href="Shoes.php" class="hover:text-white">Shoes</a></li>
                    <li><a href="Accessories.php" class="hover:text-white">Accessories</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium mb-4">Company</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-white">About Us</a></li>
                    <li><a href="#" class="hover:text-white">Contact</a></li>
                    <li><a href="#" class="hover:text-white">Terms & Conditions</a></li>
                    <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium mb-4">Connect</h4>
                <div class="flex space-x-4">
                    <a href="#" class="hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                    <a href="#" class="hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                </div>
                <div class="mt-6">
                    <h4 class="font-medium mb-2">Subscribe</h4>
                    <div class="flex">
                        <input type="email" placeholder="Email address" class="input input-bordered rounded-none w-full max-w-xs">
                        <button class="btn btn-noir rounded-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-noir mt-8 pt-8 text-center text-sm text-gray-400">
            <p>&copy; <?php echo date('Y'); ?> NoirStreet. All rights reserved.</p>
        </div>
    </div>
</footer>
</body>
</html>