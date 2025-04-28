<?php session_start(); ?>

<html>

<head>
    <meta charset="UTF-8">
    <title>NoirStreet | Streetwear meets Luxury</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Playfair+Display:wght@400;700&display=swap">
    <style>
        .font-montserrat { font-family: 'Montserrat', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .scroll-hidden::-webkit-scrollbar { display: none; }
        .hero-adidas {
            background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)),
            url('https://www.soccerbible.com/media/152818/adi-retro-feds-1-min.jpg');
            background-size: cover;
            background-position: center 30%;
        }
    </style>
</head>

<body class="font-montserrat bg-black text-white">
<?php include('nav.php'); ?>

<!-- Hero -->
<div class="hero-adidas hero min-h-[70vh] relative">
    <div class="hero-overlay bg-opacity-60"></div>
    <div class="hero-content text-center text-neutral-content p-8">
        <div class="max-w-4xl">
            <div class="mb-8">
                <span class="text-sm tracking-[0.5em] opacity-80">EXCLUSIVE COLLECTION</span>
            </div>
            <h1 class="font-playfair text-5xl md:text-7xl font-bold mb-6 leading-tight">
                <span class="text-primary">20% OFF</span> ON ADIDAS
            </h1>
            <p class="text-lg md:text-xl mb-8 font-light tracking-wider max-w-2xl mx-auto">
                Elevate your street style with our curated Adidas selection. Limited time offer on premium sneakers and apparel.
            </p>
            <button class="btn btn-primary rounded-none px-12 py-4 border-2 border-white bg-transparent hover:bg-white hover:text-black text-lg font-medium">
                SHOP THE DROP
            </button>
            <div class="mt-12 text-sm tracking-widest">
                <span>#ADIDASNOIRSTREET</span>
            </div>
        </div>
    </div>
</div>

<!-- Shop by Category -->
<section class="py-16 px-8 max-w-7xl mx-auto">
    <h2 class="font-playfair text-3xl md:text-4xl text-center mb-12">SHOP BY CATEGORY</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Tees -->
        <div class="group relative overflow-hidden h-80">
            <img src="https://images.unsplash.com/photo-1529374255404-311a2a4f1fd9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1559&q=80"
                 alt="Tees"
                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <div class="text-center p-6">
                    <h3 class="font-playfair text-3xl mb-4">TEES</h3>
                    <button class="btn btn-ghost rounded-none border border-white text-white hover:bg-white hover:text-black px-8">
                        <a href="Tees.php">SHOP NOW</a>
                    </button>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                <h3 class="font-medium text-xl">Premium Tees</h3>
                <p class="text-gray-400">From essential basics to limited editions</p>
            </div>
        </div>

        <!-- Hoodies -->
        <div class="group relative overflow-hidden h-80">
            <img src="https://media.gucci.com/style/DarkGray_Center_0_0_490x490/1715878088/756649_XJFV9_9088_005_100_0000_Light-Cotton-jersey-hooded-sweatshirt.jpg"
                 alt="Hoodies"
                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <div class="text-center p-6">
                    <h3 class="font-playfair text-3xl mb-4">HOODIES</h3>
                    <button class="btn btn-ghost rounded-none border border-white text-white hover:bg-white hover:text-black px-8">
                        <a href="Hoodies.php">SHOP NOW</a>
                    </button>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                <h3 class="font-medium text-xl">Luxury Hoodies</h3>
                <p class="text-gray-400">Premium fabrics & exclusive designs</p>
            </div>
        </div>

        <!-- Shoes -->
        <div class="group relative overflow-hidden h-80">
            <img src="https://data2.nssmag.com/cdn-cgi/image/fit=crop,width=2560,height=2560/images/galleries/30502/NewBalancexMiuMiu-031.jpg"
                 alt="Shoes"
                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <div class="text-center p-6">
                    <h3 class="font-playfair text-3xl mb-4">SNEAKERS</h3>
                    <button class="btn btn-ghost rounded-none border border-white text-white hover:bg-white hover:text-black px-8">
                        <a href="Shoes.php">SHOP NOW</a>
                    </button>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                <h3 class="font-medium text-xl">Exclusive Kicks</h3>
                <p class="text-gray-400">Rare collabs & limited editions</p>
            </div>
        </div>

        <!-- Accessories -->
        <div class="group relative overflow-hidden h-80">
            <img src="https://i.pinimg.com/736x/5f/3d/92/5f3d92f9dbfcd1257ebd34256f0f1fb1.jpg"
                 alt="Accessories"
                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <div class="text-center p-6">
                    <h3 class="font-playfair text-3xl mb-4">ACCESSORIES</h3>
                    <button class="btn btn-ghost rounded-none border border-white text-white hover:bg-white hover:text-black px-8">
                        <a href="Accessories.php">SHOP NOW</a>
                    </button>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                <h3 class="font-medium text-xl">Statement Pieces</h3>
                <p class="text-gray-400">Hats, bags, jewelry & more</p>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="py-20 border-t border-gray-800">
    <div class="max-w-4xl mx-auto px-8 text-center">
        <h2 class="font-playfair text-3xl mb-6">JOIN THE NOIRSTREET</h2>
        <p class="mb-8 text-gray-400 max-w-2xl mx-auto">
            Subscribe to our newsletter for exclusive drops, early access to new collections, and special members-only offers.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center max-w-md mx-auto">
            <input type="email" placeholder="Your email address" class="input input-bordered rounded-none bg-transparent border-gray-700 w-full">
            <button class="btn btn-primary rounded-none px-8 border border-white bg-transparent hover:bg-white hover:text-black">
                <a href="Sign-Up.php">SUBSCRIBE</a>
            </button>
        </div>
    </div>
</section>

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