<?php
// ==================================================================
// ESTE BLOQUE CARGA TODO EL CONTENIDO DESDE LA BASE DE DATOS
// ==================================================================
include 'php/db_connection.php';

// Cargar configuraciones generales
$settings_result = $conn->query("SELECT * FROM site_settings");
$settings = [];
while ($row = $settings_result->fetch_assoc()) {
    $settings[$row['setting_name']] = $row['setting_value'];
}

// Cargar slides del banner
$slides = $conn->query("SELECT * FROM banner_slides ORDER BY orden ASC");

// Cargar info del footer
$footer_result = $conn->query("SELECT * FROM footer_info");

// Cargar los nombres de los productos para el menú
$products_for_menu = $conn->query("SELECT id, nombre FROM productos ORDER BY nombre ASC");


$footer = [];
while ($row = $footer_result->fetch_assoc()) {
    $footer[$row['info_key']] = $row['info_value'];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($settings['page_title']); ?> | <?php echo htmlspecialchars($settings['company_name']); ?></title>

    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php include 'php/header.php'; ?>
           
    <section class="main-banner">
        <div class="slider" data-speed="<?php echo htmlspecialchars($settings['slider_speed']); ?>">

            <?php if ($slides && $slides->num_rows > 0): ?>
                <?php $is_first = true; ?>
                <?php while ($slide = $slides->fetch_assoc()): ?>
                    <div class="slide <?php if ($is_first) {
                                            echo 'active';
                                            $is_first = false;
                                        } ?>">
                        <?php if ($slide['tipo'] === 'video'): ?>
                            <video autoplay loop muted playsinline>
                                <source src="<?php echo htmlspecialchars($slide['ruta_archivo']); ?>" type="video/mp4">
                                Tu navegador no soporta la etiqueta de video.
                            </video>
                        <?php else: // Es 'imagen' 
                        ?>
                            <div style="width:100%; height:100%; background-image: url('<?php echo htmlspecialchars($slide['ruta_archivo']); ?>'); background-size:cover; background-position:center;"></div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="slide active" style="background-image: url('images/banner_default.jpg');"></div>
            <?php endif; ?>
        </div>
        <div class="banner-overlay">
            <h2><?php echo htmlspecialchars($settings['banner_overlay_text']); ?></h2>
        </div>
    </section>

    <main class="product-catalog" id="catalogo">
        <h2>Nuestro Catálogo</h2>
        <div class="product-grid">
            <?php include 'php/load_products.php'; ?>
        </div>
    </main>


    <?php include 'php/footer.php'; ?>

    <script src="js/scripts.js"></script>
</body>

</html>