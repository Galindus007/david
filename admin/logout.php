<?php
// 1. Iniciar la sesión
// Es necesario iniciar la sesión para poder destruirla.
session_start();

// 2. Eliminar todas las variables de sesión
// Esto vacía el array $_SESSION.
session_unset();

// 3. Destruir la sesión por completo
// Esto elimina el archivo de sesión del servidor.
session_destroy();

// 4. Redirigir al usuario a la página de login (index.php dentro de la carpeta /admin/)
// Es importante llamar a exit() después de header() para asegurar que el script se detenga.
header('Location: index.php');
exit;
?>