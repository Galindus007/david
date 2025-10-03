<?php
// =========================================================================
// CAMBIO: Añadimos la carga de datos DENTRO de footer.php
// =========================================================================

// Usamos la conexión global que ya está abierta
global $conn; 

// Cargar la información del footer desde la base de datos
$footer_result = $conn->query("SELECT * FROM footer_info");
$footer = [];
while ($row = $footer_result->fetch_assoc()) {
    $footer[$row['info_key']] = $row['info_value'];
}

// Cargar el nombre de la empresa para el copyright (de site_settings)
$company_name_result = $conn->query("SELECT setting_value FROM site_settings WHERE setting_name = 'company_name'");
$company_name = ($company_name_result && $company_name_result->num_rows > 0) ? $company_name_result->fetch_assoc()['setting_value'] : '';

?>
<footer class="main-footer">
    <div class="footer-content">
        <div class="footer-section about">
            <h3>Sobre Nosotros</h3>
            <p><?php echo nl2br(htmlspecialchars($footer['about_us'] ?? '')); ?></p>
        </div>
        <div class="footer-section links">
            <h3>Enlaces Rápidos</h3>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="index.php#catalogo">Productos</a></li>
                <li><a href="#">Términos y Condiciones</a></li>
            </ul>
        </div>
        <div class="footer-section contact">
            <h3>Contacto</h3>
            <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($footer['contact_phone'] ?? ''); ?></p>
            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($footer['contact_email'] ?? ''); ?></p>
            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($footer['contact_address'] ?? ''); ?></p>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?php echo date("Y"); ?> | <?php echo htmlspecialchars($company_name); ?> | Todos los derechos reservados.
    </div>
</footer>

<script src="js/scripts.js"></script>