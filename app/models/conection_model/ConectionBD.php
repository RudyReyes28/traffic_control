<?php
class ConectionBD{
    private $conexion;

    public function __construct() {
        $host = 'localhost'; 
        $usuario = 'root';
        $password = '1234';
        $base_datos = 'monitoreo_trafico';

        $this->conexion = new mysqli($host, $usuario, $password, $base_datos);

        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    }

    public function getConexion() {
        return $this->conexion;
    }


    public function cerrarConexion() {
        $this->conexion->close();
    }
}


?>