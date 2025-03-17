<?php
    session_start();
    if(!isset($_SESSION['usuario'])){
        header('Location: ../../views/login/login.php');
    }
    
    // Importar el modelo de reportes
    require_once '../../models/supervisor_dao/ReportesModel.php';
    $reportesModel = new ReportesModel();
    
    // Obtener datos para los diferentes reportes
    $resumenTrafico = $reportesModel->getResumenTrafico();
    $registroIteraciones = $reportesModel->getRegistroIteraciones();
    $analisisFlujo = $reportesModel->getAnalisisFlujoVehicular();
    $estadisticasTipoVehiculo = $reportesModel->getEstadisticasTipoVehiculo();
    $listaPruebas = $reportesModel->getListaPruebas();
    
    // Si se solicita un informe detallado
    $informeDetallado = null;
    $resumenPrueba = null;
    $estadisticasSemaforos = null;
    if(isset($_GET['id_prueba'])) {
        $resumenPrueba = $reportesModel->getResumenPrueba($_GET['id_prueba']);
        $estadisticasSemaforos = $reportesModel->getEstadisticasSemaforos($_GET['id_prueba']);
    }
    
    // Cerrar la conexión
    $reportesModel->closeConection();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Supervisor - Monitoreo de Tráfico</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .stats-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .tab-content {
            padding: 20px 0;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            font-weight: bold;
        }
        .bg-light-green {
            background-color: #e8f5e9;
        }
        .bg-light-red {
            background-color: #ffebee;
        }
        .bg-light-yellow {
            background-color: #fff8e1;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-traffic-light me-2"></i>Sistema de Monitoreo de Tráfico</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link active"><i class="fas fa-user me-1"></i> Bienvenido, <?php echo $_SESSION['usuario']; ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../controllers/authentication/session_close.php"><i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title"><i class="fas fa-chart-line me-2"></i>Panel de Control - Reportes de Tráfico</h2>
                        <p class="card-text">Gestión y análisis de datos de monitoreo de tráfico vehicular.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestañas de navegación -->
        <ul class="nav nav-tabs" id="reportesTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="resumen-tab" data-bs-toggle="tab" data-bs-target="#resumen" type="button" role="tab" aria-controls="resumen" aria-selected="true">
                    <i class="fas fa-tachometer-alt me-1"></i> Resumen Tráfico
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="iteraciones-tab" data-bs-toggle="tab" data-bs-target="#iteraciones" type="button" role="tab" aria-controls="iteraciones" aria-selected="false">
                    <i class="fas fa-clock me-1"></i> Registro Iteraciones
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="flujo-tab" data-bs-toggle="tab" data-bs-target="#flujo" type="button" role="tab" aria-controls="flujo" aria-selected="false">
                    <i class="fas fa-car me-1"></i> Flujo Vehicular
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="informe-tab" data-bs-toggle="tab" data-bs-target="#informe" type="button" role="tab" aria-controls="informe" aria-selected="false">
                    <i class="fas fa-file-alt me-1"></i> Informe Detallado
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tipoVehiculo-tab" data-bs-toggle="tab" data-bs-target="#tipoVehiculo" type="button" role="tab" aria-controls="tipoVehiculo" aria-selected="false">
                    <i class="fas fa-truck me-1"></i> Estadísticas por Tipo
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="reportesTabsContent">
            <!-- 1. Resumen Tráfico Vehicular -->
            <div class="tab-pane fade show active" id="resumen" role="tabpanel" aria-labelledby="resumen-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-bar me-1"></i> Resumen de Tráfico Vehicular
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="card stats-card bg-light-green">
                                            <div class="card-body text-center">
                                                <div class="card-icon text-success">
                                                    <i class="fas fa-car"></i>
                                                </div>
                                                <h5 class="card-title">Total Vehículos</h5>
                                                <h2 class="fw-bold">
                                                    <?php 
                                                        $totalVehiculos = 0;
                                                        foreach ($resumenTrafico as $item) {
                                                            $totalVehiculos += $item['total_vehiculos'];
                                                        }
                                                        echo $totalVehiculos;
                                                    ?>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card stats-card bg-light-yellow">
                                            <div class="card-body text-center">
                                                <div class="card-icon text-warning">
                                                    <i class="fas fa-tachometer-alt"></i>
                                                </div>
                                                <h5 class="card-title">Velocidad Promedio</h5>
                                                <h2 class="fw-bold">
                                                    <?php 
                                                        $velocidadPromedio = 0;
                                                        $count = 0;
                                                        foreach ($resumenTrafico as $item) {
                                                            $velocidadPromedio += $item['velocidad_promedio'];
                                                            $count++;
                                                        }
                                                        echo $count > 0 ? number_format($velocidadPromedio / $count, 1) : 0;
                                                    ?> km/h
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card stats-card bg-light-red">
                                            <div class="card-body text-center">
                                                <div class="card-icon text-danger">
                                                    <i class="fas fa-traffic-light"></i>
                                                </div>
                                                <h5 class="card-title">Total Semáforos</h5>
                                                <h2 class="fw-bold">
                                                    <?php 
                                                        $totalSemaforos = 0;
                                                        foreach ($resumenTrafico as $item) {
                                                            if ($item['total_semaforos'] > $totalSemaforos) {
                                                                $totalSemaforos = $item['total_semaforos'];
                                                            }
                                                        }
                                                        echo $totalSemaforos;
                                                    ?>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card stats-card">
                                            <div class="card-body text-center">
                                                <div class="card-icon text-primary">
                                                    <i class="fas fa-clipboard-list"></i>
                                                </div>
                                                <h5 class="card-title">Total Pruebas</h5>
                                                <h2 class="fw-bold"><?php echo count($resumenTrafico); ?></h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>ID Prueba</th>
                                                <th>Fecha</th>
                                                <th>Hora Inicio</th>
                                                <th>Hora Fin</th>
                                                <th>Total Vehículos</th>
                                                <th>Velocidad Promedio</th>
                                                <th>Velocidad Máxima</th>
                                                <th>Velocidad Mínima</th>
                                                <th>Total Semáforos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($resumenTrafico as $item): ?>
                                            <tr>
                                                <td><?php echo $item['id_prueba']; ?></td>
                                                <td><?php echo $item['fecha_prueba']; ?></td>
                                                <td><?php echo $item['hora_inicio']; ?></td>
                                                <td><?php echo $item['hora_fin']; ?></td>
                                                <td><?php echo $item['total_vehiculos']; ?></td>
                                                <td><?php echo number_format($item['velocidad_promedio'], 1); ?> km/h</td>
                                                <td><?php echo number_format($item['velocidad_maxima'], 1); ?> km/h</td>
                                                <td><?php echo number_format($item['velocidad_minima'], 1); ?> km/h</td>
                                                <td><?php echo $item['total_semaforos']; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Registro de Iteraciones -->
            <div class="tab-pane fade" id="iteraciones" role="tabpanel" aria-labelledby="iteraciones-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-clock me-1"></i> Registro de Iteraciones y Tiempo Invertido
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="card stats-card">
                                            <div class="card-body text-center">
                                                <div class="card-icon text-primary">
                                                    <i class="fas fa-user-clock"></i>
                                                </div>
                                                <h5 class="card-title">Total Tiempo Invertido</h5>
                                                <h2 class="fw-bold">
                                                <?php 
                                            $totalHoras = 0;
                                            $totalMinutos = 0;
                                            
                                            foreach ($registroIteraciones as $item) {
                                                $tiempo = explode(':', $item['tiempo_total']);
                                                $totalHoras += intval($tiempo[0]);
                                                $totalMinutos += intval($tiempo[1]);
                                            }
                                            
                                            $totalHoras += floor($totalMinutos / 60);
                                            $totalMinutos = $totalMinutos % 60;
                                            
                                            echo $totalHoras . 'h ' . $totalMinutos . 'm';
                                        ?>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card stats-card">
                                            <div class="card-body text-center">
                                                <div class="card-icon text-success">
                                                    <i class="fas fa-sync-alt"></i>
                                                </div>
                                                <h5 class="card-title">Total Iteraciones</h5>
                                                <h2 class="fw-bold">
                                                    <?php 
                                                        $totalIteraciones = 0;
                                                        foreach ($registroIteraciones as $item) {
                                                            $totalIteraciones += $item['total_iteraciones'];
                                                        }
                                                        echo $totalIteraciones;
                                                    ?>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card stats-card">
                                            <div class="card-body text-center">
                                                <div class="card-icon text-warning">
                                                    <i class="fas fa-stopwatch"></i>
                                                </div>
                                                <h5 class="card-title">Tiempo Promedio por Iteración</h5>
                                                <h2 class="fw-bold">
                                                    <?php 
                                                        $tiempoPromedio = 0;
                                                        $count = 0;
                                                        foreach ($registroIteraciones as $item) {
                                                            $tiempoPromedio += $item['tiempo_promedio_iteracion'];
                                                            $count++;
                                                        }
                                                        echo $count > 0 ? number_format($tiempoPromedio / $count, 1) : 0;
                                                    ?> seg
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Monitor</th>
                                                <th>ID Prueba</th>
                                                <th>Tipo De Prueba</th>
                                                <th>Fecha</th>
                                                <th>Hora Inicio</th>
                                                <th>Hora Fin</th>
                                                <th>Tiempo Total</th>
                                                <th>Total Iteraciones</th>
                                                <th>Tiempo Promedio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($registroIteraciones as $item): ?>
                                            <tr>
                                                <td><?php echo $item['nombre_monitor'] . ' ' . $item['apellido_monitor']; ?></td>
                                                <td><?php echo $item['id_prueba']; ?></td>
                                                <td><?php echo $item['tipo_prueba'] ?></td>
                                                <td><?php echo $item['fecha_prueba']; ?></td>
                                                <td><?php echo $item['hora_inicio']; ?></td>
                                                <td><?php echo $item['hora_fin']; ?></td>
                                                <td><?php echo $item['tiempo_total']; ?></td>
                                                <td><?php echo $item['total_iteraciones']; ?></td>
                                                <td><?php echo number_format($item['tiempo_promedio_iteracion'], 1); ?> seg</td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Análisis del Flujo Vehicular -->
            <div class="tab-pane fade" id="flujo" role="tabpanel" aria-labelledby="flujo-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-car me-1"></i> Análisis del Flujo Vehicular por Semáforo
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="card stats-card bg-light-green">
                                            <div class="card-body text-center">
                                                <div class="card-icon text-success">
                                                    <i class="fas fa-car-side"></i>
                                                </div>
                                                <h5 class="card-title">Vehículos en Verde</h5>
                                                <h2 class="fw-bold">
                                                    <?php 
                                                        $totalVehiculosVerde = 0;
                                                        foreach ($analisisFlujo as $item) {
                                                            $totalVehiculosVerde += $item['total_vehiculos_verde'];
                                                        }
                                                        echo $totalVehiculosVerde;
                                                    ?>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card stats-card bg-light-yellow">
                                            <div class="card-body text-center">
                                                <div class="card-icon text-warning">
                                                    <i class="fas fa-car-side"></i>
                                                </div>
                                                <h5 class="card-title">Vehículos en Amarillo</h5>
                                                <h2 class="fw-bold">
                                                    <?php 
                                                        $totalVehiculosAmarillo = 0;
                                                        foreach ($analisisFlujo as $item) {
                                                            $totalVehiculosAmarillo += $item['total_vehiculos_amarillo'];
                                                        }
                                                        echo $totalVehiculosAmarillo;
                                                    ?>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card stats-card bg-light-red">
                                            <div class="card-body text-center">
                                                <div class="card-icon text-danger">
                                                    <i class="fas fa-car-side"></i>
                                                </div>
                                                <h5 class="card-title">Vehículos Detenidos</h5>
                                                <h2 class="fw-bold">
                                                    <?php 
                                                        $totalVehiculosDetenidos = 0;
                                                        foreach ($analisisFlujo as $item) {
                                                            $totalVehiculosDetenidos += $item['total_vehiculos_detenidos'];
                                                        }
                                                        echo $totalVehiculosDetenidos;
                                                    ?>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card stats-card">
                                            <div class="card-body text-center">
                                                <div class="card-icon text-primary">
                                                    <i class="fas fa-traffic-light"></i>
                                                </div>
                                                <h5 class="card-title">Total Semáforos</h5>
                                                <h2 class="fw-bold"><?php echo count($analisisFlujo); ?></h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>ID Semáforo</th>
                                                <th>Intersección</th>
                                                <th>Vía 1</th>
                                                <th>Vía 2</th>
                                                <th>Promedio Vehículos</th>
                                                <th>Velocidad Promedio</th>
                                                <th>Vehículos en Verde</th>
                                                <th>Vehículos en Amarillo</th>
                                                <th>Vehículos Detenidos</th>
                                                <th>Tiempos Semáforo Promedio(V/A/R)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($analisisFlujo as $item): ?>
                                            <tr>
                                                <td><?php echo $item['id_semaforo']; ?></td>
                                                <td><?php echo $item['interseccion']; ?></td>
                                                <td><?php echo $item['via_1']; ?></td>
                                                <td><?php echo $item['via_2']; ?></td>
                                                <td><?php echo number_format($item['promedio_vehiculos'], 1); ?></td>
                                                <td><?php echo number_format($item['velocidad_promedio'], 1); ?> km/h</td>
                                                <td><?php echo $item['total_vehiculos_verde']; ?></td>
                                                <td><?php echo $item['total_vehiculos_amarillo']; ?></td>
                                                <td><?php echo $item['total_vehiculos_detenidos']; ?></td>
                                                <td>
                                                    <?php 
                                                        echo number_format($item['promedio_tiempo_verde'], 0) . ' / ';
                                                        echo number_format($item['promedio_tiempo_amarillo'], 0) . ' / ';
                                                        echo number_format($item['promedio_tiempo_rojo'], 0);
                                                    ?> seg
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Informe Detallado -->
            <div class="tab-pane fade" id="informe" role="tabpanel" aria-labelledby="informe-tab">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-file-alt me-1"></i> Generación de Informes Detallados
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-search me-1"></i> Seleccionar Prueba
                                </div>
                                <div class="card-body">
                                    <form action="home_supervisor.php" method="GET">
                                        <div class="mb-3">
                                            <label for="id_prueba" class="form-label">Seleccione una prueba:</label>
                                            <select class="form-select" id="id_prueba" name="id_prueba" required>
                                                <option value="">-- Seleccione una prueba --</option>
                                                <?php foreach ($listaPruebas as $prueba): ?>
                                                <option value="<?php echo $prueba['id_prueba']; ?>" <?php echo (isset($_GET['id_prueba']) && $_GET['id_prueba'] == $prueba['id_prueba']) ? 'selected' : ''; ?>>
                                                    Prueba #<?php echo $prueba['id_prueba']; ?> - <?php echo $prueba['fecha_prueba']; ?> - <?php echo $prueba['monitor']; ?> (<?php echo $prueba['total_iteraciones']; ?> iteraciones)
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i> Generar Informe
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (isset($_GET['id_prueba']) && !empty($resumenPrueba)): ?>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-info-circle me-1"></i> Información General
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Tipo de prueba:</strong> <?php echo $resumenPrueba['tipo_prueba']; ?></p>
                                            <p><strong>Comentarios:</strong> <?php echo $resumenPrueba['comentario']; ?></p>
                                            <p><strong>Fecha:</strong> <?php echo $resumenPrueba['fecha_prueba']; ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Hora inicio:</strong> <?php echo $resumenPrueba['hora_inicio']; ?></p>
                                            <p><strong>Hora fin:</strong> <?php echo $resumenPrueba['hora_fin']; ?></p>
                                            <p><strong>Tiempo total:</strong> <?php echo $resumenPrueba['tiempo_total']; ?> segundos</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($_GET['id_prueba']) && !empty($estadisticasSemaforos)): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-traffic-light me-1"></i> Estadísticas por Semáforo
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="semaforosAccordion">
                                <?php foreach ($estadisticasSemaforos as $index => $semaforo): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?php echo $semaforo['id_semaforo']; ?>">
                                        <button class="accordion-button <?php echo $index === 0 ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $semaforo['id_semaforo']; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $semaforo['id_semaforo']; ?>">
                                            Semáforo <?php echo $semaforo['descripcion']; ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?php echo $semaforo['id_semaforo']; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $semaforo['id_semaforo']; ?>" data-bs-parent="#semaforosAccordion">
                                        <div class="accordion-body">
                                            <div class="row mb-4">
                                                <div class="col-md-3">
                                                    <p><strong>Tiempo en verde:</strong> <?php echo $semaforo['tiempo_verde']; ?>s</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p><strong>Tiempo en amarillo:</strong> <?php echo $semaforo['tiempo_amarillo']; ?>s</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p><strong>Tiempo en rojo:</strong> <?php echo $semaforo['tiempo_rojo']; ?>s</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p><strong>Vehículos totales:</strong> <?php echo $semaforo['cantidad_vehiculos_total']; ?></p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p><strong>Velocidad promedio:</strong> <?php echo number_format($semaforo['velocidad_promedio'], 1); ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-success text-white">
                                                            <i class="fas fa-check-circle me-1"></i> Iteraciones en verde (<?php echo $semaforo['cantidad_veces_verde']; ?>)
                                                        </div>
                                                        <div class="card-body">
                                                            <?php foreach ($semaforo['iteraciones_verde'] as $iteracion): ?>
                                                            <p>Iteración <?php echo $iteracion['iteracion']; ?>: <?php echo $iteracion['vehiculos']; ?> vehículos</p>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-warning text-dark">
                                                            <i class="fas fa-exclamation-triangle me-1"></i> Iteraciones en amarillo (<?php echo $semaforo['cantidad_veces_amarillo']; ?>)
                                                        </div>
                                                        <div class="card-body">
                                                            <?php foreach ($semaforo['iteraciones_amarillo'] as $iteracion): ?>
                                                            <p>Iteración <?php echo $iteracion['iteracion']; ?>: <?php echo $iteracion['vehiculos']; ?> vehículos</p>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-danger text-white">
                                                            <i class="fas fa-times-circle me-1"></i> Iteraciones en rojo (<?php echo $semaforo['cantidad_veces_rojo']; ?>)
                                                        </div>
                                                        <div class="card-body">
                                                            <?php foreach ($semaforo['iteraciones_rojo'] as $iteracion): ?>
                                                            <p>Iteración <?php echo $iteracion['iteracion']; ?>: 0 vehículos pasaron, <?php echo $iteracion['vehiculos_detenidos']; ?> vehículos detenidos</p>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

            <!-- 5. Estadísticas por Tipo de Vehículo -->
            <div class="tab-pane fade" id="tipoVehiculo" role="tabpanel" aria-labelledby="tipoVehiculo-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-truck me-1"></i> Estadísticas por Tipo de Vehículo
                            </div>
                            <div class="card-body">

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Tipo de Vehículo</th>
                                                <th>Cantidad Total</th>
                                                <th>Velocidad Promedio</th>
                                                <th>Velocidad Máxima</th>
                                                <th>Velocidad Mínima</th>
                                                <th>Pruebas Registradas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($estadisticasTipoVehiculo as $item): ?>
                                            <tr>
                                                <td><?php echo $item['tipo_vehiculo']; ?></td>
                                                <td><?php echo $item['cantidad_total']; ?></td>
                                                <td><?php echo number_format($item['velocidad_promedio'], 1); ?> km/h</td>
                                                <td><?php echo number_format($item['velocidad_maxima'], 1); ?> km/h</td>
                                                <td><?php echo number_format($item['velocidad_minima'], 1); ?> km/h</td>
                                                <td><?php echo $item['cantidad_pruebas']; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Sistema de Monitoreo de Tráfico</h5>
                    <p>Panel de control para supervisores de tráfico vehicular.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; <?php echo date('Y'); ?> - Todos los derechos reservados</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
<script> 

    </script>