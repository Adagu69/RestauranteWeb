<?php
class Conexion {
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $port;

    public function __construct() {
        // Obtener las variables de entorno para la conexión a la base de datos
        $this->servername = getenv('DB_HOST');
        $this->username = getenv('DB_USER');
        $this->password = getenv('DB_PASSWORD');
        $this->dbname = getenv('DB_NAME');
        $this->port = getenv('DB_PORT') ?? 3306; // Usar el puerto de la variable de entorno o 3306 por defecto
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