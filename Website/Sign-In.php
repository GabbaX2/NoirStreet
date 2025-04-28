<?php
session_start();
$messaggio = "";
$classeMessaggio = "";

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'Index.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost:8080";
    $user = "root";
    $password = "";
    $dbname = "streetnoirdb";

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        $messaggio = "Si è verificato un errore, per favore riprova più tardi.";
        $classeMessaggio = "text-red-500";
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Store redirect URL when form is submitted
        $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'index.php';

        $stmt = $conn->prepare("SELECT id_u, nome, cognome, password FROM utenti WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id_u'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_surname'] = $user['cognome'];
                $_SESSION['logged_in'] = true;

                // Sicurezza sessione
                session_regenerate_id(true);

                // Redirect to the page the user came from
                header("Location: " . $redirect);
                exit();
            } else {
                $messaggio = "Password errata.";
                $classeMessaggio = "text-red-500";
            }
        } else {
            $messaggio = "Nessun utente trovato con questa email.";
            $classeMessaggio = "text-red-500";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | NoirStreet</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Playfair+Display:wght@400;700&display=swap">
    <style>
        .font-montserrat { font-family: 'Montserrat', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-black text-white font-montserrat">

<div class="min-h-screen flex items-center justify-center px-6 py-16">
    <div class="w-full max-w-md space-y-8 border border-gray-800 p-8 shadow-lg">
        <h2 class="text-center text-3xl font-playfair">Login</h2>

        <?php if (!empty($messaggio)): ?>
            <div class="text-center text-sm <?= $classeMessaggio ?>"><?= $messaggio ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6">
            <div>
                <label class="block text-sm mb-1">Email</label>
                <input type="email" name="email" required class="input input-bordered w-full bg-transparent border-gray-700">
            </div>
            <div>
                <label class="block text-sm mb-1">Password</label>
                <input type="password" name="password" required class="input input-bordered w-full bg-transparent border-gray-700">
            </div>

            <!-- Hidden field to store the redirect URL -->
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">

            <button type="submit" class="btn btn-primary w-full rounded-none bg-transparent border border-white hover:bg-white hover:text-black">Accedi</button>
        </form>

        <p class="text-sm text-center text-gray-400">Non hai un account?
            <a href="Sign-Up.php" class="hover:underline text-white">Registrati</a>
        </p>
    </div>
</div>
</body>
</html>