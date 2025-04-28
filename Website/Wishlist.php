<?php
session_start();

// Database connection
$conn = new mysqli("localhost:8080", "root", "", "streetnoirdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Check if user is logged in
$user_logged_in = isset($_SESSION['user_id']);
$user_id = $user_logged_in ? $_SESSION['user_id'] : null;

// Handle remove from wishlist
if (isset($_POST['remove_from_wishlist']) && isset($_POST['wishlist_id']) && $user_logged_in) {
    $wishlist_id = $_POST['wishlist_id'];

    // Delete the wishlist item
    $delete_query = "DELETE FROM wishlist WHERE id_w = ? AND id_u = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("ii", $wishlist_id, $user_id);
    $stmt->execute();

    // Redirect to avoid form resubmission
    header('Location: wishlist.php');
    exit;
}

// Handle move to cart
if (isset($_POST['move_to_cart']) && isset($_POST['product_id']) && isset($_POST['wishlist_id'])) {
    $product_id = $_POST['product_id'];
    $wishlist_id = $_POST['wishlist_id'];

    // Get product details
    $product_query = "SELECT p.*, b.nome_b, s.nome_s 
                      FROM prodotti p 
                      JOIN brand b ON p.nome_b = b.nome_b 
                      JOIN stili s ON p.nome_s = s.nome_s 
                      WHERE p.id_p = ?";

    $stmt = $conn->prepare($product_query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();

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
                'nome_p' => $product['nome_p'],
                'prezzo' => $product['prezzo'],
                'url_img' => $product['url_img'],
                'nome_b' => $product['nome_b'],
                'descrizione' => $product['descrizione'],
                'taglia' => $product['taglia'],
                'quantity' => 1
            );

            $_SESSION['cart'][] = $cart_item;
        }

        // If user is logged in, remove from wishlist
        if ($user_logged_in) {
            $delete_query = "DELETE FROM wishlist WHERE id_w = ? AND id_u = ?";
            $stmt = $conn->prepare($delete_query);
            $stmt->bind_param("ii", $wishlist_id, $user_id);
            $stmt->execute();
        }
    }

    // Redirect to cart
    header('Location: cart.php');
    exit;
}

// Get wishlist items from database if user is logged in
$wishlist_items = array();

if ($user_logged_in) {
    $wishlist_query = "SELECT w.id_w, p.*, b.nome_b, s.nome_s 
                      FROM wishlist w
                      JOIN prodotti p ON w.id_p = p.id_p
                      JOIN brand b ON p.nome_b = b.nome_b
                      JOIN stili s ON p.nome_s = s.nome_s
                      WHERE w.id_u = ?
                      ORDER BY w.data_w DESC";

    $stmt = $conn->prepare($wishlist_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $wishlist_items[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>NoirStreet | Your Wishlist</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Playfair+Display:wght@400;700&display=swap">
</head>

<body class="font-montserrat bg-black text-white">
<?php include("nav.php"); ?>

<!-- Banner della categoria -->
<div class="relative h-64 bg-black text-white overflow-hidden">
    <!-- Overlay pattern -->
    <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2256%22%20height%3D%2256%22%3E%3Cpath%20fill%3D%22%23fff%22%20d%3D%22M8%2016h8v8H8zm16%200h8v8h-8zm16%200h8v8h-8zM0%2032h8v8H0zm16%200h8v8h-8zm16%200h8v8h-8zm16%200h8v8h-8zM8%2048h8v8H8zm16%200h8v8h-8zm16%200h8v8h-8z%22%2F%3E%3C%2Fsvg%3E')]"></div>

    <!-- Left side decorative element -->
    <div class="absolute -left-20 top-0 bottom-0 w-40 bg-gradient-to-r from-gray-100 to-transparent opacity-5"></div>

    <!-- Right side decorative element -->
    <div class="absolute -right-20 top-0 bottom-0 w-40 bg-gradient-to-l from-gray-100 to-transparent opacity-5"></div>

    <!-- Main content container -->
    <div class="container mx-auto h-full flex flex-col justify-center items-center px-4 relative">
        <!-- Small decorative text -->
        <div class="absolute top-6 left-8 md:left-12">
            <p class="text-xs tracking-widest opacity-50 font-light">NOIR·STREET</p>
        </div>

        <!-- Main heading with decorative line -->
        <div class="flex flex-col items-center">
            <div class="w-12 h-px bg-gray-400 mb-6"></div>
            <h1 class="text-4xl md:text-5xl font-playfair font-bold tracking-tighter">WISHLIST</h1>
            <div class="w-24 h-px bg-gray-400 mt-6"></div>
        </div>

        <!-- Bottom right decorative element -->
        <div class="absolute bottom-6 right-8 md:right-12">
            <div class="flex items-center">
                <div class="w-8 h-px bg-gray-400 mr-2"></div>
                <p class="text-xs tracking-widest opacity-70">FAVORITES</p>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-8 py-12">
    <?php if (!$user_logged_in): ?>
        <!-- User not logged in message -->
        <div class="bg-gray-900 p-12 text-center">
            <h3 class="text-xl font-playfair mb-4">Please log in to view your wishlist</h3>
            <p class="text-gray-400 mb-6">Create an account or log in to save your favorite items.</p>
            <div class="flex justify-center space-x-4">
                <a href="login.php" class="btn btn-white rounded-none">Log In</a>
                <a href="register.php" class="btn btn-outline btn-white rounded-none">Register</a>
            </div>
        </div>
    <?php elseif (empty($wishlist_items)): ?>
        <!-- Empty wishlist message -->
        <div class="bg-gray-900 p-12 text-center">
            <h3 class="text-xl font-playfair mb-4">Your wishlist is empty</h3>
            <p class="text-gray-400 mb-6">Add items to your wishlist while browsing our collections.</p>
            <a href="index.php" class="btn btn-outline btn-white rounded-none">Start Shopping</a>
        </div>
    <?php else: ?>
        <!-- Wishlist items grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($wishlist_items as $item): ?>
                <div class="group">
                    <div class="aspect-square overflow-hidden bg-gray-900 relative">
                        <img src="<?php echo htmlspecialchars($item['url_img']); ?>"
                             alt="<?php echo htmlspecialchars($item['nome_p']); ?>"
                             class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110">

                        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 p-4 transform translate-y-full transition-transform duration-300 group-hover:translate-y-0">
                            <div class="flex justify-between">
                                <!-- Remove from wishlist form -->
                                <form method="post" action="wishlist.php">
                                    <input type="hidden" name="wishlist_id" value="<?php echo $item['id_w']; ?>">
                                    <button type="submit" name="remove_from_wishlist" class="btn btn-sm btn-ghost hover:bg-gray-800 rounded-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>

                                <!-- Move to cart form -->
                                <form method="post" action="wishlist.php">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id_p']; ?>">
                                    <input type="hidden" name="wishlist_id" value="<?php echo $item['id_w']; ?>">
                                    <button type="submit" name="move_to_cart" class="btn btn-sm btn-ghost hover:bg-gray-800 rounded-none">
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
                                <h3 class="text-sm font-semibold"><?php echo htmlspecialchars($item['nome_p']); ?></h3>
                                <p class="text-xs text-gray-400"><?php echo htmlspecialchars($item['nome_b']); ?></p>
                            </div>
                            <p class="font-semibold">€<?php echo htmlspecialchars($item['prezzo']); ?></p>
                        </div>

                        <div class="flex justify-between items-center mt-1">
                            <p class="text-xs text-gray-400 line-clamp-2"><?php echo htmlspecialchars($item['descrizione']); ?></p>
                            <span class="text-xs px-2 py-1 bg-gray-900 rounded">
                                Size: <?php echo htmlspecialchars($item['taglia']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
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
                    <li><a href="Scarpe.php" class="hover:text-white">Shoes</a></li>
                    <li><a href="Accessori.php" class="hover:text-white">Accessories</a></li>
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