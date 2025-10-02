<?php
/**
 * php/load_products.php
 * Este script genera las tarjetas de producto para el catálogo.
 */

// Usamos la conexión a la BD que ya fue abierta en index.php
global $conn;

// 1. IMPORTANTE: Seleccionamos el 'id' del producto.
$sql = "SELECT id, nombre, descripcion, imagen_url FROM productos ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    
    // 2. IMPORTANTE: Aquí se crea el enlace <a> que envuelve todo.
    echo '<a href="producto.php?id=' . $row["id"] . '" class="product-card-link">';
    
    // Este es el contenido de la tarjeta que ya tenías
    echo '  <div class="product-card">';
    echo '    <img src="' . htmlspecialchars($row["imagen_url"]) . '" alt="' . htmlspecialchars($row["nombre"]) . '">';
    echo '    <div class="product-info">';
    echo '      <h3>' . htmlspecialchars($row["nombre"]) . '</h3>';
    echo '      <p>' . htmlspecialchars($row["descripcion"]) . '</p>';
    echo '    </div>';
    echo '  </div>';
    
    // 3. IMPORTANTE: Aquí se cierra la etiqueta del enlace </a>.
    echo '</a>';

  }
} else {
  echo "<p>No hay productos para mostrar en este momento.</p>";
}
?>