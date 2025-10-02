<?php
include 'php/db_connection.php';

// 1. Obtener el ID del producto desde la URL de forma segura
$product_id = 0;
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']); // intval() convierte a entero para seguridad
}

if ($product_id <= 0) {
    // Si no hay ID o no es válido, redirigimos o mostramos error
    header("Location: index.php");
    exit;
}

// 2. Buscar el producto en la base de datos
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Si no se encuentra un producto con ese ID
    echo "Producto no encontrado.";
    exit;
}

// 3. Guardar los datos del producto en una variable
$product = $result->fetch_assoc();

// --- Carga de datos para header/footer (opcional, pero mantiene consistencia) ---
$settings_result = $conn->query("SELECT * FROM site_settings");
$settings = [];
while ($row = $settings_result->fetch_assoc()) {
    $settings[$row['setting_name']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo htmlspecialchars($product['nombre']); ?> - <?php echo htmlspecialchars($settings['company_name']); ?></title>
    
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <header class="main-header">
        <div class="logo">
            <a href="index.php"><img src="images/logo.png" alt="Logo de la Empresa"></a>
            <a href="index.php" style="text-decoration:none;"><h1><?php echo htmlspecialchars($settings['company_name']); ?></h1></a>
        </div>
    </header>

    <main class="product-detail-container">
        <div class="product-detail-image">
            <img src="<?php echo htmlspecialchars($product['imagen_url']); ?>" alt="<?php echo htmlspecialchars($product['nombre']); ?>">
        </div>
        <div class="product-detail-info">
            <h1><?php echo htmlspecialchars($product['nombre']); ?></h1>
            <p class="short-description"><?php echo htmlspecialchars($product['descripcion']); ?></p>
            <hr>
            <div class="full-details">
                <h3>Detalles del Producto</h3>
                <p><?php echo nl2br(htmlspecialchars($product['detalles'])); ?></p>
            </div>
            <a href="index.php#catalogo" class="back-link">← Volver al Catálogo</a>
        </div>
    </main>

</body>
</html>