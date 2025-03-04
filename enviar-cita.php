<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'constantes.php'; // Incluye el archivo con las funciones smtpGmail y smtpOutlook

// Conexión a la base de datos usando variables de entorno
$host = getenv('DB_HOST'); // Host de la base de datos
$dbname = getenv('DB_NAME'); // Nombre de la base de datos
$user = getenv('DB_USER'); // Usuario de la base de datos
$password = getenv('DB_PASSWORD'); // Contraseña de la base de datos
$port = getenv('DB_PORT') ?? '3306'; // Puerto de la base de datos (3306 por defecto)

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Función para detectar el proveedor de correo
function detectarProveedor($email) {
    if (strpos($email, '@gmail.com') !== false) {
        return 'gmail';
    } elseif (strpos($email, '@outlook.com') !== false || strpos($email, '@hotmail.com') !== false) {
        return 'outlook';
    } else {
        return 'desconocido';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"] ?? "";
    $email = $_POST["email"] ?? "";
    $message = $_POST["message"] ?? "";

    if (!empty($name) && !empty($email) && !empty($message)) {
        // Detectar el proveedor de correo
        $emailProvider = detectarProveedor($email);

        if ($emailProvider == 'desconocido') {
            die("El correo electrónico no es de Gmail ni de Outlook.");
        }

        // Guardar en la base de datos
        try {
            $stmt = $pdo->prepare("INSERT INTO contactos (nombre, correo, mensaje, proveedor) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $message, ucfirst($emailProvider)]);
        } catch (PDOException $e) {
            die("Error al guardar en la base de datos: " . $e->getMessage());
        }

        // Enviar correo de confirmación
        $subject = "CONFIRMACIÓN DE RESERVA";
        $messageBody = "Hola $name,\n\n";
        $messageBody .= "Gracias por contactarnos. Aquí están los detalles de tu reserva:\n\n";
        $messageBody .= "Nombre: $name\n";
        $messageBody .= "Correo: $email\n";
        $messageBody .= "Mensaje:\n$message\n\n";
        $messageBody .= "¡Esperamos verte pronto!\n";

        // Obtener credenciales del proveedor de correo
        $file_path = 'credenciales.json';
        if ($emailProvider == "gmail") {
            $obj = smtpGmail($file_path);
        } else if ($emailProvider == "outlook") {
            $obj = smtpOutlook($file_path);
        }

        // Configurar PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $obj->Host;
            $mail->Port = $obj->Port;
            $mail->SMTPSecure = $obj->SMTPSecure;
            $mail->SMTPAuth = true;
            $mail->Username = $obj->Username;
            $mail->Password = $obj->Password;

            $mail->setFrom($obj->Username, 'Restaurante'); // Cambia "Restaurante" por el nombre de tu negocio
            $mail->addAddress($email);

            $mail->Subject = $subject;
            $mail->Body = $messageBody;

            if ($mail->send()) {
                header("Location: send.php");
                exit();
            } else {
                header("Location: error.php");
                exit();
            }
        } catch (Exception $e) {
            die("Error al enviar el correo: " . $mail->ErrorInfo);
        }
    } else {
        die("Por favor, complete todos los campos del formulario.");
    }
} else {
    die("Acceso no autorizado.");
}
?>