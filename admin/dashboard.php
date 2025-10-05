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

        <!-- Inicio Configuración General -->
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

                <label for="google_maps">Código de Google Maps:</label>
                <textarea id="google_maps" name="google_maps_iframe" rows="4"><?php echo htmlspecialchars($settings['google_maps_iframe']); ?></textarea>

                <button type="submit">Guardar Configuración</button>
            </form>
        </div>
        <!-- Fin Configuración General -->

        <!-- Inicio Administrar Categorías del Menú -->
        <div class="section">
            <h2>Administrar Categorías del Menú</h2>

            <form action="save.php" method="post">
                <input type="hidden" name="action" value="add_category">
                <h3>Añadir Nueva Categoría</h3>
                <label for="cat_nombre">Nombre de la Categoría:</label>
                <input type="text" id="cat_nombre" name="nombre" required>
                <label for="cat_orden">Orden (número más bajo aparece primero):</label>
                <input type="number" id="cat_orden" name="orden" value="0">
                <button type="submit">Añadir Categoría</button>
            </form>
            <hr>

            <h3>Categorías Actuales</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Orden</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Necesitamos cargar las categorías al principio del archivo
                    $categories_result = $conn->query("SELECT * FROM product_categories ORDER BY orden ASC");
                    while ($category = $categories_result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($category['orden']); ?></td>
                            <td>
                                <form action="save.php" method="post" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_category">
                                    <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                    <button type="submit" onclick="return confirm('¿Estás seguro? Esto eliminará la categoría del menú.')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <!-- Fin Administrar Categorías -->

        <!-- Inicio Administrar Banner -->
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
        <!-- Fin Administrar Banner -->

        <!-- Inicio Administrar Productos -->
        <div class="section">
            <h2>Administrar Productos</h2>
            <form action="save.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_product">
                <h3>Añadir Nuevo Producto</h3>

                <label for="prod_nombre">Título:</label>
                <input type="text" id="prod_nombre" name="nombre" required>

                <label for="prod_categoria">Categoría:</label>
                <select name="category_id" id="prod_categoria" required>
                    <option value="">-- Selecciona una categoría --</option>
                    <?php
                    // Volvemos a cargar las categorías para usarlas en el desplegable
                    $categories_for_select = $conn->query("SELECT * FROM product_categories ORDER BY nombre ASC");
                    while ($cat = $categories_for_select->fetch_assoc()):
                    ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="prod_desc">Descripción (corta, para el catálogo):</label>
                <textarea id="prod_desc" name="descripcion" required></textarea>

                <label for="prod_detalles">Detalles Completos (para la página del producto):</label>
                <textarea id="prod_detalles" name="detalles" rows="6"></textarea>

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
            <th>Título del Producto</th>
            <th>Categoría</th>
            <th>Acciones</th> </tr>
    </thead>
    <tbody>
        <?php
        $products_list_query = "
            SELECT p.id, p.nombre, p.imagen_url, c.nombre AS categoria_nombre 
            FROM productos p 
            LEFT JOIN product_categories c ON p.category_id = c.id 
            ORDER BY p.id DESC
        ";
        $products_result = $conn->query($products_list_query);

        while($product = $products_result->fetch_assoc()): 
        ?>
            <tr>
                <td><img src="../<?php echo htmlspecialchars($product['imagen_url']); ?>" alt="" width="50" height="50" style="object-fit: cover;"></td>
                <td><?php echo htmlspecialchars($product['nombre']); ?></td>
                <td><?php echo htmlspecialchars($product['categoria_nombre'] ?? 'Sin categoría'); ?></td>
                <td>
                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="edit-btn">Editar</a>
                    
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

<style>.edit-btn { background: #ffc107; color: black; padding: 5px 10px; text-decoration: none; border-radius: 4px; margin-right: 5px; font-size: 0.9em; }</style>
        </div>
        <!-- Fin Productos -->

        <!-- Pie de Página -->
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
        <!-- Fin Pie de Página -->

        <!-- Inicio Página "Nosotros" -->
        <div class="section">
            <h2>Página "Nosotros"</h2>
            <form action="save.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_about_us">

                <?php
                $about_result = $conn->query("SELECT * FROM about_us_content");
                $about_content = [];
                while ($row = $about_result->fetch_assoc()) {
                    $about_content[$row['content_key']] = $row['content_value'];
                }
                ?>

                <h3>Contenido Principal</h3>
                <label for="about_title">Título de la Página:</label>
                <input type="text" id="about_title" name="page_title" value="<?php echo htmlspecialchars($about_content['page_title']); ?>">

                <label for="about_text">Texto de Introducción (Historia):</label>
                <textarea id="about_text" name="main_text" rows="8"><?php echo htmlspecialchars($about_content['main_text']); ?></textarea>

                <label for="about_image">Imagen de Cabecera Principal:</label>
                <input type="file" id="about_image" name="main_image">
                <p style="font-size: 0.8em; margin-bottom: 1rem;">Sube una nueva imagen para reemplazar la cabecera actual: <?php echo htmlspecialchars($about_content['main_image_url']); ?></p>

                <hr style="margin: 2rem 0;">

                <h3>Sección Misión, Visión y Valores</h3>
                <label for="mission_title">Título Misión:</label>
                <input type="text" id="mission_title" name="mission_title" value="<?php echo htmlspecialchars($about_content['mission_title']); ?>">
                <label for="mission_text">Texto Misión:</label>
                <textarea id="mission_text" name="mission_text" rows="3"><?php echo htmlspecialchars($about_content['mission_text']); ?></textarea>

                <label for="vision_title">Título Visión:</label>
                <input type="text" id="vision_title" name="vision_title" value="<?php echo htmlspecialchars($about_content['vision_title']); ?>">
                <label for="vision_text">Texto Visión:</label>
                <textarea id="vision_text" name="vision_text" rows="3"><?php echo htmlspecialchars($about_content['vision_text']); ?></textarea>

                <label for="values_title">Título Valores:</label>
                <input type="text" id="values_title" name="values_title" value="<?php echo htmlspecialchars($about_content['values_title']); ?>">
                <label for="values_text">Texto Valores:</label>
                <textarea id="values_text" name="values_text" rows="3"><?php echo htmlspecialchars($about_content['values_text']); ?></textarea>

                <hr style="margin: 2rem 0;">

                <h3>Sección Secundaria</h3>
                <label for="secondary_text">Texto Adicional:</label>
                <textarea id="secondary_text" name="secondary_text" rows="6"><?php echo htmlspecialchars($about_content['secondary_text']); ?></textarea>

                <label for="secondary_image">Imagen Secundaria:</label>
                <input type="file" id="secondary_image" name="secondary_image">
                <p style="font-size: 0.8em; margin-bottom: 1rem;">Imagen para la sección de texto adicional: <?php echo htmlspecialchars($about_content['secondary_image_url']); ?></p>

                <button type="submit">Guardar Información de "Nosotros"</button>
            </form>
        </div>
        <!-- Fin Página "Nosotros" -->

        <!-- Inicio Mensajes Recibidos -->
        <div class="section">
            <h2>Mensajes Recibidos</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Mensaje</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $messages_result = $conn->query("SELECT * FROM contact_messages ORDER BY fecha_envio DESC");
                    while ($msg = $messages_result->fetch_assoc()):
                    ?>
                        <tr style="<?php if ($msg['leido'] == 0) echo 'font-weight: bold;'; ?>">
                            <td><?php echo htmlspecialchars($msg['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><?php echo htmlspecialchars($msg['mensaje']); ?></td>
                            <td><?php echo $msg['fecha_envio']; ?></td>
                            <td>
                                <?php if ($msg['leido'] == 0): ?>
                                    <form action="save.php" method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="mark_as_read">
                                        <input type="hidden" name="id" value="<?php echo $msg['id']; ?>">
                                        <button type="submit">Marcar como leído</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <!-- Fin Mensajes Recibidos -->

    </div>
</body>

</html>

</div>
</body>

</html>