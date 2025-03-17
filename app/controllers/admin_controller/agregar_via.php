<?php
// Controlador para agregar vía
// ../../controllers/vias_controller/agregar_via.php
session_start();
if(!isset($_SESSION['usuario'])){
    header('Location: ../../views/login/login.php');
    exit();
}

require_once '../../models/admin_dao/ViasModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
    $longitud = isset($_POST['longitud']) ? (float)$_POST['longitud'] : 0;
    $inicio_via = isset($_POST['inicio_via']) ? trim($_POST['inicio_via']) : '';
    $fin_via = isset($_POST['fin_via']) ? trim($_POST['fin_via']) : '';
    $is_doble_sentido = isset($_POST['is_doble_sentido']) ? 1 : 0;
    
    // Validación básica
    if (empty($nombre) || empty($tipo) || $longitud <= 0 || empty($inicio_via) || empty($fin_via)) {
        $_SESSION['error'] = "Todos los campos son obligatorios y la longitud debe ser mayor que cero";
        header('Location: ../../views/administrator/add_via_form.php');
        exit();
    }
    
    $viasModel = new ViasModel();
    $resultado = $viasModel->agregarVia($nombre, $tipo, $longitud, $inicio_via, $fin_via, $is_doble_sentido);
    
    $viasModel->cerrarConexion();
    if ($resultado) {
        $_SESSION['success'] = "Vía agregada correctamente";
        header('Location: ../../views/administrator/home_admin.php');
    } else {
        $_SESSION['error'] = "Error al agregar la vía";
        header('Location: ../../views/administrator/add_via_form.php');
    }
    
    exit();
}
?>