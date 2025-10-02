<?php
session_start();
include '../php/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Contraseña correcta
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['username'] = $username;
            header('Location: dashboard.php');
            exit;
        }
    }
    
    // Si el usuario no existe o la contraseña es incorrecta
    header('Location: index.php?error=1');
    exit;
}
?>