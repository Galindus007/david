<?php
// check_session.php se asegura que solo usuarios logueados accedan
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

include '../php/db_connection.php';

// Cargar todos los datos existentes
$settings_result = $conn->query("SELECT * FROM site_settings");
$settings = [];
while ($row = $settings_result->fetch_assoc()) {
    $settings[$row['setting_name']] = $row['setting_value'];
}

$footer_result = $conn->query("SELECT * FROM footer_info");
$footer = [];
while ($row = $footer_result->fetch_assoc()) {
    $footer[$row['info_key']] = $row['info_value'];
}

$slides = $conn->query("SELECT * FROM banner_slides ORDER BY orden ASC");
$products = $conn->query("SELECT * FROM productos ORDER BY id DESC");

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
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
    <a href="logout.php" class="logout"><button>Cerrar Sesión</button></a>

    <div class="container">
        <h1>Panel de Administración</h1>

        <div class="section">
            <h2>Configuración General</h2>
            <form action="save.php" method="post">
                <input type="hidden" name="action" value="general_settings">
                <label for="company_name">Nombre de la Empresa:</label>
                <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($settings['company_name']); ?>">

                <label for="page_title">Título de la Página:</label>
                <input type="text" id="page_title" name="page_title" value="<?php echo htmlspecialchars($settings['page_title']); ?>">

                <label for="slider_speed">Tiempo del Banner (ms):</label>
                <input type="number" id="slider_speed" name="slider_speed" value="<?php echo htmlspecialchars($settings['slider_speed']); ?>">

                <label for="banner_overlay_text">Texto del Banner:</label>
                <input type="text" id="banner_overlay_text" name="banner_overlay_text" value="<?php echo htmlspecialchars($settings['banner_overlay_text']); ?>">

                <label for="social_facebook">Enlace Facebook:</label>
                <input type="text" id="social_facebook" name="social_facebook" value="<?php echo htmlspecialchars($settings['social_facebook']); ?>">

                <label for="social_instagram">Enlace Instagram:</label>
                <input type="text" id="social_instagram" name="social_instagram" value="<?php echo htmlspecialchars($settings['social_instagram']); ?>">

                <label for="social_whatsapp">Enlace WhatsApp:</label>
                <input type="text" id="social_whatsapp" name="social_whatsapp" value="<?php echo htmlspecialchars($settings['social_whatsapp']); ?>">

                <button type="submit">Guardar Configuración</button>
            </form>
        </div>

        <div class="section">
            <h2>Administrar Banner</h2>
            <form action="save.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_slide">
                <h3>Añadir Nuevo Slide</h3>
                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo">
                    <option value="imagen">Imagen</option>
                    <option value="video">Video</option>
                </select>
                <label for="slide_file">Archivo (Imagen o Video):</label>
                <input type="file" name="slide_file" id="slide_file" required>
                <button type="submit">Añadir Slide</button>
            </form>
            <hr>
            <h3>Slides Actuales</h3>
            <table>
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Tipo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($slide = $slides->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($slide['ruta_archivo']); ?></td>
                            <td><?php echo htmlspecialchars($slide['tipo']); ?></td>
                            <td>
                                <form action="save.php" method="post" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_slide">
                                    <input type="hidden" name="id" value="<?php echo $slide['id']; ?>">
                                    <button type="submit" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Administrar Productos</h2>
            <form action="save.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_product">
                <h3>Añadir Nuevo Producto</h3>
                <label for="prod_nombre">Título:</label>
                <input type="text" id="prod_nombre" name="nombre" required>
                <label for="prod_desc">Descripción:</label>
                <textarea id="prod_desc" name="descripcion" required></textarea>
                <label for="prod_img">Imagen:</label>
                <input type="file" id="prod_img" name="imagen" required>
                <button type="submit">Añadir Producto</button>
            </form>
            <hr>
            <h3>Productos Existentes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Título</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = $products->fetch_assoc()): ?>
                        <tr>
                            <td><img src="../<?php echo htmlspecialchars($product['imagen_url']); ?>" alt="" width="50"></td>
                            <td><?php echo htmlspecialchars($product['nombre']); ?></td>
                            <td>
                                <form action="save.php" method="post" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_product">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Pie de Página</h2>
            <form action="save.php" method="post">
                <input type="hidden" name="action" value="footer_info">
                <label for="about_us">Sobre Nosotros:</label>
                <textarea id="about_us" name="about_us"><?php echo htmlspecialchars($footer['about_us']); ?></textarea>

                <label for="contact_phone">Teléfono:</label>
                <input type="text" id="contact_phone" name="contact_phone" value="<?php echo htmlspecialchars($footer['contact_phone']); ?>">

                <label for="contact_email">Email:</label>
                <input type="email" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($footer['contact_email']); ?>">

                <label for="contact_address">Dirección:</label>
                <input type="text" id="contact_address" name="contact_address" value="<?php echo htmlspecialchars($footer['contact_address']); ?>">

                <button type="submit">Guardar Información</button>
            </form>
        </div>
    </div>
</body>

</html>