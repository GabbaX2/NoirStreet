<?php


$messaggio = "";
$classeMessaggio = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost:8080";
    $user = "root";
    $password = "";
    $dbname = "streetnoirdb";

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        $messaggio = "Connessione fallita: " . $conn->connect_error;
        $classeMessaggio = "text-red-500";
    } else {
        $nome = $_POST['nome'];
        $cognome = $_POST['cognome'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $messaggio = "Le password non corrispondono.";
            $classeMessaggio = "text-red-500";
        } else {
            $checkEmail = $conn->prepare("SELECT id_u FROM utenti WHERE email = ?");
            $checkEmail->bind_param("s", $email);
            $checkEmail->execute();
            $checkEmail->store_result();

            if ($checkEmail->num_rows > 0) {
                $messaggio = "Questa email è già registrata.";
                $classeMessaggio = "text-red-500";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Calcolo ID nuovo utente
                $result = $conn->query("SELECT MAX(id_u) AS max_id FROM utenti");
                $row = $result->fetch_assoc();
                $new_id = $row['max_id'] ? $row['max_id'] + 1 : 1;

                $sql = "INSERT INTO utenti (id_u, nome, cognome, email, password)
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issss", $new_id, $nome, $cognome, $email, $hashedPassword);

                if ($stmt->execute()) {
                    $messaggio = "Registrazione completata! <a href='login.php' class='underline'>Accedi</a>";
                    $classeMessaggio = "text-green-500";
                } else {
                    $messaggio = "Errore durante la registrazione: " . $stmt->error;
                    $classeMessaggio = "text-red-500";
                }

                $stmt->close();
            }

            $checkEmail->close();
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | NoirStreet</title>
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
        <h2 class="text-center text-3xl font-playfair">Create Account</h2>

        <?php if (!empty($messaggio)): ?>
            <div class="text-center text-sm <?= $classeMessaggio ?>"><?= $messaggio ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6">
            <div>
                <label class="block text-sm mb-1">Nome</label>
                <input type="text" name="nome" required class="input input-bordered w-full bg-transparent border-gray-700">
            </div>
            <div>
                <label class="block text-sm mb-1">Cognome</label>
                <input type="text" name="cognome" required class="input input-bordered w-full bg-transparent border-gray-700">
            </div>
            <div>
                <label class="block text-sm mb-1">Email</label>
                <input type="email" name="email" required class="input input-bordered w-full bg-transparent border-gray-700">
            </div>
            <div>
                <label class="block text-sm mb-1">Password</label>
                <input type="password" name="password" required class="input input-bordered w-full bg-transparent border-gray-700">
            </div>
            <div>
                <label class="block text-sm mb-1">Conferma Password</label>
                <input type="password" name="confirm_password" required class="input input-bordered w-full bg-transparent border-gray-700">
            </div>
            <button type="submit" class="btn btn-primary w-full rounded-none bg-transparent border border-white hover:bg-white hover:text-black">Registrati</button>
        </form>

        <p class="text-sm text-center text-gray-400">Hai già un account?
            <a href="Sign-In.php" class="hover:underline text-white">Login</a>
        </p>
    </div>
</div>
</body>
</html>