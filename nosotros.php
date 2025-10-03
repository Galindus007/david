<?php
include 'php/db_connection.php';

// Cargar datos del header/footer para consistencia
$settings_result = $conn->query("SELECT * FROM site_settings");
$settings = [];
while ($row = $settings_result->fetch_assoc()) {
    $settings[$row['setting_name']] = $row['setting_value'];
}

// Cargar el contenido específico de la página "Nosotros"
$about_result = $conn->query("SELECT * FROM about_us_content");
$about_content = [];
while ($row = $about_result->fetch_assoc()) {
    $about_content[$row['content_key']] = $row['content_value'];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($about_content['page_title']); ?> - <?php echo htmlspecialchars($settings['company_name']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php include 'php/header.php'; ?>

    <section class="page-banner" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('<?php echo htmlspecialchars($about_content['main_image_url']); ?>');">
        <div class="banner-overlay-text">
            <h1><?php echo htmlspecialchars($about_content['page_title']); ?></h1>
        </div>
    </section>

    <main class="page-content">
        <div class="content-wrapper intro-section">
            <p><?php echo nl2br(htmlspecialchars($about_content['main_text'])); ?></p>
        </div>

        <div class="features-section">
            <div class="feature-item">
                <i class="fas fa-bullseye"></i>
                <h3><?php echo htmlspecialchars($about_content['mission_title']); ?></h3>
                <p><?php echo htmlspecialchars($about_content['mission_text']); ?></p>
            </div>
            <div class="feature-item">
                <i class="fas fa-eye"></i>
                <h3><?php echo htmlspecialchars($about_content['vision_title']); ?></h3>
                <p><?php echo htmlspecialchars($about_content['vision_text']); ?></p>
            </div>
            <div class="feature-item">
                <i class="fas fa-heart"></i>
                <h3><?php echo htmlspecialchars($about_content['values_title']); ?></h3>
                <p><?php echo htmlspecialchars($about_content['values_text']); ?></p>
            </div>
        </div>

        <div class="alternating-layout">
            <div class="image-column">
                <img src="<?php echo htmlspecialchars($about_content['secondary_image_url']); ?>" alt="Imagen secundaria de la empresa">
            </div>
            <div class="text-column">
                <p><?php echo nl2br(htmlspecialchars($about_content['secondary_text'])); ?></p>
            </div>
        </div>
    </main>

    <?php include 'php/footer.php'; ?>
</body>

</html>