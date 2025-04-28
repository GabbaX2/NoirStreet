<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: Sign-In.php');
    exit;
}

// Database connection
$conn = new mysqli("localhost:8080", "root", "", "streetnoirdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Get user ID
$user_id = $_SESSION['user_id'];

// Fetch user's orders
$orders_query = "SELECT o.* FROM ordini o WHERE o.id_u = ? ORDER BY o.data_o DESC";
$stmt = $conn->prepare($orders_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>NoirStreet | Your Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Playfair+Display:wght@400;700&display=swap">
</head>

<body class="font-montserrat bg-black text-white">
<?php include("nav.php"); ?>

<div class="container mx-auto px-8 py-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-playfair font-bold">Your Orders</h1>
        <p class="text-sm text-gray-400">
            <?php echo $orders_result->num_rows; ?> orders
        </p>
    </div>

    <?php if ($orders_result->num_rows == 0): ?>
        <div class="bg-gray-900 p-12 text-center">
            <h3 class="text-xl font-playfair mb-4">You haven't placed any orders yet</h3>
            <p class="text-gray-400 mb-6">Start shopping and your orders will appear here.</p>
            <a href="index.php" class="btn btn-outline btn-white rounded-none">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="space-y-8">
            <?php while ($order = $orders_result->fetch_assoc()):
                // Get products for this order
                $products_query = "SELECT p.*, a.id_o FROM prodotti p 
                                  JOIN acquistare a ON p.id_p = a.id_p 
                                  JOIN brand b ON p.nome_b = b.nome_b
                                  WHERE a.id_o = ?";
                $prod_stmt = $conn->prepare($products_query);
                $prod_stmt->bind_param("i", $order['id_o']);
                $prod_stmt->execute();
                $products_result = $prod_stmt->get_result();

                // Calculate order total
                $order_total = 0;
                $products = [];
                while ($product = $products_result->fetch_assoc()) {
                    $products[] = $product;
                    $order_total += $product['prezzo'];
                }

                // Add shipping and tax
                $shipping = 10.00;
                $tax = $order_total * 0.22;
                $total = $order_total + $shipping + $tax;
                ?>
                <div class="bg-gray-900 p-6">
                    <div class="flex flex-col md:flex-row justify-between items-start mb-6 pb-4 border-b border-gray-800">
                        <div>
                            <h3 class="font-semibold">Order #<?php echo $order['id_o']; ?></h3>
                            <p class="text-sm text-gray-400">Placed on <?php echo date('F j, Y', strtotime($order['data_o'])); ?></p>
                        </div>
                        <div class="mt-4 md:mt-0 flex flex-col items-end">
                            <p class="font-semibold">€<?php echo number_format($total, 2); ?></p>
                            <p class="text-xs text-gray-400">Payment: <?php echo $order['metodo_pag']; ?></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between text-sm text-gray-400 mb-2">
                            <p>Shipping Address:</p>
                            <p><?php echo htmlspecialchars($order['indirizzo']); ?>, <?php echo htmlspecialchars($order['citta']); ?>, <?php echo htmlspecialchars($order['cap']); ?></p>
                        </div>

                        <div class="space-y-4">
                            <?php foreach ($products as $product): ?>
                                <div class="flex flex-col md:flex-row bg-gray-800 p-4">
                                    <div class="md:w-1/6 aspect-square">
                                        <img src="<?php echo htmlspecialchars($product['url_img']); ?>" alt="<?php echo htmlspecialchars($product['nome_p']); ?>" class="object-cover w-full h-full">
                                    </div>
                                    <div class="md:w-5/6 md:pl-6 pt-4 md:pt-0 flex flex-col justify-between">
                                        <div>
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h3 class="font-semibold"><?php echo htmlspecialchars($product['nome_p']); ?></h3>
                                                    <p class="text-sm text-gray-400"><?php echo htmlspecialchars($product['nome_b']); ?></p>
                                                    <p class="text-xs text-gray-500 mt-1">Size: <?php echo htmlspecialchars($product['taglia']); ?></p>
                                                </div>
                                                <p class="font-semibold">€<?php echo number_format($product['prezzo'], 2); ?></p>
                                            </div>
                                            <p class="text-sm text-gray-400 mt-2"><?php echo htmlspecialchars($product['descrizione']); ?></p>
                                        </div>
                                        <div class="flex justify-end items-center mt-4">
                                            <form method="post" action="add-to-cart.php" class="ml-2">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id_p']; ?>">
                                                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['nome_p']); ?>">
                                                <input type="hidden" name="price" value="<?php echo htmlspecialchars($product['prezzo']); ?>">
                                                <input type="hidden" name="image" value="<?php echo htmlspecialchars($product['url_img']); ?>">
                                                <input type="hidden" name="brand" value="<?php echo htmlspecialchars($product['nome_b']); ?>">
                                                <input type="hidden" name="description" value="<?php echo htmlspecialchars($product['descrizione']); ?>">
                                                <input type="hidden" name="size" value="<?php echo htmlspecialchars($product['taglia']); ?>">
                                                <button type="submit" name="add_to_cart" class="btn btn-sm btn-white rounded-none">
                                                    Buy Again
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-800">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm">
                                        <span class="text-gray-400">Subtotal:</span> €<?php echo number_format($order_total, 2); ?>
                                    </p>
                                    <p class="text-sm">
                                        <span class="text-gray-400">Shipping:</span> €<?php echo number_format($shipping, 2); ?>
                                    </p>
                                    <p class="text-sm">
                                        <span class="text-gray-400">Tax:</span> €<?php echo number_format($tax, 2); ?>
                                    </p>
                                    <p class="font-semibold mt-2">
                                        <span class="text-gray-400">Total:</span> €<?php echo number_format($total, 2); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="mt-8">
            <a href="Index.php" class="btn btn-outline btn-white rounded-none flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Continue Shopping
            </a>
        </div>
    <?php endif; ?>

    <!-- Recently Viewed Products -->
    <div class="mt-16">
        <h2 class="text-2xl font-playfair font-bold mb-6">Recently Viewed</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <?php
            // Get recently viewed products (using random for demonstration)
            $recent_query = "SELECT p.*, b.nome_b, s.nome_s 
                         FROM prodotti p 
                         JOIN brand b ON p.nome_b = b.nome_b 
                         JOIN stili s ON p.nome_s = s.nome_s 
                         ORDER BY RAND() LIMIT 4";

            $recent_result = $conn->query($recent_query);

            if ($recent_result && $recent_result->num_rows > 0) {
                while ($row = $recent_result->fetch_assoc()) {
                    ?>
                    <div class="group">
                        <div class="aspect-square overflow-hidden bg-gray-900 relative">
                            <img src="<?php echo htmlspecialchars($row['url_img']); ?>"
                                 alt="<?php echo htmlspecialchars($row['nome_p']); ?>"
                                 class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110">

                            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 p-4 transform translate-y-full transition-transform duration-300 group-hover:translate-y-0">
                                <div class="flex justify-between">
                                    <form method="post" action="add-to-wishlist.php">
                                        <input type="hidden" name="product_id" value="<?php echo $row['id_p']; ?>">
                                        <button type="submit" name="add_to_wishlist" class="btn btn-sm btn-ghost hover:bg-gray-800 rounded-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </button>
                                    </form>

                                    <form method="post" action="add-to-cart.php">
                                        <input type="hidden" name="product_id" value="<?php echo $row['id_p']; ?>">
                                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['nome_p']); ?>">
                                        <input type="hidden" name="price" value="<?php echo htmlspecialchars($row['prezzo']); ?>">
                                        <input type="hidden" name="image" value="<?php echo htmlspecialchars($row['url_img']); ?>">
                                        <input type="hidden" name="brand" value="<?php echo htmlspecialchars($row['nome_b']); ?>">
                                        <input type="hidden" name="description" value="<?php echo htmlspecialchars($row['descrizione']); ?>">
                                        <input type="hidden" name="size" value="<?php echo htmlspecialchars($row['taglia']); ?>">
                                        <button type="submit" name="add_to_cart" class="btn btn-sm btn-ghost hover:bg-gray-800 rounded-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 pb-2">
                            <div class="flex justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold"><?php echo htmlspecialchars($row['nome_p']); ?></h3>
                                    <p class="text-xs text-gray-400"><?php echo htmlspecialchars($row['nome_b']); ?></p>
                                </div>
                                <p class="font-semibold">€<?php echo htmlspecialchars($row['prezzo']); ?></p>
                            </div>

                            <div class="flex justify-between items-center mt-1">
                                <p class="text-xs text-gray-400 line-clamp-2"><?php echo htmlspecialchars($row['descrizione']); ?></p>
                                <span class="text-xs px-2 py-1 bg-gray-900 rounded">
                                    Size: <?php echo htmlspecialchars($row['taglia']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-black text-white border-t border-gray-800 py-12">
    <div class="container mx-auto px-8">
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
                        <input type="email" placeholder="Email address" class="input input-bordered rounded-none w-full max-w-xs bg-black border-gray-800">
                        <button class="btn btn-ghost hover:bg-gray-900 rounded-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
            <p>&copy; <?php echo date('Y'); ?> NoirStreet. All rights reserved.</p>
        </div>
    </div>
</footer>
</body>
</html>