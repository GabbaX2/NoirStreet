<?php
session_start();


if (isset($_SESSION['admin_id'])) {
    header("Location: admin-dashboard.php");
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

// Gestione del login
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; // La password verrà verificata con password_verify

    // Query per trovare l'amministratore
    $sql = "SELECT id_a, username, password FROM admin WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Verifica la password (assumendo che nel database sia salvata con password_hash)
        if (password_verify($password, $row['password'])) {
            // Password corretta, imposta la sessione
            $_SESSION['admin_id'] = $row['id_a'];
            $_SESSION['admin_username'] = $row['username'];

            // Reindirizza alla dashboard
            header("Location: Dashboard.php");
            exit();
        } else {
            // Password errata
            $error_message = "Username o password non validi.";
        }
    } else {
        // Username non trovato
        $error_message = "Username o password non validi.";
    }
}


function createTestAdmin($conn) {
    // Verifica se esiste già un amministratore
    $check = $conn->query("SELECT COUNT(*) as count FROM admin");
    $row = $check->fetch_assoc();

    if ($row['count'] == 0) {
        // Crea un amministratore di test
        $username = "admin";
        $password = password_hash("admin123", PASSWORD_DEFAULT);

        // Assumendo che id_a sia un valore numerico che inizia da 1
        $sql = "INSERT INTO admin (id_a, username, password) VALUES (1, '$username', '$password')";

        if ($conn->query($sql) === TRUE) {
            return "Amministratore di test creato. Username: admin, Password: admin123";
        } else {
            return "Errore nella creazione dell'amministratore di test: " . $conn->error;
        }
    }
    return null;
}


?>

    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>NoirStreet | Admin Login</title>
        <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Playfair+Display:wght@400;700&display=swap">
        <style>
            .font-montserrat {
                font-family: 'Montserrat', sans-serif;
            }
            .font-playfair {
                font-family: 'Playfair Display', serif;
            }
        </style>
    </head>

    <body class="font-montserrat bg-black text-white">
    <div class="flex items-center justify-center min-h-screen p-5">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-playfair mb-2">NoirStreet</h1>
                <p class="text-gray-400">Area Amministrazione</p>
            </div>

            <div class="bg-gray-900 border border-gray-800 p-8">
                <h2 class="text-2xl font-playfair mb-6">Accesso Admin</h2>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error mb-6">
                        <div>
                            <?php echo $error_message; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($admin_creation_message)): ?>
                    <div class="alert alert-info mb-6">
                        <div>
                            <?php echo $admin_creation_message; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-white">Username</span>
                        </label>
                        <input type="text" name="username" class="input input-bordered bg-black border-gray-800" required />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-white">Password</span>
                        </label>
                        <input type="password" name="password" class="input input-bordered bg-black border-gray-800" required />
                    </div>

                    <div class="form-control mt-6">
                        <button type="submit" class="btn btn-primary rounded-none">Accedi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </body>
    </html>

<?php $conn->close(); ?>