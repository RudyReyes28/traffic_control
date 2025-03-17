<?php
// Modelo para semáforos
require_once '../../models/conection_model/ConectionBD.php';

class SemaforoModel {
    private $conection;
    
    public function __construct() {
        $this->conection = new ConectionBD();
    }
    
    public function agregarSemaforo($id_interseccion, $posicion_salida, $posicion_alto, $estado_operativo) {
        $conexion = $this->conection->getConexion();
        $query = "INSERT INTO SEMAFORO (id_interseccion, posicion_salida, posicion_alto, estado_operativo) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("isss", $id_interseccion, $posicion_salida, $posicion_alto, $estado_operativo);
        
        $resultado = $stmt->execute();
        $stmt->close();
        
        return $resultado;
    }
    
    public function getAllSemaforos() {
        $conexion = $this->conection->getConexion();
        $query = "SELECT s.*, i.descripcion as interseccion_descripcion,
                 v1.nombre as via1_nombre, v1.tipo as via1_tipo,
                 v2.nombre as via2_nombre, v2.tipo as via2_tipo
                 FROM SEMAFORO s
                 INNER JOIN INTERSECCION i ON s.id_interseccion = i.id_interseccion
                 INNER JOIN DETALLE_VIA_INTERSECCION dvi ON i.id_detalle_via_interserccion = dvi.id_detalle_via_interseccion
                 INNER JOIN VIAS v1 ON dvi.id_via_interseccion_1 = v1.id_via
                 INNER JOIN VIAS v2 ON dvi.id_via_inserseccion_2 = v2.id_via
                 ORDER BY s.id_semaforo";
        
        $resultado = $conexion->query($query);
        $semaforos = [];
        
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $semaforos[] = $fila;
            }
        }
        
        return $semaforos;
    }
    
    public function getSemaforosByInterseccion($id_interseccion) {
        $conexion = $this->conection->getConexion();
        $query = "SELECT * FROM SEMAFORO WHERE id_interseccion = ? ORDER BY id_semaforo";
        
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("i", $id_interseccion);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $semaforos = [];
        
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $semaforos[] = $fila;
            }
        }
        
        $stmt->close();
        
        return $semaforos;
    }
    
    public function cerrarConexion() {
        $this->conection->cerrarConexion();
    }
}
?>