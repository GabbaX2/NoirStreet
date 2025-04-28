<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: Sign-In.php");
    exit;
}

// Gestione cambio password
$password_message = "";
$password_class = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $host = "localhost:8080";
    $user = "root";
    $password = "";
    $dbname = "streetnoirdb";

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        $password_message = "Errore di connessione al database.";
        $password_class = "text-red-500";
    } else {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $user_id = $_SESSION['user_id'];

        // Verifica password corrente
        $stmt = $conn->prepare("SELECT password FROM utenti WHERE id_u = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 8) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    $update_stmt = $conn->prepare("UPDATE utenti SET password = ? WHERE id_u = ?");
                    $update_stmt->bind_param("si", $hashed_password, $user_id);

                    if ($update_stmt->execute()) {
                        $password_message = "Password cambiata con successo!";
                        $password_class = "text-green-500";
                    } else {
                        $password_message = "Errore durante l'aggiornamento della password.";
                        $password_class = "text-red-500";
                    }

                    $update_stmt->close();
                } else {
                    $password_message = "La nuova password deve essere lunga almeno 8 caratteri.";
                    $password_class = "text-red-500";
                }
            } else {
                $password_message = "Le nuove password non corrispondono.";
                $password_class = "text-red-500";
            }
        } else {
            $password_message = "Password corrente errata.";
            $password_class = "text-red-500";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo Utente | NoirStreet</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .password-toggle { cursor: pointer; }
    </style>
</head>
<body class="bg-black text-white min-h-screen flex items-center py-8">
<div class="max-w-2xl mx-auto bg-gray-900 p-8 rounded-xl shadow-xl border border-gray-700 w-full">
    <h1 class="text-3xl mb-4 font-bold">Ciao, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utente'); ?>!</h1>
    <p class="text-gray-400 mb-6">Gestisci il tuo profilo NoirStreet</p>

    <div class="grid md:grid-cols-2 gap-8">
        <!-- Sezione Informazioni Utente -->
        <div class="border-b border-gray-700 pb-6">
            <h2 class="text-xl font-semibold mb-4">Le tue informazioni</h2>
            <div class="space-y-3">
                <p><span class="text-gray-400">Nome:</span> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'N/D'); ?></p>
                <p><span class="text-gray-400">Email:</span> <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'N/D'); ?></p>
            </div>
        </div>

        <!-- Sezione Cambio Password -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Cambia Password</h2>

            <?php if (!empty($password_message)): ?>
                <div class="mb-4 p-3 rounded <?php echo $password_class; ?>">
                    <?php echo $password_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text text-gray-300">Password corrente</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="current_password" required
                               class="input input-bordered w-full bg-gray-800 border-gray-700">
                        <span class="absolute right-3 top-3 password-toggle" onclick="togglePassword(this)">
                            👁️
                        </span>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text text-gray-300">Nuova password</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="new_password" required
                               class="input input-bordered w-full bg-gray-800 border-gray-700">
                        <span class="absolute right-3 top-3 password-toggle" onclick="togglePassword(this)">
                            👁️
                        </span>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text text-gray-300">Conferma nuova password</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="confirm_password" required
                               class="input input-bordered w-full bg-gray-800 border-gray-700">
                        <span class="absolute right-3 top-3 password-toggle" onclick="togglePassword(this)">
                            👁️
                        </span>
                    </div>
                </div>

                <button type="submit" name="change_password"
                        class="btn btn-primary w-full rounded-none border border-white bg-transparent hover:bg-white hover:text-black">
                    Cambia Password
                </button>
            </form>
        </div>
    </div>

    <div class="mt-8 flex justify-between items-center">
        <a href="Index.php" class="text-gray-400 hover:text-white">← Torna alla home</a>
        <a href="user-logout.php" class="btn bg-white text-black rounded-none hover:bg-gray-200 px-6 py-2 border border-white transition">
            Logout
        </a>
    </div>
</div>

<script>
    function togglePassword(icon) {
        const input = icon.previousElementSibling;
        if (input.type === "password") {
            input.type = "text";
            icon.textContent = "🔒";
        } else {
            input.type = "password";
            icon.textContent = "👁️";
        }
    }
</script>
</body>
</html>