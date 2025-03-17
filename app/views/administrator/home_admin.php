<?php
    session_start();
    if(!isset($_SESSION['usuario'])){
        header('Location: ../../views/login/login.php');
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard del Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .card-dashboard {
            transition: transform 0.3s;
        }
        .card-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .icon-large {
            font-size: 3rem;
        }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sistema de Gestión de Tráfico</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-light">Bienvenido, <?php echo $_SESSION['usuario']; ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../controllers/authentication/session_close.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="text-center mb-4">Panel de Control</h2>
                <p class="text-center">Seleccione una opción para gestionar el sistema.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card card-dashboard h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-person-plus icon-large text-primary"></i>
                        <h5 class="card-title mt-3">Usuarios</h5>
                        <p class="card-text">Gestione los usuarios del sistema.</p>
                        <a href="add_user_form.php" class="btn btn-primary">Agregar Usuario</a>
                        <!-- <a href="../usuario/listar_usuarios.php" class="btn btn-outline-primary mt-2">Ver Usuarios</a> -->
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card card-dashboard h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-map icon-large text-success"></i>
                        <h5 class="card-title mt-3">Vías</h5>
                        <p class="card-text">Gestione las calles y avenidas del sistema.</p>
                        <a href="add_via_form.php" class="btn btn-success">Agregar Vía</a>
                        <!-- <a href="../vias/listar_vias.php" class="btn btn-outline-success mt-2">Ver Vías</a> -->
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card card-dashboard h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-signpost-split icon-large text-info"></i>
                        <h5 class="card-title mt-3">Intersecciones</h5>
                        <p class="card-text">Gestione las intersecciones de vías.</p>
                        <a href="add_intersection_form.php" class="btn btn-info">Agregar Intersección</a>
                        <!-- <a href="../interseccion/listar_intersecciones.php" class="btn btn-outline-info mt-2">Ver Intersecciones</a> -->
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card card-dashboard h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-stoplights icon-large text-warning"></i>
                        <h5 class="card-title mt-3">Semáforos</h5>
                        <p class="card-text">Gestione los semáforos del sistema.</p>
                        <a href="add_traffic_ligth_form.php" class="btn btn-warning">Agregar Semáforo</a>
                        <!-- <a href="../semaforo/listar_semaforos.php" class="btn btn-outline-warning mt-2">Ver Semáforos</a> -->
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5>Resumen del Sistema</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                                require_once '../../models/admin_dao/UsuarioModel.php';
                                require_once '../../models/admin_dao/ViasModel.php';
                                require_once '../../models/admin_dao/InterseccionModel.php';
                                require_once '../../models/admin_dao/SemaforoModel.php';
                                
                                $usuarioModel = new UsuarioModel();
                                $viasModel = new ViasModel();
                                $interseccionModel = new InterseccionModel();
                                $semaforoModel = new SemaforoModel();
                                
                                $usuarios = $usuarioModel->getAllUsuarios();
                                $vias = $viasModel->getAllVias();
                                $intersecciones = $interseccionModel->getAllIntersecciones();
                                $semaforos = $semaforoModel->getAllSemaforos();
                                
                                $totalUsuarios = count($usuarios);
                                $totalVias = count($vias);
                                $totalIntersecciones = count($intersecciones);
                                $totalSemaforos = count($semaforos);
                            ?>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h1 class="display-4"><?php echo $totalUsuarios; ?></h1>
                                        <p class="lead">Usuarios</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h1 class="display-4"><?php echo $totalVias; ?></h1>
                                        <p class="lead">Vías</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h1 class="display-4"><?php echo $totalIntersecciones; ?></h1>
                                        <p class="lead">Intersecciones</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h1 class="display-4"><?php echo $totalSemaforos; ?></h1>
                                        <p class="lead">Semáforos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5>Últimos Usuarios Registrados</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Rol</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $contador = 0;
                                    foreach(array_reverse($usuarios) as $usuario) {
                                        if($contador < 5) {
                                            echo "<tr>";
                                            echo "<td>" . $usuario['Nombre'] . "</td>";
                                            echo "<td>" . $usuario['Apellido'] . "</td>";
                                            echo "<td>" . $usuario['nombre_tipo'] . "</td>";
                                            echo "</tr>";
                                            $contador++;
                                        } else {
                                            break;
                                        }
                                    }
                                    
                                    if($contador == 0) {
                                        echo "<tr><td colspan='3' class='text-center'>No hay usuarios registrados</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5>Últimos Semáforos Agregados</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Intersección</th>
                                    <th>Posición</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $contador = 0;
                                    foreach(array_reverse($semaforos) as $semaforo) {
                                        if($contador < 5) {
                                            echo "<tr>";
                                            echo "<td>" . $semaforo['interseccion_descripcion'] . "</td>";
                                            echo "<td>" . $semaforo['posicion_salida'] . "</td>";
                                            echo "<td>" . $semaforo['estado_operativo'] . "</td>";
                                            echo "</tr>";
                                            $contador++;
                                        } else {
                                            break;
                                        }
                                    }
                                    
                                    if($contador == 0) {
                                        echo "<tr><td colspan='3' class='text-center'>No hay semáforos registrados</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; <?php echo date('Y'); ?> Sistema de Gestión de Tráfico</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>