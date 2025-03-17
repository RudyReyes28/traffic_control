<?php
require_once '../../models/conection_model/ConectionBD.php';

class ReportesModel {
    private $conection;

    public function __construct() {
        $this->conection = new ConectionBD();
    }

    // 1. Resumen tráfico vehicular
    public function getResumenTrafico() {
        $conexion = $this->conection->getConexion();
        $query = "SELECT 
                    p.id_prueba, 
                    p.fecha_prueba, 
                    p.hora_inicio, 
                    p.hora_fin, 
                    COUNT(DISTINCT d.id_vehiculo_prueba) as total_vehiculos,
                    AVG(d.velocidad) as velocidad_promedio,
                    MAX(d.velocidad) as velocidad_maxima,
                    MIN(d.velocidad) as velocidad_minima,
                    COUNT(DISTINCT s.id_semaforo) as total_semaforos
                FROM PRUEBA p
                JOIN DATOS_VEHICULO_PRUEBA d ON p.id_prueba = d.id_prueba
                JOIN ITERACION_PRUEBA i ON p.id_prueba = i.id_prueba
                JOIN DATOS_SEMAFORO_ITERACION_PRUEBA ds ON i.id_iteracion_prueba = ds.id_iteracion_prueba
                JOIN SEMAFORO s ON ds.id_semaforo = s.id_semaforo
                GROUP BY p.id_prueba
                ORDER BY p.fecha_prueba DESC, p.hora_inicio DESC";
        
        $resultado = $conexion->query($query);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // 2. Registro de iteraciones y tiempo invertido por el monitor
    public function getRegistroIteraciones() {
        $conexion = $this->conection->getConexion();
        $query = "SELECT 
                    u.Nombre as nombre_monitor,
                    u.Apellido as apellido_monitor,
                    p.id_prueba,
                    p.fecha_prueba,
                    p.hora_inicio,
                    p.hora_fin,
                    TIMEDIFF(p.hora_fin, p.hora_inicio) as tiempo_total,
                    COUNT(DISTINCT i.id_iteracion_prueba) as total_iteraciones,
                    AVG(r.tiempo_prueba) as tiempo_promedio_iteracion
                FROM PRUEBA p
                JOIN USUARIO u ON p.id_usuario_prueba = u.ID
                JOIN ITERACION_PRUEBA i ON p.id_prueba = i.id_prueba
                JOIN RESULTADOS_PRUEBA_ITERACION r ON i.id_iteracion_prueba = r.id_iteracion_prueba
                GROUP BY p.id_prueba, u.ID
                ORDER BY p.fecha_prueba DESC, p.hora_inicio DESC";
        
        $resultado = $conexion->query($query);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // 3. Análisis del flujo vehicular por semáforo
    public function getAnalisisFlujoVehicular() {
        $conexion = $this->conection->getConexion();
        $query = "SELECT 
                    s.id_semaforo,
                    i.descripcion as interseccion,
                    v1.nombre as via_1,
                    v2.nombre as via_2,
                    AVG(rm.cantidad_vehiculos_total) as promedio_vehiculos,
                    AVG(rm.velocidad_promedio) as velocidad_promedio,
                    SUM(rv.cantidad_vehiculos) as total_vehiculos_verde,
                    SUM(ra.cantidad_vehiculos) as total_vehiculos_amarillo,
                    SUM(rr.cantidad_vehiculos_detenidos) as total_vehiculos_detenidos,
                    AVG(ds.tiempo_verde) as promedio_tiempo_verde,
                    AVG(ds.tiempo_amarillo) as promedio_tiempo_amarillo,
                    AVG(ds.tiempo_rojo) as promedio_tiempo_rojo
                FROM SEMAFORO s
                JOIN INTERSECCION i ON s.id_interseccion = i.id_interseccion
                JOIN DETALLE_VIA_INTERSECCION dvi ON i.id_detalle_via_interserccion = dvi.id_detalle_via_interseccion
                JOIN VIAS v1 ON dvi.id_via_interseccion_1 = v1.id_via
                JOIN VIAS v2 ON dvi.id_via_inserseccion_2 = v2.id_via
                JOIN DATOS_SEMAFORO_ITERACION_PRUEBA ds ON s.id_semaforo = ds.id_semaforo
                JOIN RESULTADO_MONITOREO_SEMAFORO rm ON ds.id_datos_semaforo_prueba = rm.id_datos_semaforo_prueba
                LEFT JOIN RESULTADO_ITERACION_SEMAFORO_VERDE rv ON rm.id_resultado_monitoreo = rv.id_resultado_monitoreo
                LEFT JOIN RESULTADO_ITERACION_SEMAFORO_AMARILLO ra ON rm.id_resultado_monitoreo = ra.id_resultado_monitoreo
                LEFT JOIN RESULTADO_ITERACION_SEMAFORO_ROJO rr ON rm.id_resultado_monitoreo = rr.id_resultado_monitoreo
                GROUP BY s.id_semaforo
                ORDER BY promedio_vehiculos DESC";
        
        $resultado = $conexion->query($query);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // 4. Generación de informes detallados
    public function getInformeDetallado($id_prueba) {
        $conexion = $this->conection->getConexion();
        $query = "SELECT 
                    p.id_prueba,
                    p.fecha_prueba,
                    p.hora_inicio,
                    p.hora_fin,
                    CONCAT(u.Nombre, ' ', u.Apellido) as monitor,
                    i.iteracion,
                    i.comentario,
                    s.id_semaforo,
                    inter.descripcion as interseccion,
                    ds.tiempo_verde,
                    ds.tiempo_amarillo,
                    ds.tiempo_rojo,
                    rm.cantidad_vehiculos_total,
                    rm.velocidad_promedio,
                    rm.cantidad_veces_verde,
                    rm.cantidad_veces_amarillo,
                    rm.cantidad_veces_rojo,
                    rv.cantidad_vehiculos as vehiculos_verde,
                    ra.cantidad_vehiculos as vehiculos_amarillo,
                    rr.cantidad_vehiculos_detenidos as vehiculos_detenidos
                FROM PRUEBA p
                JOIN USUARIO u ON p.id_usuario_prueba = u.ID
                JOIN ITERACION_PRUEBA i ON p.id_prueba = i.id_prueba
                JOIN DATOS_SEMAFORO_ITERACION_PRUEBA ds ON i.id_iteracion_prueba = ds.id_iteracion_prueba
                JOIN SEMAFORO s ON ds.id_semaforo = s.id_semaforo
                JOIN INTERSECCION inter ON s.id_interseccion = inter.id_interseccion
                JOIN RESULTADOS_PRUEBA_ITERACION rpi ON i.id_iteracion_prueba = rpi.id_iteracion_prueba
                JOIN RESULTADO_MONITOREO_SEMAFORO rm ON rpi.id_resultado = rm.id_resultado AND ds.id_datos_semaforo_prueba = rm.id_datos_semaforo_prueba
                LEFT JOIN RESULTADO_ITERACION_SEMAFORO_VERDE rv ON rm.id_resultado_monitoreo = rv.id_resultado_monitoreo
                LEFT JOIN RESULTADO_ITERACION_SEMAFORO_AMARILLO ra ON rm.id_resultado_monitoreo = ra.id_resultado_monitoreo
                LEFT JOIN RESULTADO_ITERACION_SEMAFORO_ROJO rr ON rm.id_resultado_monitoreo = rr.id_resultado_monitoreo
                WHERE p.id_prueba = ?
                ORDER BY i.iteracion, s.id_semaforo";
        
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("i", $id_prueba);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // 5. Estadísticas por tipo de vehículo
    public function getEstadisticasTipoVehiculo() {
        $conexion = $this->conection->getConexion();
        $query = "SELECT 
                    dv.tipo_vehiculo,
                    COUNT(*) as cantidad_total,
                    AVG(dv.velocidad) as velocidad_promedio,
                    MAX(dv.velocidad) as velocidad_maxima,
                    MIN(dv.velocidad) as velocidad_minima,
                    COUNT(DISTINCT p.id_prueba) as cantidad_pruebas
                FROM DATOS_VEHICULO_PRUEBA dv
                JOIN PRUEBA p ON dv.id_prueba = p.id_prueba
                GROUP BY dv.tipo_vehiculo
                ORDER BY cantidad_total DESC";
        
        $resultado = $conexion->query($query);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // Obtener la lista de pruebas para el selector de informes detallados
    public function getListaPruebas() {
        $conexion = $this->conection->getConexion();
        $query = "SELECT 
                    p.id_prueba,
                    p.fecha_prueba,
                    p.hora_inicio,
                    CONCAT(u.Nombre, ' ', u.Apellido) as monitor,
                    COUNT(DISTINCT i.id_iteracion_prueba) as total_iteraciones,
                    COUNT(DISTINCT d.id_vehiculo_prueba) as total_vehiculos
                FROM PRUEBA p
                JOIN USUARIO u ON p.id_usuario_prueba = u.ID
                JOIN ITERACION_PRUEBA i ON p.id_prueba = i.id_prueba
                LEFT JOIN DATOS_VEHICULO_PRUEBA d ON p.id_prueba = d.id_prueba
                GROUP BY p.id_prueba
                ORDER BY p.fecha_prueba DESC, p.hora_inicio DESC";
        
        $resultado = $conexion->query($query);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function closeConection() {
        $this->conection->cerrarConexion();
    }
}
?>