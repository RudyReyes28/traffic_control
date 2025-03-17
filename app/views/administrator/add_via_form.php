<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Calle/Avenida</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
        session_start();
        if(!isset($_SESSION['usuario'])){
            header('Location: ../../views/login/login.php');
        }
    ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4>Agregar Nueva Calle/Avenida</h4>
                    </div>
                    <div class="card-body">
                        <form action="../../controllers/admin_controller/agregar_via.php" method="POST">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="Calle">Calle</option>
                                    <option value="Avenida">Avenida</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="longitud" class="form-label">Longitud (Kilometros)</label>
                                <input type="number" step="0.01" class="form-control" id="longitud" name="longitud" required>
                            </div>
                            <div class="mb-3">
                                <label for="inicio_via" class="form-label">Inicio de la Vía</label>
                                <input type="text" class="form-control" id="inicio_via" name="inicio_via" required>
                            </div>
                            <div class="mb-3">
                                <label for="fin_via" class="form-label">Fin de la Vía</label>
                                <input type="text" class="form-control" id="fin_via" name="fin_via" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_doble_sentido" name="is_doble_sentido" value="1">
                                <label class="form-check-label" for="is_doble_sentido">¿Es de doble sentido?</label>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">Guardar Vía</button>
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