<?php
// Controlador para agregar semáforo
session_start();
if(!isset($_SESSION['usuario'])){
    header('Location: ../../views/login/login.php');
    exit();
}

require_once '../../models/admin_dao/SemaforoModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_interseccion = isset($_POST['id_interseccion']) ? (int)$_POST['id_interseccion'] : 0;
    $posicion_salida = isset($_POST['posicion_salida']) ? trim($_POST['posicion_salida']) : '';
    $posicion_alto = isset($_POST['posicion_alto']) ? trim($_POST['posicion_alto']) : '';
    $estado_operativo = isset($_POST['estado_operativo']) ? trim($_POST['estado_operativo']) : '';
    
    // Validación básica
    if ($id_interseccion <= 0 || empty($posicion_salida) || empty($posicion_alto) || empty($estado_operativo)) {
        $_SESSION['error'] = "Todos los campos son obligatorios";
        header('Location: ../../views/administrator/add_traffic_ligth_form.php');
        exit();
    }
    
    $semaforoModel = new SemaforoModel();
    $resultado = $semaforoModel->agregarSemaforo($id_interseccion, $posicion_salida, $posicion_alto, $estado_operativo);
    
    $semaforoModel->cerrarConexion();
    if ($resultado) {
        $_SESSION['success'] = "Semáforo agregado correctamente";
        header('Location: ../../views/administrator/home_admin.php');
    } else {
        $_SESSION['error'] = "Error al agregar el semáforo";
        header('Location: ../../views/administrator/add_traffic_ligth_form.php');
    }
    
    exit();
}
?>