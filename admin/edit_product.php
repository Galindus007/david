<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
include '../php/db_connection.php';

// 1. Obtener el ID del producto de la URL
$product_id = intval($_GET['id'] ?? 0);
if ($product_id <= 0) {
    header('Location: dashboard.php');
    exit;
}

// 2. Obtener los datos del producto a editar
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
if ($product_result->num_rows === 0) {
    header('Location: dashboard.php');
    exit;
}
$product = $product_result->fetch_assoc();

// 3. Obtener todas las categorías para el menú desplegable
$categories_result = $conn->query("SELECT * FROM product_categories ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f9f9f9;
            padding: 2rem;
        }

        h2 {
            color: #005A9C;
            border-bottom: 2px solid #005A9C;
            padding-bottom: 10px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .section {
            background: white;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px 15px;
            border: none;
            background: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }

        .logout {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: #dc3545;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Producto</h1>
        <p><a href="dashboard.php">← Volver al Dashboard</a></p>

        <div class="section">
            <form action="save.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_product">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                <label for="prod_nombre">Título:</label>
                <input type="text" id="prod_nombre" name="nombre" value="<?php echo htmlspecialchars($product['nombre']); ?>" required>

                <label for="prod_categoria">Categoría:</label>
                <select name="category_id" id="prod_categoria" required>
                    <option value="">-- Selecciona una categoría --</option>
                    <?php while($cat = $categories_result->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php if($cat['id'] == $product['category_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cat['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="prod_desc">Descripción corta:</label>
                <textarea id="prod_desc" name="descripcion" required><?php echo htmlspecialchars($product['descripcion']); ?></textarea>

                <label for="prod_detalles">Detalles Completos:</label>
                <textarea id="prod_detalles" name="detalles" rows="6"><?php echo htmlspecialchars($product['detalles']); ?></textarea>

                <label>Imagen Actual:</label>
                <img src="../<?php echo htmlspecialchars($product['imagen_url']); ?>" alt="Imagen actual" width="100">

                <label for="prod_img">Subir Nueva Imagen (opcional):</label>
                <input type="file" id="prod_img" name="imagen">

                <button type="submit">Guardar Cambios</button>
            </form>
        </div>
    </div>
</body>
</html>