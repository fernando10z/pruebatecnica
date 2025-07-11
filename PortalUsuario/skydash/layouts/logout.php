<?php
session_start();
session_destroy(); // Cierra la sesión del usuario
header("Location: ../../index.php"); // Redirige a la página de login
exit();
?>
