<?php
// Configuración de la conexión a la base de datos
$host = "localhost"; // Servidor de la base de datos (por defecto, localhost)
$user = "root"; // Usuario de la base de datos (cambiar si es necesario)
$password = ""; // Contraseña (dejar vacío si no hay)
$database = "test_db"; // Nombre de la base de datos

// Crear conexión con MySQLi
$conexion = new mysqli($host, $user, $password, $database);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Configurar el conjunto de caracteres a UTF-8
$conexion->set_charset("utf8");

?>
