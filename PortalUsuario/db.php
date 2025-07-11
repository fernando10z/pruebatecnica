<?php
// Configuración de la conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$database = "test_db"; 

// Crear conexión con MySQLi
$conexion = new mysqli($host, $user, $password, $database);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Configurar el conjunto de caracteres a UTF-8
$conexion->set_charset("utf8");

?>
