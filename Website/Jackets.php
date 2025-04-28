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

$conn->set_charset("utf8");

// Recupero dei parametri di filtro dalla URL
$brand_filter = isset($_GET['brand']) ? $_GET['brand'] : '';
$style_filter = isset($_GET['style']) ? $_GET['style'] : '';
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'price_asc';

// Query per ottenere i prodotti di categoria "Giacche"
$query = "SELECT p.*, b.nome_b, s.nome_s 
          FROM prodotti p 
          JOIN brand b ON p.nome_b = b.nome_b 
          JOIN stili s ON p.nome_s = s.nome_s 
          WHERE p.nome_cat = 'Giacche'";

// Aggiungi filtri se selezionati
if (!empty($brand_filter) && $brand_filter != 'all') {
    $query .= " AND p.nome_b = '" . $conn->real_escape_string($brand_filter) . "'";
}

if (!empty($style_filter) && $style_filter != 'all') {
    $query .= " AND p.nome_s = '" . $conn->real_escape_string($style_filter) . "'";
}

// Aggiungi ordinamento
switch ($sort_option) {
    case 'price_asc':
        $query .= " ORDER BY p.prezzo ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY p.prezzo DESC";
        break;
    case 'newest':
        $query .= " ORDER BY p.id_p DESC"; // Assumendo che ID più alti siano prodotti più recenti
        break;
    case 'popular':
        // Se hai un campo per la popolarità, usalo qui
        $query .= " ORDER BY p.views DESC"; // Esempio: se hai un campo 'views'
        break;
    default:
        $query .= " ORDER BY p.prezzo ASC";
}

$result = $conn->query($query);
if (!$result) {
    die("Query fallita: " . $conn->error);
}

// Query per ottenere tutti i brand (per i filtri)
$brandQuery = "SELECT DISTINCT nome_b FROM brand ORDER BY nome_b";
$brandResult = $conn->query($brandQuery);

// Query per ottenere tutti gli stili (per i filtri)
$styleQuery = "SELECT DISTINCT nome_s FROM stili ORDER BY nome_s";
$styleResult = $conn->query($styleQuery);

// Gestione dell'invio delle recensioni
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_review'])) {
    // Controllo se l'utente è loggato
    if (!isset($_SESSION['user_id'])) {
        $review_error = "Devi effettuare il login per lasciare una recensione.";
    } else {
        $user_id = $_SESSION['user_id'];
        $product_id = $_POST['product_id'];
        $comment = $conn->real_escape_string($_POST['comment']);
        $rating = $conn->real_escape_string($_POST['rating']);
        $current_date = date('Y-m-d');

        // Recupera l'ultimo ID recensione per creare un nuovo ID
        $lastIdQuery = "SELECT MAX(id_r) as last_id FROM recensioni";
        $lastIdResult = $conn->query($lastIdQuery);
        $lastIdRow = $lastIdResult->fetch_assoc();
        $new_review_id = $lastIdRow['last_id'] + 1;

        // Inserisci la recensione nella tabella recensioni
        $insertReviewQuery = "INSERT INTO recensioni (id_r, id_u, commento, rating, data_r) 
                             VALUES ($new_review_id, $user_id, '$comment', '$rating', '$current_date')";

        if ($conn->query($insertReviewQuery) === TRUE) {
            // Inserisci nella tabella di relazione giudicare
            $insertRelationQuery = "INSERT INTO giudicare (id_r, id_p) VALUES ($new_review_id, $product_id)";

            if ($conn->query($insertRelationQuery) === TRUE) {
                $review_success = "Recensione inviata con successo!";
            } else {
                $review_error = "Errore nell'invio della recensione: " . $conn->error;
            }
        } else {
            $review_error = "Errore nell'invio della recensione: " . $conn->error;
        }
    }
}
?>

    <html>
    <head>
        <meta charset="UTF-8">
        <title>NoirStreet | Jackets Collection</title>
        <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Playfair+Display:wght@400;700&display=swap">
    </head>

    <body class="font-montserrat bg-black text-white">
    <?php include("nav.php"); ?>

    <!-- Banner della categoria -->
    <div class="relative h-96 bg-black text-white overflow-hidden">
        <!-- Overlay pattern -->
        <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2256%22%20height%3D%2256%22%3E%3Cpath%20fill%3D%22%23fff%22%20d%3D%22M8%2016h8v8H8zm16%200h8v8h-8zm16%200h8v8h-8zM0%2032h8v8H0zm16%200h8v8h-8zm16%200h8v8h-8zm16%200h8v8h-8zM8%2048h8v8H8zm16%200h8v8h-8zm16%200h8v8h-8z%22%2F%3E%3C%2Fsvg%3E')]"></div>

        <!-- Left side decorative element -->
        <div class="absolute -left-20 top-0 bottom-0 w-40 bg-gradient-to-r from-gray-100 to-transparent opacity-5"></div>

        <!-- Right side decorative element -->
        <div class="absolute -right-20 top-0 bottom-0 w-40 bg-gradient-to-l from-gray-100 to-transparent opacity-5"></div>

        <!-- Main content container -->
        <div class="container mx-auto h-full flex flex-col justify-center items-center px-4 relative">
            <!-- Small decorative text -->
            <div class="absolute top-12 left-8 md:left-12">
                <p class="text-xs tracking-widest opacity-50 font-light">NOIR·STREET</p>
            </div>

            <!-- Main heading with decorative line -->
            <div class="flex flex-col items-center">
                <div class="w-12 h-px bg-gray-400 mb-6"></div>
                <h1 class="text-7xl md:text-8xl font-playfair font-bold tracking-tighter">JACKETS</h1>
                <div class="w-24 h-px bg-gray-400 mt-6"></div>
            </div>

            <!-- Bottom content with season and tagline -->
            <div class="absolute bottom-16 md:bottom-12 flex flex-col items-center">
                <p class="text-xs tracking-widest opacity-50 font-light mb-2">FALL/WINTER 2025</p>
                <p class="text-lg md:text-xl font-light italic">"Style that withstands the elements."</p>
            </div>

            <!-- Bottom right decorative element -->
            <div class="absolute bottom-6 right-8 md:right-12">
                <div class="flex items-center">
                    <div class="w-8 h-px bg-gray-400 mr-2"></div>
                    <p class="text-xs tracking-widest opacity-70">COLLECTION</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtri -->
    <form id="filter-form" method="GET" action="" class="flex justify-between items-center px-8 py-6 border-b border-gray-800">
        <div class="flex space-x-4">
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-ghost hover:bg-gray-900 rounded-none px-4">
                    Brand
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-black border border-gray-800 rounded-none w-52">
                    <li><a onclick="setFilter('brand', 'all')" class="hover:bg-gray-900 <?php echo empty($brand_filter) || $brand_filter == 'all' ? 'bg-gray-800' : ''; ?>">All Brands</a></li>
                    <?php
                    while ($brandRow = $brandResult->fetch_assoc()) {
                        $brandName = htmlspecialchars($brandRow['nome_b']);
                        $isSelected = $brand_filter == $brandName ? 'bg-gray-800' : '';
                        echo '<li><a onclick="setFilter(\'brand\', \'' . $brandName . '\')" class="hover:bg-gray-900 ' . $isSelected . '">' . $brandName . '</a></li>';
                    }
                    ?>
                </ul>
            </div>

            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-ghost hover:bg-gray-900 rounded-none px-4">
                    Style
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-black border border-gray-800 rounded-none w-52">
                    <li><a onclick="setFilter('style', 'all')" class="hover:bg-gray-900 <?php echo empty($style_filter) || $style_filter == 'all' ? 'bg-gray-800' : ''; ?>">All Styles</a></li>
                    <?php
                    while ($styleRow = $styleResult->fetch_assoc()) {
                        $styleName = htmlspecialchars($styleRow['nome_s']);
                        $isSelected = $style_filter == $styleName ? 'bg-gray-800' : '';
                        echo '<li><a onclick="setFilter(\'style\', \'' . $styleName . '\')" class="hover:bg-gray-900 ' . $isSelected . '">' . $styleName . '</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>

        <div>
            <select name="sort" class="select select-bordered select-sm rounded-none bg-black border-gray-800 focus:border-white" onchange="document.getElementById('filter-form').submit()">
                <option value="price_asc" <?php echo $sort_option == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price_desc" <?php echo $sort_option == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                <option value="newest" <?php echo $sort_option == 'newest' ? 'selected' : ''; ?>>Newest First</option>
            </select>
        </div>

        <!-- Hidden inputs per i filtri selezionati -->
        <input type="hidden" id="brand-filter" name="brand" value="<?php echo htmlspecialchars($brand_filter); ?>">
        <input type="hidden" id="style-filter" name="style" value="<?php echo htmlspecialchars($style_filter); ?>">
    </form>

    <script>
        function setFilter(type, value) {
            // Imposta il valore del filtro selezionato
            document.getElementById(type + '-filter').value = value;
            // Invia il form per applicare i filtri
            document.getElementById('filter-form').submit();
        }

        // Funzione per aprire il modal di recensione
        function openReviewModal(productId, productName) {
            document.getElementById('review-product-id').value = productId;
            document.getElementById('review-product-name').textContent = productName;
            document.getElementById('review-modal').classList.remove('hidden');
        }

        // Funzione per chiudere il modal di recensione
        function closeReviewModal() {
            document.getElementById('review-modal').classList.add('hidden');
        }

        // Funzione per gestire la selezione del rating
        function setRating(rating) {
            document.getElementById('rating-input').value = rating;

            // Aggiorna l'aspetto visivo delle stelle
            for (let i = 1; i <= 5; i++) {
                const star = document.getElementById('star-' + i);
                if (i <= rating) {
                    star.classList.add('text-yellow-400');
                    star.classList.remove('text-gray-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-400');
                }
            }
        }
    </script>


    <!-- Griglia prodotti -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-8 prodotti-container" style="margin-top: 80px; z-index: 50;">
        <?php
        // Verify that query results are available
        if ($result && $result->num_rows > 0) {
            // Loop through product results
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="group">
                    <div class="aspect-square overflow-hidden bg-gray-900 relative">
                        <img src="<?php echo htmlspecialchars($row['url_img']); ?>"
                             alt="<?php echo htmlspecialchars($row['nome_p']); ?>"
                             class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110">

                        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 p-4 transform translate-y-full transition-transform duration-300 group-hover:translate-y-0">
                            <div class="flex justify-between">
                                <!-- Wishlist button implementation (to be added later) -->
                                <form method="post" action="add-to-wishlist.php">
                                    <input type="hidden" name="product_id" value="<?php echo $row['id_p']; ?>">
                                    <button type="submit" name="add_to_wishlist" class="btn btn-sm btn-ghost hover:bg-gray-800 rounded-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                </form>

                                <!-- Add to cart form -->
                                <form method="post" action="add-to-cart.php">
                                    <input type="hidden" name="product_id" value="<?php echo $row['id_p']; ?>">
                                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['nome_p'] ?? ''); ?>">
                                    <input type="hidden" name="price" value="<?php echo htmlspecialchars($row['prezzo'] ?? ''); ?>">
                                    <input type="hidden" name="image" value="<?php echo htmlspecialchars($row['url_img'] ?? ''); ?>">
                                    <input type="hidden" name="brand" value="<?php echo htmlspecialchars($row['nome_b'] ?? ''); ?>">
                                    <input type="hidden" name="size" value="<?php echo htmlspecialchars($row['taglia'] ?? ''); ?>">
                                    <button type="submit" name="add_to_cart" class="btn btn-sm btn-ghost hover:bg-gray-800 rounded-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </button>
                                </form>

                                <!-- Review button -->
                                <button onclick="openReviewModal(<?php echo $row['id_p']; ?>, '<?php echo htmlspecialchars($row['nome_p']); ?>')" class="btn btn-sm btn-ghost hover:bg-gray-800 rounded-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                </button>
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
                            <div class="flex flex-col items-end gap-1">
                            <span class="text-xs px-2 py-1 bg-gray-900 rounded">
                                Size: <?php echo htmlspecialchars($row['taglia']); ?>
                            </span>
                            </div>
                        </div>

                        <!-- Visualizzazione recensioni esistenti -->
                        <?php
                        // Query per ottenere il rating medio del prodotto
                        $avgRatingQuery = "SELECT AVG(r.rating) as avg_rating, COUNT(r.id_r) as review_count 
                                      FROM recensioni r 
                                      JOIN giudicare g ON r.id_r = g.id_r 
                                      WHERE g.id_p = " . $row['id_p'];
                        $avgRatingResult = $conn->query($avgRatingQuery);

                        if ($avgRatingResult && $avgRatingRow = $avgRatingResult->fetch_assoc()) {
                            $avgRating = ($avgRatingRow['avg_rating'] !== NULL) ? round($avgRatingRow['avg_rating'], 1) : 0;
                            $reviewCount = $avgRatingRow['review_count'];

                            if ($reviewCount > 0) {
                                ?>
                                <div class="mt-2 flex items-center">
                                    <div class="flex">
                                        <?php
                                        // Visualizza stelle piene e vuote in base al rating medio
                                        $fullStars = floor($avgRating);
                                        $hasHalfStar = ($avgRating - $fullStars) >= 0.5;

                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $fullStars) {
                                                // Stella piena
                                                echo '<svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>';
                                            } elseif ($i == $fullStars + 1 && $hasHalfStar) {
                                                // Mezza stella
                                                echo '<svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <defs>
                                                    <linearGradient id="halfGradient">
                                                      <stop offset="50%" stop-color="currentColor" />
                                                      <stop offset="50%" stop-color="#D1D5DB" />
                                                    </linearGradient>
                                                  </defs>
                                                  <path fill="url(#halfGradient)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>';
                                            } else {
                                                // Stella vuota
                                                echo '<svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <span class="text-xs text-gray-400 ml-1"><?php echo $avgRating; ?> (<?php echo $reviewCount; ?>)</span>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="col-span-full text-center py-16">
            <h3 class="text-2xl font-playfair">No products found</h3>
            <p class="text-gray-400 mt-2">Check back soon for new arrivals.</p>
        </div>';
        }
        ?>
    </div>

    <!-- Modal per recensioni -->
    <div id="review-modal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 hidden">
        <div class="bg-gray-900 p-6 max-w-md w-full mx-4 border border-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold font-playfair">Recensisci il prodotto</h3>
                <button onclick="closeReviewModal()" class="text-gray-400 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <p class="text-sm text-gray-400 mb-4">Stai recensendo: <span id="review-product-name" class="text-white"></span></p>

            <form method="post" action="">
                <input type="hidden" id="review-product-id" name="product_id">
                <input type="hidden" id="rating-input" name="rating" value="5">

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Valutazione</label>
                    <div class="flex space-x-1">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <button type="button" onclick="setRating(<?php echo $i; ?>)" class="focus:outline-none">
                                <svg id="star-<?php echo $i; ?>" class="h-6 w-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </button>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="comment" class="block text-sm font-medium mb-2">Commento</label>
                    <textarea id="comment" name="comment" rows="4" maxlength="200" class="textarea textarea-bordered bg-black border-gray-700 w-full focus:border-white" placeholder="Scrivi la tua recensione qui (max 200 caratteri)" required></textarea>
                    <div class="text-xs text-gray-400 mt-1 flex justify-end">
                        <span id="char-count">0</span>/200
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="button" onclick="closeReviewModal()" class="btn btn-ghost hover:bg-gray-800 rounded-none mr-2">Annulla</button>
                    <button type="submit" name="submit_review" class="btn bg-white text-black hover:bg-gray-200 rounded-none">Invia Recensione</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        document.getElementById('comment').addEventListener('input', function() {
            const maxLength = 200;
            const currentLength = this.value.length;
            document.getElementById('char-count').textContent = currentLength;

            if (currentLength > maxLength) {
                this.value = this.value.substring(0, maxLength);
                document.getElementById('char-count').textContent = maxLength;
            }
        });
    </script>

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
                        <li><a href="Jackets.php" class="hover:text-white">Jackets</a></li>
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
<?php
$conn->close();
?>