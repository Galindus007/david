<?php
// La contraseña que quieres usar
$passwordParaEncriptar = 'admin123'; 

// Generar el hash
$hash = password_hash($passwordParaEncriptar, PASSWORD_DEFAULT);

echo "Copia y pega este hash en tu base de datos: <br><br>";
echo "<strong>" . $hash . "</strong>";
// Un hash válido empieza con $2y$10$ o similar, no $2y$2y$
?>