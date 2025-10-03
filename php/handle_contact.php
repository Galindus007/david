<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $mensaje = trim($_POST['mensaje']);

    // Validación simple
    if (empty($nombre) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($mensaje)) {
        header('Location: ../contacto.php?status=error');
        exit;
    }

    // Insertar en la base de datos de forma segura
    $stmt = $conn->prepare("INSERT INTO contact_messages (nombre, email, mensaje) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $email, $mensaje);
    
    if ($stmt->execute()) {
        header('Location: ../contacto.php?status=success');
    } else {
        header('Location: ../contacto.php?status=error');
    }
    exit;
}
?>