<?php
$servername = "localhost";    // Generalmente 'localhost'
$username = "jose";           // Tu usuario de la base de datos
$password = "1234";               // Tu contraseña
$dbname = "empresa_db";   // El nombre de tu base de datos

// Crear conexión con MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Asegurar que la conexión use UTF-8 para carácteres especiales
$conn->set_charset("utf8");
?>

