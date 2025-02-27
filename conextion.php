<?php
class Conexion{
    public $servername = "localhost:3307";
public $username = "root";
public $password = "";
public $dbname = "restaurante";

public function conectar() {
    $conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

}
?>