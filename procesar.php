<?php
session_start();
require_once 'usuarioServicio.php';

echo "Procesando formulario...<br>"; // Mensaje de depuración

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    echo "Usuario: $username<br>"; // Mensaje de depuración
    echo "Contraseña: $password<br>"; // Mensaje de depuración

    if (!empty($username) && !empty($password)) {
        $servicio = new usuarioServicio();
        $usuario = $servicio->validarLogin($username, $password);

        if ($usuario) {
            echo "Credenciales válidas<br>"; // Mensaje de depuración
            // Credenciales válidas: crear sesión
            $_SESSION['ingreso'] = true;
            $_SESSION['usuario'] = (object)[
                'idusuario' => $usuario['idusuario'],
                'usuario' => $usuario['usuario'],
                'tipoUsuario' => $usuario['tipoUsuario'],
                'nombres' => $usuario['nombres'],
                'apellidos' => $usuario['apellidos']
            ];

            // Redirigir al usuario según su tipo
            if ($usuario['tipoUsuario'] == 'admin') {
                header("Location: admin.php"); // Página de administrador
            } else {
                header("Location: index.php"); // Página principal para clientes
            }
            exit();
        } else {
            echo "Credenciales inválidas<br>"; // Mensaje de depuración
            // Credenciales inválidas
            $_SESSION['error'] = true;
            header("Location: login.php");
            exit();
        }
    } else {
        echo "Campos vacíos<br>"; // Mensaje de depuración
        // Campos vacíos
        $_SESSION['error'] = true;
        header("Location: login.php");
        exit();
    }
} else {
    echo "Acceso no autorizado<br>"; // Mensaje de depuración
    // Acceso no autorizado
    header("Location: login.php");
    exit();
}
?>