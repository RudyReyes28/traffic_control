<?php
// Modelo para usuarios
require_once '../../models/conection_model/ConectionBD.php';

class UsuarioModel {
    private $conection;
    
    public function __construct() {
        $this->conection = new ConectionBD();
    }
    
    public function agregarUsuario($nombre, $apellido, $nombre_usuario, $contrasenia, $rol) {
        $conexion = $this->conection->getConexion();
        $query = "INSERT INTO USUARIO (Nombre, Apellido, nombre_usuario, contrasenia, rol) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ssssi", $nombre, $apellido, $nombre_usuario, $contrasenia, $rol);
        
        $resultado = $stmt->execute();
        $stmt->close();
        
        return $resultado;
    }
    
    public function checkUsuarioExists($nombre_usuario) {
        $conexion = $this->conection->getConexion();
        $query = "SELECT COUNT(*) as total FROM USUARIO WHERE nombre_usuario = ?";
        
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $nombre_usuario);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        
        $stmt->close();
        
        return ($fila['total'] > 0);
    }
    
    public function getAllUsuarios() {
        $conexion = $this->conection->getConexion();
        $query = "SELECT u.*, t.nombre_tipo FROM USUARIO u 
                  INNER JOIN TIPO_USUARIO t ON u.rol = t.id_tipo 
                  ORDER BY u.ID";
        
        $resultado = $conexion->query($query);
        $usuarios = [];
        
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $usuarios[] = $fila;
            }
        }
        
        return $usuarios;
    }
    
    public function cerrarConexion() {
        $this->conection->cerrarConexion();
    }
}
?>