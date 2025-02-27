<?php
class Conexion {
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $port;

    public function __construct() {
        // Obtener la URL de conexión desde Railway
        $DATABASE_URL = getenv('DATABASE_URL');

        if ($DATABASE_URL) {
            $dbparts = parse_url($DATABASE_URL);
            $this->servername = $dbparts['host'];
            $this->username = $dbparts['user'];
            $this->password = $dbparts['pass'];
            $this->dbname = ltrim($dbparts['path'], '/');
            $this->port = $dbparts['port'] ?? 3306; // Usar el puerto de la URL o 3306 por defecto
        } else {
            // Configuración local (solo para pruebas en tu PC)
            $this->servername = "localhost";
            $this->username = "root";
            $this->password = "";
            $this->dbname = "restaurante";
            $this->port = 3307; // Cambia según tu configuración local
        }
    }

    public function conectar() {
        $conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname, $this->port);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }
}
?>