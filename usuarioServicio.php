<?php
require_once 'conextion.php';

class usuarioServicio {
    private $conexion;

    public function __construct() {
        $this->conexion = (new Conexion())->conectar();
    }

    public function validar_reserva($reservaData) {
        $result = new stdClass();
    
        // Validar los datos del formulario
        if (empty($reservaData['name']) || empty($reservaData['email']) || empty($reservaData['datetime']) || 
            empty($reservaData['select1']) || empty($reservaData['message'])) {
            $result->success = false;
            $result->message = "Todos los campos son obligatorios.";
            return $result;
        }
    
        // Recuperar los valores de la reserva
        $name = $reservaData['name'];
        $email = $reservaData['email'];
        $datetime = $reservaData['datetime'];
        $num_personas = $reservaData['select1'];
        $message = $reservaData['message'];
    
        // Preparar la consulta para insertar en la tabla de reservas
        $insertQuery = "INSERT INTO reservas (name, email, datetime, num_personas, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($insertQuery);
    
        if ($stmt === false) {
            $result->success = false;
            $result->message = "Error al preparar la consulta: " . $this->conexion->error;
            return $result;
        }
    
        // Bindear los parámetros
        $stmt->bind_param("sssis", $name, $email, $datetime, $num_personas, $message);
    
        // Ejecutar la consulta preparada
        if ($stmt->execute()) {
            $result->success = true;
            $result->message = "Reserva realizada con éxito.";
        } else {
            $result->success = false;
            $result->message = "Error al realizar la reserva: " . $stmt->error;
        }
    
        $stmt->close();
        $this->conexion->close();
        return $result;
    }

    public function validarLogin($username, $password) {
        // Preparar la consulta para obtener el usuario
        $query = "SELECT u.idusuario, u.usuario, u.password, u.tipoUsuario, p.nombres, p.apellidos 
                  FROM usuario u 
                  INNER JOIN persona p ON u.idusuario = p.idusuario 
                  WHERE u.usuario = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
    
            // Comparar contraseñas en texto plano
            if ($password === $usuario['password']) {
                return $usuario; // Credenciales válidas
            }
        }
    
        return false; // Credenciales inválidas
    }
}
?>
