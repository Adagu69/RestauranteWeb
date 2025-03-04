<?php
require_once 'conextion.php';
require_once 'usuarioServicio.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Validar los datos del formulario de reserva
    $reservaData = array(
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'datetime' => $_POST['datetime'],
        'select1' => $_POST['select1'],
        'message' => $_POST['message']
    );

    $service = new usuarioServicio();
    $resultadoReserva = $service->validar_reserva($reservaData);

    if ($resultadoReserva->success) {
        // La reserva se realizó correctamente, redirigir a la página de reserva exitosa
        header("Location: reserva_exitosa.php");
        exit(); // Asegúrate de salir del script después de redirigir
    } else {
        // Hubo un error al hacer la reserva, puedes manejarlo aquí
        echo "Error al hacer la reserva: {$resultadoReserva->message}";
    }
}

// Si no es una solicitud POST, mostrar el formulario
require_once 'reserva_view.php';
?>
