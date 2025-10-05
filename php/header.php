<?php
// Usamos la conexión global que ya abrió el archivo principal (index.php, etc.)
global $conn;
global $settings; // También necesitamos $settings para los enlaces

// Cargamos los datos que ESTE COMPONENTE necesita (las categorías del menú)
$categories_result = $conn->query("SELECT * FROM product_categories ORDER BY orden ASC");
?>
<div class="top-bar">
    <div class="social-media">
        <a href="<?php echo htmlspecialchars($settings['social_facebook'] ?? '#'); ?>" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="<?php echo htmlspecialchars($settings['social_instagram'] ?? '#'); ?>" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="<?php echo htmlspecialchars($settings['social_whatsapp'] ?? '#'); ?>" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
    </div>
    <div class="login">
        <a href="admin/">Login</a>
    </div>
</div>

<header class="main-header">
    <div class="logo">
        <a href="index.php"><img src="images/logo.png" alt="Logo de la Empresa"></a>
        <a href="index.php" style="text-decoration:none;"><h1><?php echo htmlspecialchars($settings['company_name'] ?? 'Mi Empresa'); ?></h1></a>
    </div>
    <nav>
        <button class="hamburger" id="hamburger-icon" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <ul class="nav-menu" id="nav-menu">
            <li><a href="index.php">Inicio</a></li>
            <li class="dropdown">
                <a href="index.php#catalogo" class="dropbtn">Productos <i class="fas fa-chevron-down"></i></a>
                <div class="dropdown-content">
                    <?php if ($categories_result && $categories_result->num_rows > 0): ?>
                        <?php while($category = $categories_result->fetch_assoc()): ?>
                            <a href="categoria.php?id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['nombre']); ?></a>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </li>
            <li><a href="nosotros.php">Nosotros</a></li>
            <li><a href="contacto.php">Contacto</a></li>
        </ul>
    </nav>
</header>