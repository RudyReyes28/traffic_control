<?php
// Modelo para vías
require_once '../../models/conection_model/ConectionBD.php';

class ViasModel {
    private $conection;
    
    public function __construct() {
        $this->conection = new ConectionBD();
    }
    
    public function agregarVia($nombre, $tipo, $longitud, $inicio_via, $fin_via, $is_doble_sentido) {
        $conexion = $this->conection->getConexion();
        $query = "INSERT INTO VIAS (nombre, tipo, longitud, inicio_via, fin_via, is_doble_sentido) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ssdssi", $nombre, $tipo, $longitud, $inicio_via, $fin_via, $is_doble_sentido);
        
        $resultado = $stmt->execute();
        $stmt->close();
        
        return $resultado;
    }
    
    public function getAllVias() {
        $conexion = $this->conection->getConexion();
        $query = "SELECT * FROM VIAS ORDER BY tipo, nombre";
        
        $resultado = $conexion->query($query);
        $vias = [];
        
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $vias[] = $fila;
            }
        }
        
        return $vias;
    }
    
    public function getViaById($id_via) {
        $conexion = $this->conection->getConexion();
        $query = "SELECT * FROM VIAS WHERE id_via = ?";
        
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("i", $id_via);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $via = null;
        
        if ($resultado && $resultado->num_rows > 0) {
            $via = $resultado->fetch_assoc();
        }
        
        $stmt->close();
        
        return $via;
    }
    
    public function cerrarConexion() {
        $this->conection->cerrarConexion();
    }
}
?>