<?php
    require_once  '../../models/conection_model/ConectionBD.php';

    class AuthenticateConection{
        private $conection;

        public function __construct(){
            $this->conection = new ConectionBD();
        }

        public function findUsser($usser){
            $conexion = $this->conection->getConexion();
            $query = "SELECT u.*, t.nombre_tipo FROM usuario u INNER JOIN tipo_usuario t ON u.rol = t.id_tipo WHERE u.nombre_usuario = ?";
            
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("s", $usser); 
            $stmt->execute();
    
            // Obtener el resultado
            $resultado = $stmt->get_result();
    
            if($resultado->num_rows > 0){
                return $resultado->fetch_assoc();
            }
    
            $stmt->close();
            // Si no se encuentra el usuario
            return null;
        }

        public function closeConection(){
            $this->conection->cerrarConexion();
        }
    }

?>