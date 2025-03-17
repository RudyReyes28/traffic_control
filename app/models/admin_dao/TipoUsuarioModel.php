<?php
// Modelo para tipo de usuario
require_once '../../models/conection_model/ConectionBD.php';

class TipoUsuarioModel {
    private $conection;
    
    public function __construct() {
        $this->conection = new ConectionBD();
    }
    
    public function getAllTiposUsuario() {
        $conexion = $this->conection->getConexion();
        $query = "SELECT * FROM TIPO_USUARIO ORDER BY id_tipo";
        
        $resultado = $conexion->query($query);
        $tipos = [];
        
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $tipos[] = $fila;
            }
        }
        
        return $tipos;
    }
    
    public function cerrarConexion() {
        $this->conection->cerrarConexion();
    }
}
?>