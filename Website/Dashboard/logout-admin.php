<?php
session_start();

$_SESSION = array();

// Distrugge la sessione
session_destroy();

// Reindirizza alla pagina di login
header("Location: admin-auth.php");
exit();
?>