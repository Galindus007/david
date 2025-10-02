<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Acceso no autorizado.");
}

include '../php/db_connection.php';

$action = $_POST['action'] ?? '';

// Función para subir archivos de forma segura
function upload_file($file_input_name, $upload_dir = '../uploads/')
{
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES[$file_input_name]['tmp_name'];
        $file_name = $_FILES[$file_input_name]['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $new_file_name = uniqid('', true) . '.' . $file_ext;
        $dest_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp_path, $dest_path)) {
            return 'uploads/' . $new_file_name; // Devuelve la ruta relativa para guardarla en la BD
        }
    }
    return null;
}

switch ($action) {
    case 'general_settings':
        $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_name = ?");
        $stmt->bind_param("ss", $value, $key);

        $key = 'company_name';
        $value = $_POST['company_name'];
        $stmt->execute();
        $key = 'page_title';
        $value = $_POST['page_title'];
        $stmt->execute();
        $key = 'slider_speed';
        $value = $_POST['slider_speed'];
        $stmt->execute();
        // ... existing $stmt->execute() lines ...
        $key = 'banner_overlay_text';
        $value = $_POST['banner_overlay_text'];
        $stmt->execute();
        $key = 'social_facebook';
        $value = $_POST['social_facebook'];
        $stmt->execute();
        $key = 'social_instagram';
        $value = $_POST['social_instagram'];
        $stmt->execute();
        $key = 'social_whatsapp';
        $value = $_POST['social_whatsapp'];
        $stmt->execute();
        break;

    case 'add_slide':
        $ruta_archivo = upload_file('slide_file');
        if ($ruta_archivo) {
            $stmt = $conn->prepare("INSERT INTO banner_slides (tipo, ruta_archivo) VALUES (?, ?)");
            $stmt->bind_param("ss", $_POST['tipo'], $ruta_archivo);
            $stmt->execute();
        }
        break;

    case 'delete_slide':
        // Opcional: eliminar el archivo del servidor
        // $stmt = $conn->prepare("SELECT ruta_archivo FROM banner_slides WHERE id = ?"); ... unlink('../' . $ruta);
        $stmt = $conn->prepare("DELETE FROM banner_slides WHERE id = ?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        break;

    // --- AÑADIR ESTOS DOS NUEVOS BLOQUES ---
    case 'add_category':
        $nombre = $_POST['nombre'] ?? '';
        $orden = $_POST['orden'] ?? 0;
        if (!empty($nombre)) {
            $stmt = $conn->prepare("INSERT INTO product_categories (nombre, orden) VALUES (?, ?)");
            $stmt->bind_param("si", $nombre, $orden);
            $stmt->execute();
        }
        break;

    case 'delete_category':
        $id = $_POST['id'] ?? 0;
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM product_categories WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        break;
    // --- FIN DE LOS BLOQUES NUEVOS ---

    // Busca este case en tu switch
    case 'add_product':
        $imagen_url = upload_file('imagen');
        if ($imagen_url) {
            // CAMBIO: Añadimos 'detalles' a la consulta
            $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, detalles, imagen_url) VALUES (?, ?, ?, ?)");
            // CAMBIO: El tipo de parámetro cambia de "sss" a "ssss" y añadimos la variable
            $stmt->bind_param("ssss", $_POST['nombre'], $_POST['descripcion'], $_POST['detalles'], $imagen_url);
            $stmt->execute();
        }
        break;

    case 'delete_product':
        $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        break;

    case 'footer_info':
        $stmt = $conn->prepare("UPDATE footer_info SET info_value = ? WHERE info_key = ?");
        $stmt->bind_param("ss", $value, $key);

        $key = 'about_us';
        $value = $_POST['about_us'];
        $stmt->execute();
        $key = 'contact_phone';
        $value = $_POST['contact_phone'];
        $stmt->execute();
        $key = 'contact_email';
        $value = $_POST['contact_email'];
        $stmt->execute();
        $key = 'contact_address';
        $value = $_POST['contact_address'];
        $stmt->execute();
        break;
}

// Redirigir de vuelta al dashboard
header('Location: dashboard.php');
exit;
