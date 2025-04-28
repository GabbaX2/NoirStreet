<?php
// Inizializzazione della sessione
session_start();

// Verifica se l'admin è loggato
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-auth.php");
    exit();
}

// Connessione al database
$host = 'localhost:8080';
$dbname = 'streetnoirdb';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Gestione delle operazioni
$message = '';
$messageType = '';


// Aggiunta prodotto
if (isset($_POST['add_product'])) {
    $nome_p = $conn->real_escape_string($_POST['nome_p']);
    $descrizione = $conn->real_escape_string($_POST['descrizione']);
    $prezzo = $conn->real_escape_string($_POST['prezzo']);
    $url_img = $conn->real_escape_string($_POST['url_img']);
    $taglia = $conn->real_escape_string($_POST['taglia']);
    $nome_b = $conn->real_escape_string($_POST['nome_b']);
    $nome_cat = $conn->real_escape_string($_POST['nome_cat']);
    $nome_s = $conn->real_escape_string($_POST['nome_s']);

    // Generare un nuovo ID per il prodotto
    $result = $conn->query("SELECT MAX(id_p) as max_id FROM prodotti");
    $row = $result->fetch_assoc();
    $new_id = $row['max_id'] + 1;

    $sql = "INSERT INTO prodotti (id_p, nome_p, descrizione, prezzo, url_img, taglia, nome_b, nome_cat, nome_s)
            VALUES ('$new_id', '$nome_p', '$descrizione', '$prezzo', '$url_img', '$taglia', '$nome_b', '$nome_cat', '$nome_s')";

    if ($conn->query($sql) === TRUE) {
        $message = "Prodotto aggiunto con successo!";
        $messageType = "success";
    } else {
        $message = "Errore nell'aggiunta del prodotto: " . $conn->error;
        $messageType = "error";
    }
}

// Eliminazione prodotto
if (isset($_POST['delete_product'])) {
    $id_p = $conn->real_escape_string($_POST['id_p']);

    // Verificare se il prodotto è presente in tabelle correlate
    $tables = ["wishlist", "carrelli", "inventari", "inserire", "giudicare", "acquistare"];
    $canDelete = true;

    foreach ($tables as $table) {
        $checkQuery = "";
        if ($table == "wishlist" || $table == "carrelli" || $table == "inventari") {
            $checkQuery = "SELECT COUNT(*) as count FROM $table WHERE id_p = '$id_p'";
        } else {
            $checkQuery = "SELECT COUNT(*) as count FROM $table WHERE id_p = '$id_p'";
        }

        $result = $conn->query($checkQuery);
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            $canDelete = false;
            $message = "Impossibile eliminare il prodotto perché è referenziato in altre tabelle.";
            $messageType = "error";
            break;
        }
    }

    if ($canDelete) {
        $sql = "DELETE FROM prodotti WHERE id_p = '$id_p'";
        if ($conn->query($sql) === TRUE) {
            $message = "Prodotto eliminato con successo!";
            $messageType = "success";
        } else {
            $message = "Errore nell'eliminazione del prodotto: " . $conn->error;
            $messageType = "error";
        }
    }
}

// Aggiornamento inventario
if (isset($_POST['update_inventory'])) {
    $id_p = $conn->real_escape_string($_POST['id_p']);
    $id_f = $conn->real_escape_string($_POST['id_f']);
    $admin_id = $_SESSION['admin_id']; // ID dell'admin loggato
    $data_rif = date('Y-m-d'); // Data odierna

    // Generare un nuovo ID per l'inventario
    $result = $conn->query("SELECT MAX(id_i) as max_id FROM inventari");
    $row = $result->fetch_assoc();
    $new_id = $row['max_id'] + 1;

    $sql = "INSERT INTO inventari (id_i, id_p, data_rif, id_a, id_f)
            VALUES ('$new_id', '$id_p', '$data_rif', '$admin_id', '$id_f')";

    if ($conn->query($sql) === TRUE) {
        $message = "Inventario aggiornato con successo!";
        $messageType = "success";
    } else {
        $message = "Errore nell'aggiornamento dell'inventario: " . $conn->error;
        $messageType = "error";
    }
}

// Aggiunta brand
if (isset($_POST['add_brand'])) {
    $nome_b = $conn->real_escape_string($_POST['nome_b']);

    $sql = "INSERT INTO brand (nome_b) VALUES ('$nome_b')";

    if ($conn->query($sql) === TRUE) {
        $message = "Brand aggiunto con successo!";
        $messageType = "success";
    } else {
        $message = "Errore nell'aggiunta del brand: " . $conn->error;
        $messageType = "error";
    }
}

// Aggiunta categoria
if (isset($_POST['add_category'])) {
    $nome_cat = $conn->real_escape_string($_POST['nome_cat']);

    $sql = "INSERT INTO categorie (nome_cat) VALUES ('$nome_cat')";

    if ($conn->query($sql) === TRUE) {
        $message = "Categoria aggiunta con successo!";
        $messageType = "success";
    } else {
        $message = "Errore nell'aggiunta della categoria: " . $conn->error;
        $messageType = "error";
    }
}

// Aggiunta stile
if (isset($_POST['add_style'])) {
    $nome_s = $conn->real_escape_string($_POST['nome_s']);

    $sql = "INSERT INTO stili (nome_s) VALUES ('$nome_s')";

    if ($conn->query($sql) === TRUE) {
        $message = "Stile aggiunto con successo!";
        $messageType = "success";
    } else {
        $message = "Errore nell'aggiunta dello stile: " . $conn->error;
        $messageType = "error";
    }
}

// Aggiunta fornitore
if (isset($_POST['add_supplier'])) {
    $nome = $conn->real_escape_string($_POST['nome']);
    $indirizzo = $conn->real_escape_string($_POST['indirizzo']);
    $citta = $conn->real_escape_string($_POST['citta']);
    $mail = $conn->real_escape_string($_POST['mail']);
    $cap = $conn->real_escape_string($_POST['cap']);

    // Generare un nuovo ID per il fornitore
    $result = $conn->query("SELECT MAX(id_f) as max_id FROM fornitori");
    $row = $result->fetch_assoc();
    $new_id = $row['max_id'] + 1;

    $sql = "INSERT INTO fornitori (id_f, nome, indirizzo, citta, mail, cap)
            VALUES ('$new_id', '$nome', '$indirizzo', '$citta', '$mail', '$cap')";

    if ($conn->query($sql) === TRUE) {
        $message = "Fornitore aggiunto con successo!";
        $messageType = "success";
    } else {
        $message = "Errore nell'aggiunta del fornitore: " . $conn->error;
        $messageType = "error";
    }
}

// Aggiunta admin
if (isset($_POST['add_admin'])) {
    $admin_username = $conn->real_escape_string($_POST['admin_username']);
    $admin_password = $_POST['admin_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificare che l'username non esista già
    $check_username = $conn->query("SELECT COUNT(*) as count FROM admin WHERE username = '$admin_username'");
    $row = $check_username->fetch_assoc();

    if ($row['count'] > 0) {
        $message = "Username già in uso, scegline un altro.";
        $messageType = "error";
    } else if ($admin_password !== $confirm_password) {
        $message = "Le password non coincidono.";
        $messageType = "error";
    } else {
        // Generare un nuovo ID per l'admin
        $result = $conn->query("SELECT MAX(id_a) as max_id FROM admin");
        $row = $result->fetch_assoc();
        $new_id = $row['max_id'] + 1;

        // Hash della password (meglio usare password_hash in produzione)
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO admin (id_a, username, password) 
                VALUES ('$new_id', '$admin_username', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            $message = "Admin creato con successo!";
            $messageType = "success";
        } else {
            $message = "Errore nella creazione dell'admin: " . $conn->error;
            $messageType = "error";
        }
    }
}

// Query per recuperare i dati per i dropdown
$brands = $conn->query("SELECT nome_b FROM brand ORDER BY nome_b");
$categories = $conn->query("SELECT nome_cat FROM categorie ORDER BY nome_cat");
$styles = $conn->query("SELECT nome_s FROM stili ORDER BY nome_s");
$products = $conn->query("SELECT id_p, nome_p FROM prodotti ORDER BY nome_p");
$suppliers = $conn->query("SELECT id_f, nome FROM fornitori ORDER BY nome");
?>

    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>NoirStreet | Admin Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Playfair+Display:wght@400;700&display=swap">
        <style>
            body {
                font-family: 'Montserrat', sans-serif;
            }
            .font-playfair {
                font-family: 'Playfair Display', serif;
            }
        </style>
    </head>

    <body class="bg-base-100 text-base-content">
    <!-- Navbar -->
    <div class="navbar bg-base-100 text-base-content border-b border-base-200 px-8 sticky top-0 z-50 shadow-sm">
        <div class="navbar-start">
            <h1 class="text-xl font-playfair font-bold">NoirStreet <span class="text-base-content/70 text-sm">Admin</span></h1>
        </div>
        <div class="navbar-end">
            <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                    <div class="avatar placeholder">
                        <div class="bg-neutral text-neutral-content rounded-full w-8">
                            <span>A</span>
                        </div>
                    </div>
                </label>
                <!-- Il menu dropdown -->
                <ul tabindex="0" class="menu dropdown-content z-[1] p-2 shadow bg-base-100 rounded-box w-52 mt-4 border border-base-200">
                    <li><a href="logout-admin.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 border-r border-base-200 p-5 space-y-6 bg-base-100">
            <div class="space-y-2">
                <h3 class="text-xs font-semibold text-base-content/50 uppercase tracking-wider">Dashboard</h3>
                <ul class="menu p-0 text-sm">
                    <li><a class="py-2 active" href="#stats">Statistiche</a></li>
                    <li><a class="py-2" href="#orders">Ordini</a></li>
                </ul>
            </div>
            <div class="space-y-2">
                <h3 class="text-xs font-semibold text-base-content/50 uppercase tracking-wider">Catalogo</h3>
                <ul class="menu p-0 text-sm">
                    <li><a class="py-2" href="#products">Prodotti</a></li>
                    <li><a class="py-2" href="#inventory">Inventario</a></li>
                    <li><a class="py-2" href="#categories">Categorie</a></li>
                    <li><a class="py-2" href="#brands">Brand</a></li>
                    <li><a class="py-2" href="#styles">Stili</a></li>
                </ul>
            </div>
            <div class="space-y-2">
                <h3 class="text-xs font-semibold text-base-content/50 uppercase tracking-wider">Utenti</h3>
                <ul class="menu p-0 text-sm">
                    <li><a class="py-2" href="#reviews">Recensioni</a></li>
                </ul>
            </div>
            <div class="space-y-2">
                <h3 class="text-xs font-semibold text-base-content/50 uppercase tracking-wider">Fornitori</h3>
                <ul class="menu p-0 text-sm">
                    <li><a class="py-2" href="#suppliers">Gestione Fornitori</a></li>
                </ul>
            </div>
            <div class="space-y-2">
                <h3 class="text-xs font-semibold text-base-content/50 uppercase tracking-wider">Amministrazione</h3>
                <ul class="menu p-0 text-sm">
                    <li><a class="py-2" href="#admin-management">Gestione Admin</a></li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8 bg-base-100">
            <?php if ($message): ?>
                <div class="alert <?php echo $messageType === 'success' ? 'alert-success' : 'alert-error'; ?> mb-6">
                    <div>
                        <?php echo $message; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Stats Section -->
            <section id="stats" class="mb-12">
                <h2 class="text-2xl font-playfair mb-6">Dashboard</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="card bg-base-200 border border-base-300 shadow-sm">
                        <div class="card-body">
                            <h3 class="text-base-content/70 text-sm mb-2">Totale Prodotti</h3>
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as total FROM prodotti");
                            $row = $result->fetch_assoc();
                            ?>
                            <p class="text-3xl font-semibold"><?php echo $row['total']; ?></p>
                        </div>
                    </div>
                    <div class="card bg-base-200 border border-base-300 shadow-sm">
                        <div class="card-body">
                            <h3 class="text-base-content/70 text-sm mb-2">Totale Ordini</h3>
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as total FROM ordini");
                            $row = $result->fetch_assoc();
                            ?>
                            <p class="text-3xl font-semibold"><?php echo $row['total']; ?></p>
                        </div>
                    </div>
                    <div class="card bg-base-200 border border-base-300 shadow-sm">
                        <div class="card-body">
                            <h3 class="text-base-content/70 text-sm mb-2">Totale Utenti</h3>
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as total FROM utenti");
                            $row = $result->fetch_assoc();
                            ?>
                            <p class="text-3xl font-semibold"><?php echo $row['total']; ?></p>
                        </div>
                    </div>
                    <div class="card bg-base-200 border border-base-300 shadow-sm">
                        <div class="card-body">
                            <h3 class="text-base-content/70 text-sm mb-2">Totale Fornitori</h3>
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as total FROM fornitori");
                            $row = $result->fetch_assoc();
                            ?>
                            <p class="text-3xl font-semibold"><?php echo $row['total']; ?></p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Products Section -->
            <section id="products" class="mb-12">
                <h2 class="text-2xl font-playfair mb-6">Gestione Prodotti</h2>

                <!-- Add Product Form -->
                <div class="collapse bg-base-200 border border-base-300 mb-6">
                    <input type="checkbox" class="peer" />
                    <div class="collapse-title text-xl font-medium peer-checked:bg-base-300">
                        Aggiungi Nuovo Prodotto
                    </div>
                    <div class="collapse-content bg-base-200">
                        <form method="POST" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Nome Prodotto</span>
                                    </label>
                                    <input type="text" name="nome_p" class="input input-bordered" required />
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Prezzo</span>
                                    </label>
                                    <input type="number" step="0.01" name="prezzo" class="input input-bordered" required />
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">URL Immagine</span>
                                    </label>
                                    <input type="text" name="url_img" class="input input-bordered" required />
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Taglia</span>
                                    </label>
                                    <input type="text" name="taglia" class="input input-bordered" required />
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Brand</span>
                                    </label>
                                    <select name="nome_b" class="select select-bordered w-full" required>
                                        <?php while($brand = $brands->fetch_assoc()): ?>
                                            <option value="<?php echo htmlspecialchars($brand['nome_b']); ?>"><?php echo htmlspecialchars($brand['nome_b']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Categoria</span>
                                    </label>
                                    <select name="nome_cat" class="select select-bordered w-full" required>
                                        <?php while($category = $categories->fetch_assoc()): ?>
                                            <option value="<?php echo htmlspecialchars($category['nome_cat']); ?>"><?php echo htmlspecialchars($category['nome_cat']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Stile</span>
                                    </label>
                                    <select name="nome_s" class="select select-bordered w-full" required>
                                        <?php while($style = $styles->fetch_assoc()): ?>
                                            <option value="<?php echo htmlspecialchars($style['nome_s']); ?>"><?php echo htmlspecialchars($style['nome_s']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Descrizione</span>
                                </label>
                                <textarea name="descrizione" class="textarea textarea-bordered h-24" required></textarea>
                            </div>
                            <div class="form-control mt-6">
                                <button name="add_product" class="btn btn-primary">Aggiungi Prodotto</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Product Form -->
                <div class="collapse bg-base-200 border border-base-300 mb-6">
                    <input type="checkbox" class="peer" />
                    <div class="collapse-title text-xl font-medium peer-checked:bg-base-300">
                        Elimina Prodotto
                    </div>
                    <div class="collapse-content bg-base-200">
                        <form method="POST" class="space-y-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Seleziona Prodotto</span>
                                </label>
                                <select name="id_p" class="select select-bordered w-full" required>
                                    <?php
                                    $products_delete = $conn->query("SELECT id_p, nome_p FROM prodotti ORDER BY nome_p");
                                    while($product = $products_delete->fetch_assoc()):
                                        ?>
                                        <option value="<?php echo $product['id_p']; ?>"><?php echo htmlspecialchars($product['nome_p']); ?> (ID: <?php echo $product['id_p']; ?>)</option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-control mt-6">
                                <button name="delete_product" class="btn btn-error">Elimina Prodotto</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Product List -->
                <h3 class="text-xl mb-4">Lista Prodotti</h3>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Prezzo</th>
                            <th>Categoria</th>
                            <th>Brand</th>
                            <th>Taglia</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $products_list = $conn->query("SELECT id_p, nome_p, prezzo, nome_cat, nome_b, taglia FROM prodotti ORDER BY id_p");
                        while ($product = $products_list->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $product['id_p']; ?></td>
                                <td><?php echo htmlspecialchars($product['nome_p']); ?></td>
                                <td>€<?php echo number_format($product['prezzo'], 2); ?></td>
                                <td><?php echo htmlspecialchars($product['nome_cat']); ?></td>
                                <td><?php echo htmlspecialchars($product['nome_b']); ?></td>
                                <td><?php echo htmlspecialchars($product['taglia']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Inventory Section -->
            <section id="inventory" class="mb-12">
                <h2 class="text-2xl font-playfair mb-6">Gestione Inventario</h2>

                <!-- Update Inventory Form -->
                <div class="collapse bg-base-200 border border-base-300 mb-6">
                    <input type="checkbox" class="peer" />
                    <div class="collapse-title text-xl font-medium peer-checked:bg-base-300">
                        Aggiorna Inventario
                    </div>
                    <div class="collapse-content bg-base-200">
                        <form method="POST" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Prodotto</span>
                                    </label>
                                    <select name="id_p" class="select select-bordered w-full" required>
                                        <?php
                                        $products_inventory = $conn->query("SELECT id_p, nome_p FROM prodotti ORDER BY nome_p");
                                        while($product = $products_inventory->fetch_assoc()):
                                            ?>
                                            <option value="<?php echo $product['id_p']; ?>"><?php echo htmlspecialchars($product['nome_p']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Fornitore</span>
                                    </label>
                                    <select name="id_f" class="select select-bordered w-full" required>
                                        <?php
                                        $suppliers_inventory = $conn->query("SELECT id_f, nome FROM fornitori ORDER BY nome");
                                        while($supplier = $suppliers_inventory->fetch_assoc()):
                                            ?>
                                            <option value="<?php echo $supplier['id_f']; ?>"><?php echo htmlspecialchars($supplier['nome']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-control mt-6">
                                <button name="update_inventory" class="btn btn-primary">Aggiorna Inventario</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Inventory List -->
                <h3 class="text-xl mb-4">Registro Inventario</h3>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Prodotto</th>
                            <th>Data</th>
                            <th>Admin</th>
                            <th>Fornitore</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $inventory_list = $conn->query("SELECT i.id_i, p.nome_p, i.data_rif, a.username, f.nome 
                                                FROM inventari i 
                                                JOIN prodotti p ON i.id_p = p.id_p 
                                                JOIN admin a ON i.id_a = a.id_a 
                                                JOIN fornitori f ON i.id_f = f.id_f 
                                                ORDER BY i.data_rif DESC");
                        while ($inventory = $inventory_list->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $inventory['id_i']; ?></td>
                                <td><?php echo htmlspecialchars($inventory['nome_p']); ?></td>
                                <td><?php echo $inventory['data_rif']; ?></td>
                                <td><?php echo htmlspecialchars($inventory['username']); ?></td>
                                <td><?php echo htmlspecialchars($inventory['nome']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Categories Section -->
            <section id="categories" class="mb-12">
                <h2 class="text-2xl font-playfair mb-6">Gestione Categorie</h2>

                <!-- Add Category Form -->
                <div class="collapse bg-base-200 border border-base-300 mb-6">
                    <input type="checkbox" class="peer" />
                    <div class="collapse-title text-xl font-medium peer-checked:bg-base-300">
                        Aggiungi Categoria
                    </div>
                    <div class="collapse-content bg-base-200">
                        <form method="POST" class="space-y-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Nome Categoria</span>
                                </label>
                                <input type="text" name="nome_cat" class="input input-bordered" required />
                            </div>
                            <div class="form-control mt-6">
                                <button name="add_category" class="btn btn-primary">Aggiungi Categoria</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Categories List -->
                <h3 class="text-xl mb-4">Lista Categorie</h3>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                        <tr>
                            <th>Nome Categoria</th>
                            <th>Numero Prodotti</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $categories_list = $conn->query("SELECT c.nome_cat, COUNT(p.id_p) as num_products 
                                                 FROM categorie c 
                                                 LEFT JOIN prodotti p ON c.nome_cat = p.nome_cat 
                                                 GROUP BY c.nome_cat 
                                                 ORDER BY c.nome_cat");
                        while ($category = $categories_list->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category['nome_cat']); ?></td>
                                <td><?php echo $category['num_products']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Brands Section -->
            <section id="brands" class="mb-12">
                <h2 class="text-2xl font-playfair mb-6">Gestione Brand</h2>

                <!-- Add Brand Form -->
                <div class="collapse bg-base-200 border border-base-300 mb-6">
                    <input type="checkbox" class="peer" />
                    <div class="collapse-title text-xl font-medium peer-checked:bg-base-300">
                        Aggiungi Brand
                    </div>
                    <div class="collapse-content bg-base-200">
                        <form method="POST" class="space-y-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Nome Brand</span>
                                </label>
                                <input type="text" name="nome_b" class="input input-bordered" required />
                            </div>
                            <div class="form-control mt-6">
                                <button name="add_brand" class="btn btn-primary">Aggiungi Brand</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Brands List -->
                <h3 class="text-xl mb-4">Lista Brand</h3>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                        <tr>
                            <th>Nome Brand</th>
                            <th>Numero Prodotti</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $brands_list = $conn->query("SELECT b.nome_b, COUNT(p.id_p) as num_products 
                                             FROM brand b 
                                             LEFT JOIN prodotti p ON b.nome_b = p.nome_b 
                                             GROUP BY b.nome_b 
                                             ORDER BY b.nome_b");
                        while ($brand = $brands_list->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($brand['nome_b']); ?></td>
                                <td><?php echo $brand['num_products']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Styles Section -->
            <section id="styles" class="mb-12">
                <h2 class="text-2xl font-playfair mb-6">Gestione Stili</h2>

                <!-- Add Style Form -->
                <div class="collapse bg-base-200 border border-base-300 mb-6">
                    <input type="checkbox" class="peer" />
                    <div class="collapse-title text-xl font-medium peer-checked:bg-base-300">
                        Aggiungi Stile
                    </div>
                    <div class="collapse-content bg-base-200">
                        <form method="POST" class="space-y-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Nome Stile</span>
                                </label>
                                <input type="text" name="nome_s" class="input input-bordered" required />
                            </div>
                            <div class="form-control mt-6">
                                <button name="add_style" class="btn btn-primary">Aggiungi Stile</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Styles List -->
                <h3 class="text-xl mb-4">Lista Stili</h3>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                        <tr>
                            <th>Nome Stile</th>
                            <th>Numero Prodotti</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $styles_list = $conn->query("SELECT s.nome_s, COUNT(p.id_p) as num_products 
                                             FROM stili s 
                                             LEFT JOIN prodotti p ON s.nome_s = p.nome_s 
                                             GROUP BY s.nome_s 
                                             ORDER BY s.nome_s");
                        while ($style = $styles_list->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($style['nome_s']); ?></td>
                                <td><?php echo $style['num_products']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Suppliers Section -->
            <section id="suppliers" class="mb-12">
                <h2 class="text-2xl font-playfair mb-6">Gestione Fornitori</h2>

                <!-- Add Supplier Form -->
                <div class="collapse bg-base-200 border border-base-300 mb-6">
                    <input type="checkbox" class="peer" />
                    <div class="collapse-title text-xl font-medium peer-checked:bg-base-300">
                        Aggiungi Fornitore
                    </div>
                    <div class="collapse-content bg-base-200">
                        <form method="POST" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Nome</span>
                                    </label>
                                    <input type="text" name="nome" class="input input-bordered" required />
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Email</span>
                                    </label>
                                    <input type="email" name="mail" class="input input-bordered" required />
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Indirizzo</span>
                                    </label>
                                    <input type="text" name="indirizzo" class="input input-bordered" required />
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Città</span>
                                    </label>
                                    <input type="text" name="citta" class="input input-bordered" required />
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">CAP</span>
                                    </label>
                                    <input type="text" name="cap" class="input input-bordered" required />
                                </div>
                            </div>
                            <div class="form-control mt-6">
                                <button name="add_supplier" class="btn btn-primary">Aggiungi Fornitore</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Suppliers List -->
                <h3 class="text-xl mb-4">Lista Fornitori</h3>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Città</th>
                            <th>Indirizzo</th>
                            <th>CAP</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $suppliers_list = $conn->query("SELECT id_f, nome, mail, citta, indirizzo, cap FROM fornitori ORDER BY nome");
                        while ($supplier = $suppliers_list->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $supplier['id_f']; ?></td>
                                <td><?php echo htmlspecialchars($supplier['nome']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['mail']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['citta']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['indirizzo']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['cap']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Reviews Section -->
            <section id="reviews" class="mb-12">
                <h2 class="text-2xl font-playfair mb-6">Recensioni</h2>

                <!-- Reviews List -->
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                        <tr>
                            <th>Prodotto</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Voto</th>
                            <th>Commento</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $reviews_list = $conn->query("SELECT p.nome_p, r.data_r, u.nome, u.cognome, r.rating AS voto, r.commento
                              FROM prodotti p
                              JOIN giudicare g ON p.id_p = g.id_p
                              JOIN recensioni r ON g.id_r = r.id_r
                              JOIN utenti u ON r.id_u = u.id_u
                              ORDER BY r.data_r DESC");
                        while ($review = $reviews_list->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($review['nome_p']); ?></td>
                                <td><?php echo htmlspecialchars($review['nome'] . ' ' . $review['cognome']); ?></td>
                                <td><?php echo $review['data_r']; ?></td>
                                <td>
                                    <div class="rating rating-sm">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <input type="radio" name="rating-<?php echo $review['nome_p']; ?>" class="mask mask-star-2 bg-orange-400" <?php echo ($i == $review['voto']) ? 'checked' : 'disabled'; ?> />
                                        <?php endfor; ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($review['commento']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="admin-management" class="mb-12">
                <h2 class="text-2xl font-playfair mb-6">Gestione Admin</h2>

                <!-- Add Admin Form -->
                <div class="collapse bg-base-200 border border-base-300 mb-6">
                    <input type="checkbox" class="peer" />
                    <div class="collapse-title text-xl font-medium peer-checked:bg-base-300">
                        Aggiungi Nuovo Admin
                    </div>
                    <div class="collapse-content bg-base-200">
                        <form method="POST" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Username</span>
                                    </label>
                                    <input type="text" name="admin_username" class="input input-bordered" required maxlength="10" />
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Password</span>
                                    </label>
                                    <input type="password" name="admin_password" class="input input-bordered" required />
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Conferma Password</span>
                                    </label>
                                    <input type="password" name="confirm_password" class="input input-bordered" required />
                                </div>
                            </div>
                            <div class="form-control mt-6">
                                <button name="add_admin" class="btn btn-primary">Crea Admin</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Admin List -->
                <h3 class="text-xl mb-4">Lista Admin</h3>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $admins_list = $conn->query("SELECT id_a, username FROM admin ORDER BY id_a");
                        while ($admin = $admins_list->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $admin['id_a']; ?></td>
                                <td><?php echo htmlspecialchars($admin['username']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <script>
        // Script per rendere attivi i link della sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('.menu a');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    links.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
    </body>
    </html>

<?php
// Chiudere la connessione al database
$conn->close();
?>