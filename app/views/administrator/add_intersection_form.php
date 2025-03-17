<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Intersección</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
        session_start();
        if(!isset($_SESSION['usuario'])){
            header('Location: ../../views/login/login.php');
        }
        require_once '../../models/admin_dao/ViasModel.php';
        $viasModel = new ViasModel();
        $vias = $viasModel->getAllVias();
    ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h4>Agregar Nueva Intersección</h4>
                    </div>
                    <div class="card-body">
                        <form action="../../controllers/admin_controller/add_intersection.php" method="POST">
                            <div class="mb-3">
                                <label for="via1" class="form-label">Primera Vía</label>
                                <select class="form-select" id="via1" name="id_via_interseccion_1" required>
                                    <option value="">Seleccione una vía</option>
                                    <?php foreach($vias as $via): ?>
                                        <option value="<?php echo $via['id_via']; ?>">
                                            <?php echo $via['tipo'] . ' ' . $via['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="distancia_via1" class="form-label">Distancia desde inicio de la primera vía (KM)</label>
                                <input type="number" step="0.01" class="form-control" id="distancia_via1" name="distancia_insterseccion_via1" required>
                            </div>
                            <div class="mb-3">
                                <label for="via2" class="form-label">Segunda Vía</label>
                                <select class="form-select" id="via2" name="id_via_inserseccion_2" required>
                                    <option value="">Seleccione una vía</option>
                                    <?php foreach($vias as $via): ?>
                                        <option value="<?php echo $via['id_via']; ?>">
                                            <?php echo $via['tipo'] . ' ' . $via['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="distancia_via2" class="form-label">Distancia desde inicio de la segunda vía (KM)</label>
                                <input type="number" step="0.01" class="form-control" id="distancia_via2" name="distancia_interseccion_via2" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción de la Intersección</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-info">Guardar Intersección</button>
                                <a href="home_admin.php" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación para evitar seleccionar la misma vía
        document.addEventListener('DOMContentLoaded', function() {
            const via1 = document.getElementById('via1');
            const via2 = document.getElementById('via2');
            
            via1.addEventListener('change', function() {
                checkVias();
            });
            
            via2.addEventListener('change', function() {
                checkVias();
            });
            
            function checkVias() {
                if(via1.value !== '' && via2.value !== '' && via1.value === via2.value) {
                    alert('No se puede seleccionar la misma vía en ambos campos.');
                    via2.value = '';
                }
            }
        });
    </script>
</body>
</html>