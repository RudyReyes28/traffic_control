<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Semáforo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
        session_start();
        if(!isset($_SESSION['usuario'])){
            header('Location: ../../views/login/login.php');
        }
        require_once '../../models/admin_dao/InterseccionModel.php';
        $interseccionModel = new InterseccionModel();
        $intersecciones = $interseccionModel->getAllIntersecciones();
    ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h4>Agregar Nuevo Semáforo</h4>
                    </div>
                    <div class="card-body">
                        <form action="../../controllers/admin_controller/add_traffic_ligth.php" method="POST">
                            <div class="mb-3">
                                <label for="interseccion" class="form-label">Intersección</label>
                                <select class="form-select" id="interseccion" name="id_interseccion" required>
                                    <option value="">Seleccione una intersección</option>
                                    <?php foreach($intersecciones as $interseccion): ?>
                                        <option value="<?php echo $interseccion['id_interseccion']; ?>">
                                            <?php echo $interseccion['descripcion'] . ' (ID: ' . $interseccion['id_interseccion'] . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="posicion_salida" class="form-label">Posición de Salida</label>
                                <input type="text" class="form-control" id="posicion_salida" name="posicion_salida" placeholder="Ej: Norte, Sur, Este, Oeste" required>
                            </div>
                            <div class="mb-3">
                                <label for="posicion_alto" class="form-label">Posición de Alto</label>
                                <input type="text" class="form-control" id="posicion_alto" name="posicion_alto" placeholder="Ej: Norte, Sur, Este, Oeste" required>
                            </div>
                            <div class="mb-3">
                                <label for="estado_operativo" class="form-label">Estado Operativo</label>
                                <select class="form-select" id="estado_operativo" name="estado_operativo" required>
                                    <option value="">Seleccione un estado</option>
                                    <option value="Activo">Activo</option>
                                    <option value="En Mantenimiento">En Mantenimiento</option>
                                    <option value="Inactivo">Inactivo</option>
                                    <option value="Intermitente">Intermitente</option>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning">Guardar Semáforo</button>
                                <a href="home_admin.php" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>