<?php
require_once '../../models/conection_model/ConectionBD.php';

class ResultsProcessor {
    private $connection;
    private $userId;
    private $data;
    
    private $semaphoreIds = [
        'right' => 1,
        'left' => 2,
        'down' => 3,
        'up' => 4
    ];
    
    public function __construct($userId) {
        $this->connection = new ConectionBD();
        $this->userId = $userId;
    }
    
    public function processData($jsonData) {
        $this->data = $jsonData;
        
        // Begin transaction
        $conn = $this->connection->getConexion();
        $conn->begin_transaction();
        
        try {
            if ($this->data['metadatos']['es_nueva_prueba']) {
                $prueba_id = $this->createNewTest();
            } else {
                $prueba_id = $this->getLastTestId();
            }
            
            $iteracion_id = $this->createIteration($prueba_id);
            
            $this->processSemaphoreData($iteracion_id);
            
            $resultado_id = $this->createTestResult($iteracion_id);
            
            $this->processSemaphoreMonitoringResults($resultado_id);
            
            if ($this->data['metadatos']['modo'] === 'manual') {
                $this->processVehicles($prueba_id);
            }
            
            $conn->commit();
            
            return [
                'status' => 'success',
                'message' => 'Datos guardados correctamente',
                'prueba_id' => $prueba_id,
                'iteracion_id' => $iteracion_id
            ];
            
        } catch (Exception $e) {
            $conn->rollback();
            return [
                'status' => 'error',
                'message' => 'Error al procesar los datos: ' . $e->getMessage()
            ];
        }
    }
    
    private function createNewTest() {
        $conn = $this->connection->getConexion();
        
        $query = "INSERT INTO PRUEBA (id_usuario_prueba, tipo_prueba, fecha_prueba, hora_inicio, hora_fin) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        
        $tipo_prueba = $this->data['metadatos']['modo'];
        $fecha = $this->formatDateForMySQL($this->data['metadatos']['fecha']);
        $hora_inicio = $this->formatTimeForMySQL($this->data['metadatos']['hora_inicio']);
        $hora_fin = $this->formatTimeForMySQL($this->data['metadatos']['hora_fin']);
        
        $stmt->bind_param("issss", $this->userId, $tipo_prueba, $fecha, $hora_inicio, $hora_fin);
        $stmt->execute();
        
        $prueba_id = $conn->insert_id;
        $stmt->close();
        
        return $prueba_id;
    }

    private function formatDateForMySQL($dateString) {
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $dateString)) {
            $dateParts = explode('/', $dateString);
            return $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
        }
        return $dateString; 
    }
    
    private function formatTimeForMySQL($timeString) {
        $timeString = trim($timeString);
        
        if (stripos($timeString, 'a. m.') !== false || stripos($timeString, 'p. m.') !== false) {
           
            $timeString = str_replace('a. m.', 'AM', $timeString);
            $timeString = str_replace('p. m.', 'PM', $timeString);
            
            $dateTime = DateTime::createFromFormat('g:i:s A', $timeString);
            if ($dateTime) {
                return $dateTime->format('H:i:s');
            }
        }
        
        if (stripos($timeString, 'am') !== false || stripos($timeString, 'pm') !== false) {
            $dateTime = DateTime::createFromFormat('g:i:s a', $timeString);
            if ($dateTime) {
                return $dateTime->format('H:i:s');
            }
        }
        
        if (preg_match('/^\d{1,2}:\d{1,2}:\d{1,2}$/', $timeString)) {
            $timeParts = explode(':', $timeString);
            return sprintf('%02d:%02d:%02d', (int)$timeParts[0], (int)$timeParts[1], (int)$timeParts[2]);
        }
        
        $time = strtotime($timeString);
        if ($time !== false) {
            return date('H:i:s', $time);
        }
        
        return '00:00:00';
    }
    
    private function getLastTestId() {
        $conn = $this->connection->getConexion();
        
        $query = "SELECT MAX(id_prueba) as last_id FROM PRUEBA WHERE id_usuario_prueba = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['last_id'];
    }
    
    private function createIteration($prueba_id) {
        $conn = $this->connection->getConexion();
        
        $query = "INSERT INTO ITERACION_PRUEBA (iteracion, id_prueba, comentario) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        $iteracion = $this->data['metadatos']['iteracion'];
        $comentario = $this->data['metadatos']['comentario'];
        
        $stmt->bind_param("iis", $iteracion, $prueba_id, $comentario);
        $stmt->execute();
        
        $iteracion_id = $conn->insert_id;
        $stmt->close();
        
        return $iteracion_id;
    }
    
    private function processSemaphoreData($iteracion_id) {
        $conn = $this->connection->getConexion();
        
        $query = "INSERT INTO DATOS_SEMAFORO_ITERACION_PRUEBA 
                  (id_iteracion_prueba, id_semaforo, tiempo_verde, tiempo_amarillo, tiempo_rojo) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        
        foreach ($this->semaphoreIds as $direction => $semaphore_id) {
            $tiempo_verde = $this->data['configuracion_semaforos'][$direction]['green'];
            $tiempo_amarillo = $this->data['configuracion_semaforos'][$direction]['yellow'];
            $tiempo_rojo = $this->data['configuracion_semaforos'][$direction]['red'];
            
            $stmt->bind_param("iiiii", $iteracion_id, $semaphore_id, $tiempo_verde, $tiempo_amarillo, $tiempo_rojo);
            $stmt->execute();
        }
        
        $stmt->close();
    }
    
    private function createTestResult($iteracion_id) {
        $conn = $this->connection->getConexion();
        
        $query = "INSERT INTO RESULTADOS_PRUEBA_ITERACION (id_iteracion_prueba, tiempo_prueba) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        
        $tiempo_prueba = $this->data['metadatos']['duracion_total'];
        
        $stmt->bind_param("ii", $iteracion_id, $tiempo_prueba);
        $stmt->execute();
        
        $resultado_id = $conn->insert_id;
        $stmt->close();
        
        return $resultado_id;
    }
    
    private function processSemaphoreMonitoringResults($resultado_id) {
        $conn = $this->connection->getConexion();
        
        $datos_semaforo_ids = $this->getDatosSemaforoIds();
        
        foreach ($this->semaphoreIds as $direction => $semaphore_id) {
            // Insert into RESULTADO_MONITOREO_SEMAFORO
            $monitoreo_id = $this->insertMonitoringResult(
                $resultado_id, 
                $datos_semaforo_ids[$semaphore_id], 
                $direction
            );
            
            $this->processGreenLightIterations($monitoreo_id, $direction);
            
            $this->processYellowLightIterations($monitoreo_id, $direction);
            
            $this->processRedLightIterations($monitoreo_id, $direction);
        }
    }
    
    private function getDatosSemaforoIds() {
        $conn = $this->connection->getConexion();
        
        $query = "SELECT id_datos_semaforo_prueba, id_semaforo FROM DATOS_SEMAFORO_ITERACION_PRUEBA
                  WHERE id_iteracion_prueba = (
                    SELECT MAX(id_iteracion_prueba) FROM ITERACION_PRUEBA
                  )";
        
        $result = $conn->query($query);
        $datos_semaforo_ids = [];
        
        while ($row = $result->fetch_assoc()) {
            $datos_semaforo_ids[$row['id_semaforo']] = $row['id_datos_semaforo_prueba'];
        }
        
        return $datos_semaforo_ids;
    }
    
    private function insertMonitoringResult($resultado_id, $datos_semaforo_id, $direction) {
        $conn = $this->connection->getConexion();
        
        $query = "INSERT INTO RESULTADO_MONITOREO_SEMAFORO 
                  (id_resultado, id_datos_semaforo_prueba, cantidad_vehiculos_total, 
                   velocidad_promedio, cantidad_veces_verde, cantidad_veces_amarillo, cantidad_veces_rojo) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        
        $stats = $this->data['estadisticas'][$direction];
        $cantidad_vehiculos_total = $stats['total_vehiculos'];
        $velocidad_promedio = $stats['verde']['velocidad_promedio'];
        $cantidad_veces_verde = $stats['verde']['count'];
        $cantidad_veces_amarillo = $stats['amarillo']['count'];
        $cantidad_veces_rojo = $stats['rojo']['count'];
        
        $stmt->bind_param("iiidiii", 
            $resultado_id, 
            $datos_semaforo_id, 
            $cantidad_vehiculos_total, 
            $velocidad_promedio, 
            $cantidad_veces_verde, 
            $cantidad_veces_amarillo, 
            $cantidad_veces_rojo
        );
        
        $stmt->execute();
        $monitoreo_id = $conn->insert_id;
        $stmt->close();
        
        return $monitoreo_id;
    }
    
    private function processGreenLightIterations($monitoreo_id, $direction) {
        $conn = $this->connection->getConexion();
        
        $query = "INSERT INTO RESULTADO_ITERACION_SEMAFORO_VERDE 
                  (id_resultado_monitoreo, cantidad_vehiculos) VALUES (?, ?)";
        
        $stmt = $conn->prepare($query);
        
        $greenIterations = $this->data['estadisticas'][$direction]['verde']['iteraciones'];
        
        foreach ($greenIterations as $cantidad_vehiculos) {
            $stmt->bind_param("ii", $monitoreo_id, $cantidad_vehiculos);
            $stmt->execute();
        }
        
        $stmt->close();
    }
    
    private function processYellowLightIterations($monitoreo_id, $direction) {
        $conn = $this->connection->getConexion();
        
        $query = "INSERT INTO RESULTADO_ITERACION_SEMAFORO_AMARILLO 
                  (id_resultado_monitoreo, cantidad_vehiculos) VALUES (?, ?)";
        
        $stmt = $conn->prepare($query);
        
        $yellowIterations = $this->data['estadisticas'][$direction]['amarillo']['iteraciones'];
        
        foreach ($yellowIterations as $cantidad_vehiculos) {
            $stmt->bind_param("ii", $monitoreo_id, $cantidad_vehiculos);
            $stmt->execute();
        }
        
        $stmt->close();
    }
    
    private function processRedLightIterations($monitoreo_id, $direction) {
        $conn = $this->connection->getConexion();
        
        $query = "INSERT INTO RESULTADO_ITERACION_SEMAFORO_ROJO 
                  (id_resultado_monitoreo, cantidad_vehiculos_detenidos) VALUES (?, ?)";
        
        $stmt = $conn->prepare($query);
        
        $redIterations = $this->data['estadisticas'][$direction]['rojo']['detenidos'];
        
        foreach ($redIterations as $cantidad_vehiculos_detenidos) {
            $stmt->bind_param("ii", $monitoreo_id, $cantidad_vehiculos_detenidos);
            $stmt->execute();
        }
        
        $stmt->close();
    }
    
    private function processVehicles($prueba_id) {
        $conn = $this->connection->getConexion();
        
        $query = "INSERT INTO DATOS_VEHICULO_PRUEBA 
                  (id_prueba, marca, tipo_vehiculo, velocidad, origen, destino) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        
        foreach ($this->data['vehiculos'] as $vehicle) {
            $marca = $vehicle['marca'];
            $tipo_vehiculo = $vehicle['tipo'];
            $velocidad = $vehicle['velocidad'];
            $origen = $vehicle['origen'];
            $destino = $vehicle['destino'];
            
            $stmt->bind_param("issdss", 
                $prueba_id, 
                $marca, 
                $tipo_vehiculo, 
                $velocidad, 
                $origen, 
                $destino
            );
            
            $stmt->execute();
        }
        
        $stmt->close();
    }
    
    public function closeConnection() {
        $this->connection->cerrarConexion();
    }
}
?>