<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'constantes.php';

// Conexión a la base de datos
$host = 'localhost:3307'; // Cambia si es necesario
$dbname = 'restaurante'; // Cambia por el nombre de tu base de datos
$user = 'root'; // Cambia por tu usuario de MySQL
$password = ''; // Cambia por tu contraseña de MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"] ?? "";
    $email = $_POST["email"] ?? "";
    $message = $_POST["message"] ?? "";
    $emailProvider = $_POST["emailProvider"] ?? "";

    if (!empty($name) && !empty($email) && !empty($message) && !empty($emailProvider)) {
        // Guardar en la base de datos
        try {
            $stmt = $pdo->prepare("INSERT INTO contactos (nombre, correo, mensaje, proveedor) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $message, ucfirst($emailProvider)]); // ucfirst convierte la primera letra en mayúscula
        } catch (PDOException $e) {
            die("Error al guardar en la base de datos: " . $e->getMessage());
        }

        // Enviar correo electrónico
        $subject = "Nuevo mensaje de contacto";
        $messageBody = "Nombre: $name\n";
        $messageBody .= "Correo: $email\n";
        $messageBody .= "Proveedor de correo: $emailProvider\n";
        $messageBody .= "Mensaje:\n$message\n";

        // Obtener credenciales del proveedor de correo
        $file_path = 'credenciales.json';
        if ($emailProvider == "gmail") {
            $obj = smtpGmail($file_path);
        } else if ($emailProvider == "outlook") {
            $obj = smtpOutlook($file_path);
        } else {
            die("Proveedor de correo no válido.");
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

            $mail->setFrom($obj->Username, 'Remitente');
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
