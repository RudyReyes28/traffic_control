<?php
// Modelo para intersecciones
require_once '../../models/conection_model/ConectionBD.php';

class InterseccionModel {
    private $conection;
    
    public function __construct() {
        $this->conection = new ConectionBD();
    }
    
    public function agregarInterseccion($id_via1, $distancia_via1, $id_via2, $distancia_via2, $descripcion) {
        $conexion = $this->conection->getConexion();
        
        // Iniciar transacción
        $conexion->begin_transaction();
        
        try {
            // Insertar en DETALLE_VIA_INTERSECCION
            $query1 = "INSERT INTO DETALLE_VIA_INTERSECCION (id_via_interseccion_1, distancia_insterseccion_via1, 
                      id_via_inserseccion_2, distancia_interseccion_via2) 
                      VALUES (?, ?, ?, ?)";
            
            $stmt1 = $conexion->prepare($query1);
            $stmt1->bind_param("idid", $id_via1, $distancia_via1, $id_via2, $distancia_via2);
            $stmt1->execute();
            
            $id_detalle = $conexion->insert_id;
            $stmt1->close();
            
            // Insertar en INTERSECCION
            $query2 = "INSERT INTO INTERSECCION (descripcion, id_detalle_via_interserccion) VALUES (?, ?)";
            
            $stmt2 = $conexion->prepare($query2);
            $stmt2->bind_param("si", $descripcion, $id_detalle);
            $stmt2->execute();
            $stmt2->close();
            
            // Confirmar la transacción
            $conexion->commit();
            return true;
            
        } catch (Exception $e) {
            // En caso de error, revertir la transacción
            $conexion->rollback();
            return false;
        }
    }
    
    public function getAllIntersecciones() {
        $conexion = $this->conection->getConexion();
        $query = "SELECT i.*, dvi.id_via_interseccion_1, dvi.id_via_inserseccion_2, 
                 v1.nombre as via1_nombre, v1.tipo as via1_tipo, 
                 v2.nombre as via2_nombre, v2.tipo as via2_tipo
                 FROM INTERSECCION i
                 INNER JOIN DETALLE_VIA_INTERSECCION dvi ON i.id_detalle_via_interserccion = dvi.id_detalle_via_interseccion
                 INNER JOIN VIAS v1 ON dvi.id_via_interseccion_1 = v1.id_via
                 INNER JOIN VIAS v2 ON dvi.id_via_inserseccion_2 = v2.id_via
                 ORDER BY i.id_interseccion";
        
        $resultado = $conexion->query($query);
        $intersecciones = [];
        
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                if (empty($fila['descripcion'])) {
                    $fila['descripcion'] = 'Intersección de ' . $fila['via1_tipo'] . ' ' . $fila['via1_nombre'] . 
                                       ' con ' . $fila['via2_tipo'] . ' ' . $fila['via2_nombre'];
                }
                $intersecciones[] = $fila;
            }
        }
        
        return $intersecciones;
    }
    
    public function cerrarConexion() {
        $this->conection->cerrarConexion();
    }
}
?>