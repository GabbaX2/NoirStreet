<?php
// nav.php
// Updated navigation with cart count indicator

// Include cart functions if not already included
if (!function_exists('getCartCount')) {
    include('cart-functions.php');
}

// Get cart count
$cartCount = getCartCount();
?>

<nav class="bg-black text-white border-b border-gray-800 fixed w-full top-0 z-50">
    <div class="container mx-auto px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <a href="Index.php" class="font-playfair text-xl font-semibold">NoirStreet.</a>

            <!-- Main Navigation -->
            <div class="hidden md:flex space-x-8">
                <a href="Index.php" class="text-sm hover:text-gray-300 transition">Home</a>
                <a href="Tees.php" class="text-sm hover:text-gray-300 transition">Tees</a>
                <a href="Hoodies.php" class="text-sm hover:text-gray-300 transition">Hoodies</a>
                <a href="Shoes.php" class="text-sm hover:text-gray-300 transition">Shoes</a>
                <a href="Pants-Skirts.php" class="text-sm hover:text-gray-300 transition">Pants & Skirts</a>
                <a href="Jackets.php" class="text-sm hover:text-gray-300 transition">Jackets</a>
                <a href="Accessories.php" class="text-sm hover:text-gray-300 transition">Accessories</a>
                <a href="Dashboard/Dashboard.php" class="text-sm hover:text-gray-300 transition">Admin Area</a>
            </div>

            <!-- Right side icons -->
            <div class="flex items-center space-x-6">
                <!-- Search -->
                <button class="hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>

                <!-- Wishlist -->
                <a href="Wishlist.php" class="hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </a>

                <!-- Cart with Counter -->
                <a href="Cart.php" class="hover:text-gray-300 relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <?php if ($cartCount > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-white text-black text-xs rounded-full h-4 w-4 flex items-center justify-center font-semibold"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                </a>

                <!-- User Account (unchanged) -->
                <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="hover:text-gray-300 cursor-pointer flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-xs"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        </div>
                        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-black border border-gray-800 w-52">
                            <li><a href="Profile.php" class="text-sm hover:bg-gray-900 py-2">My Profile</a></li>
                            <li><a href="Orders.php" class="text-sm hover:bg-gray-900 py-2">My Orders</a></li>
                            <li><a href="Wishlist.php" class="text-sm hover:bg-gray-900 py-2">Wishlist</a></li>
                            <li class="border-t border-gray-800 mt-1 pt-1">
                                <a href="user-logout.php" class="text-sm hover:bg-gray-900 py-2">Logout</a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="Sign-In.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="hover:text-gray-300 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="ml-1 text-xs hidden md:inline">Login</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>