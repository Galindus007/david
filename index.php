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
    <div class="top-bar">
        <div class="social-media">
            <a href="<?php echo htmlspecialchars($settings['social_facebook']); ?>" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="<?php echo htmlspecialchars($settings['social_instagram']); ?>" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="<?php echo htmlspecialchars($settings['social_whatsapp']); ?>" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        </div>
        <div class="login">
            <a href="./admin/">Login</a>
        </div>
    </div>

    <header class="main-header">
        <div class="logo">
            <img src="images/logo.png" alt="Logo de la Empresa">
            <h1><?php echo htmlspecialchars($settings['company_name']); ?></h1>
        </div>
        <nav>
            <button class="hamburger" id="hamburger-icon" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul class="nav-menu" id="nav-menu">
                <li><a href="#">Inicio</a></li>
                <li class="dropdown">
                    <a href="#catalogo" class="dropbtn">Productos <i class="fas fa-chevron-down"></i></a>
                    <div class="dropdown-content">
                        <?php if ($products_for_menu && $products_for_menu->num_rows > 0): ?>
                            <?php while ($product_item = $products_for_menu->fetch_assoc()): ?>
                                <a href="producto.php?id=<?php echo $product_item['id']; ?>">
                                    <?php echo htmlspecialchars($product_item['nombre']); ?>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <a href="#">No hay productos</a>
                        <?php endif; ?>
                    </div>
                </li>
                <li><a href="#">Nosotros</a></li>
                <li><a href="#">Contacto</a></li>
            </ul>
        </nav>
    </header>

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

    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-section about">
                <h3>Sobre Nosotros</h3>
                <p><?php echo nl2br(htmlspecialchars($footer['about_us'])); ?></p>
            </div>
            <div class="footer-section links">
                <h3>Enlaces Rápidos</h3>
                <ul>
                    <li><a href="#">Inicio</a></li>
                    <li><a href="#catalogo">Productos</a></li>
                    <li><a href="#">Términos y Condiciones</a></li>
                </ul>
            </div>
            <div class="footer-section contact">
                <h3>Contacto</h3>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($footer['contact_phone']); ?></p>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($footer['contact_email']); ?></p>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($footer['contact_address']); ?></p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo date("Y"); ?> | <?php echo htmlspecialchars($settings['company_name']); ?> | Todos los derechos reservados.
        </div>
    </footer>

    <script src="js/scripts.js"></script>
</body>

</html>