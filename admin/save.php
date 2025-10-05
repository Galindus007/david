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
            $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, detalles, category_id, imagen_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssis", $_POST['nombre'], $_POST['descripcion'], $_POST['detalles'], $_POST['category_id'], $imagen_url);
            $stmt->execute();
        }
        break;

    case 'delete_product':
        $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        break;
    // -- EDITAR PRODUCTO --
    case 'edit_product':
        $product_id = intval($_POST['id'] ?? 0);
        if ($product_id > 0) {
            // Manejo de la imagen: solo actualizamos si se sube una nueva
            $new_image_url = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $new_image_url = upload_file('imagen');
            }
            
            if ($new_image_url) {
                // Si hay nueva imagen, la consulta la incluye (6 variables)
                $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, detalles=?, category_id=?, imagen_url=? WHERE id=?");
                $stmt->bind_param("sssisi", $_POST['nombre'], $_POST['descripcion'], $_POST['detalles'], $_POST['category_id'], $new_image_url, $product_id);
            } else {
                // Si NO hay nueva imagen, la consulta no la toca (5 variables)
                $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, detalles=?, category_id=? WHERE id=?");
                // AQUÍ ESTÁ LA CORRECCIÓN: "sssi" se convierte en "sssii"
                $stmt->bind_param("sssii", $_POST['nombre'], $_POST['descripcion'], $_POST['detalles'], $_POST['category_id'], $product_id);
            }
            $stmt->execute();
        }
        break;
    // --- FIN EDITAR PRODUCTO ---
    // --- FOOTER INFO ---
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
    // --- FIN FOOTER INFO ---
    // --- ABOUT US ---
    case 'save_about_us':
        // Manejar la subida de la imagen principal
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            $imagen_url = upload_file('main_image');
            if ($imagen_url) {
                $stmt_img = $conn->prepare("UPDATE about_us_content SET content_value = ? WHERE content_key = 'main_image_url'");
                $stmt_img->bind_param("s", $imagen_url);
                $stmt_img->execute();
            }
        }

        // Manejar la subida de la imagen secundaria
        if (isset($_FILES['secondary_image']) && $_FILES['secondary_image']['error'] === UPLOAD_ERR_OK) {
            $imagen_url_sec = upload_file('secondary_image');
            if ($imagen_url_sec) {
                $stmt_img_sec = $conn->prepare("UPDATE about_us_content SET content_value = ? WHERE content_key = 'secondary_image_url'");
                $stmt_img_sec->bind_param("s", $imagen_url_sec);
                $stmt_img_sec->execute();
            }
        }

        // Actualizar todos los campos de texto
        $stmt_text = $conn->prepare("UPDATE about_us_content SET content_value = ? WHERE content_key = ?");
        $stmt_text->bind_param("ss", $value, $key);

        $text_fields = ['page_title', 'main_text', 'mission_title', 'mission_text', 'vision_title', 'vision_text', 'values_title', 'values_text', 'secondary_text'];

        foreach ($text_fields as $field) {
            if (isset($_POST[$field])) {
                $key = $field;
                $value = $_POST[$field];
                $stmt_text->execute();
            }
        }
        break;

    // Para guardar el mapa
    case 'general_settings':
        $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_name = ?");
        $stmt->bind_param("ss", $value, $key);
        // ... (todas las líneas que ya tenías) ...
        $key = 'google_maps_iframe';
        $value = $_POST['google_maps_iframe'];
        $stmt->execute();
        break;

    // Nuevo case para marcar mensajes como leídos
    case 'mark_as_read':
        $id = $_POST['id'] ?? 0;
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE contact_messages SET leido = 1 WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        break;
        
    } // Cierre del switch


// Redirigir de vuelta al dashboard
header('Location: dashboard.php');
exit;
