<?php include 'php/db_connection.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $settings_result = $conn->query("SELECT * FROM site_settings");
    $settings = [];
    while ($row = $settings_result->fetch_assoc()) {
        $settings[$row['setting_name']] = $row['setting_value'];
    }
    ?>
    <title>Contacto - <?php echo htmlspecialchars($settings['company_name']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php include 'php/header.php'; ?>

    <section class="page-banner" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('images/banner_nosotros_default.jpg');">
        <div class="banner-overlay-text">
            <h1>Contáctanos</h1>
        </div>
    </section>

    <main class="page-content">
        <div class="contact-wrapper">
            <div class="contact-form-container">
                <h3>Envíanos un Mensaje</h3>

                <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                    <p class="form-success">¡Mensaje enviado con éxito! Gracias por contactarnos.</p>
                <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
                    <p class="form-error">Hubo un error. Por favor, completa todos los campos correctamente.</p>
                <?php endif; ?>

                <form action="php/handle_contact.php" method="POST">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" required>
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required>
                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" name="mensaje" rows="6" required></textarea>
                    <button type="submit">Enviar Mensaje</button>
                </form>
            </div>
            <div class="contact-info-container">
                <h3>Nuestra Información</h3>
                <?php
                $footer_result = $conn->query("SELECT * FROM footer_info");
                $footer = [];
                // Aquí está la corrección: 'info_value' en lugar de 'setting_value'
                while ($row = $footer_result->fetch_assoc()) {
                    $footer[$row['info_key']] = $row['info_value'];
                }
                ?>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($footer['contact_address'] ?? ''); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($footer['contact_phone'] ?? ''); ?></p>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($footer['contact_email'] ?? ''); ?></p>

                <div class="map-container">
                    <?php echo $settings['google_maps_iframe']; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'php/footer.php'; ?>
</body>

</html>