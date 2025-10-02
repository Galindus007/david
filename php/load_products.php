<?php
// Incluir la conexión
include 'db_connection.php';

// Consulta SQL para obtener los productos
$sql = "SELECT nombre, descripcion, imagen_url FROM productos ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  // Recorrer cada producto y mostrarlo
  while($row = $result->fetch_assoc()) {
    echo '<div class="product-card">';
    // Usar htmlspecialchars para seguridad
    echo '  <img src="' . htmlspecialchars($row["imagen_url"]) . '" alt="' . htmlspecialchars($row["nombre"]) . '">';
    echo '  <div class="product-info">';
    echo '    <h3>' . htmlspecialchars($row["nombre"]) . '</h3>';
    echo '    <p>' . htmlspecialchars($row["descripcion"]) . '</p>';
    echo '  </div>';
    echo '</div>';
  }
} else {
  echo "<p>No hay productos disponibles en este momento.</p>";
}

// Cerrar la conexión
$conn->close();
?>