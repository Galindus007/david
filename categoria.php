<?php
include 'php/db_connection.php';

// 1. Obtener el ID de la categoría desde la URL de forma segura
$category_id = 0;
if (isset($_GET['id'])) {
    $category_id = intval($_GET['id']);
}
if ($category_id <= 0) {
    header("Location: index.php"); // Redirigir si no hay ID válido
    exit;
}

// 2. Obtener la información de la categoría para el título
$cat_stmt = $conn->prepare("SELECT nombre FROM product_categories WHERE id = ?");
$cat_stmt->bind_param("i", $category_id);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();
if ($cat_result->num_rows === 0) {
    die("Categoría no encontrada.");
}
$category = $cat_result->fetch_assoc();
$category_name = $category['nombre'];

// 3. Obtener los productos que pertenecen a esta categoría
$prod_stmt = $conn->prepare("SELECT * FROM productos WHERE category_id = ? ORDER BY id DESC");
$prod_stmt->bind_param("i", $category_id);
$prod_stmt->execute();
$products = $prod_stmt->get_result();

// Cargar datos para el header y footer
$settings_result = $conn->query("SELECT * FROM site_settings");
$settings = [];
while ($row = $settings_result->fetch_assoc()) { $settings[$row['setting_name']] = $row['setting_value']; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category_name); ?> - <?php echo htmlspecialchars($settings['company_name']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php include 'php/header.php'; ?>

    <main class="product-catalog" id="catalogo">
        <h2><?php echo htmlspecialchars($category_name); ?></h2>

        <div class="product-grid">
            <?php if ($products && $products->num_rows > 0): ?>
                <?php while($row = $products->fetch_assoc()): ?>
                    <a href="producto.php?id=<?php echo $row["id"]; ?>" class="product-card-link">
                      <div class="product-card">
                        <img src="<?php echo htmlspecialchars($row["imagen_url"]); ?>" alt="<?php echo htmlspecialchars($row["nombre"]); ?>">
                        <div class="product-info">
                          <h3><?php echo htmlspecialchars($row["nombre"]); ?></h3>
                          <p><?php echo htmlspecialchars($row["descripcion"]); ?></p>
                        </div>
                      </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay productos en esta categoría por el momento.</p>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'php/footer.php'; ?>

</body>
</html>