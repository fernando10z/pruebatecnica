<?php
session_start();
require_once 'db.php'; // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    
    // Validar que el campo username no esté vacío
    if (empty($username)) {
        header("Location: index.php?mensaje=campos_vacios");
        exit();
    }
    
    // Buscar el usuario en la base de datos por username
    $stmt = $conexion->prepare("SELECT id, customer_id, username FROM users WHERE username = ? AND deleted = 0");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_usuario, $customer_id, $username_db);
        $stmt->fetch();
        
        // Guardar datos en la sesión
        $_SESSION['id_usuario'] = $id_usuario;
        $_SESSION['customer_id'] = $customer_id;
        $_SESSION['username'] = $username_db;
        
        header("Location: index.php?mensaje=bienvenido");
        exit();
    } else {
        header("Location: index.php?mensaje=noregistrado");
        exit();
    }
    
    $stmt->close();
    $conexion->close();
} else {
    header("Location: index.php");
    exit();
}
?>