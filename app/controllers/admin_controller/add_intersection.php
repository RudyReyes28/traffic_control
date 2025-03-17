<?php
// Controlador para agregar intersección
// ../../controllers/interseccion_controller/agregar_interseccion.php
session_start();
if(!isset($_SESSION['usuario'])){
    header('Location: ../../views/login/login.php');
    exit();
}

require_once '../../models/admin_dao/InterseccionModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_via_interseccion_1 = isset($_POST['id_via_interseccion_1']) ? (int)$_POST['id_via_interseccion_1'] : 0;
    $distancia_insterseccion_via1 = isset($_POST['distancia_insterseccion_via1']) ? (float)$_POST['distancia_insterseccion_via1'] : 0;
    $id_via_inserseccion_2 = isset($_POST['id_via_inserseccion_2']) ? (int)$_POST['id_via_inserseccion_2'] : 0;
    $distancia_interseccion_via2 = isset($_POST['distancia_interseccion_via2']) ? (float)$_POST['distancia_interseccion_via2'] : 0;
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    
    // Validación básica
    if ($id_via_interseccion_1 <= 0 || $id_via_inserseccion_2 <= 0 || 
        $distancia_insterseccion_via1 <= 0 || $distancia_interseccion_via2 <= 0) {
        $_SESSION['error'] = "Todos los campos son obligatorios y las distancias deben ser mayores que cero";
        header('Location: ../../views/administrator/add_intersection_form.php');
        exit();
    }
    
    if ($id_via_interseccion_1 == $id_via_inserseccion_2) {
        $_SESSION['error'] = "No se puede crear una intersección con la misma vía";
        header('Location: ../../views/administrator/add_intersection_form.php');
        exit();
    }
    
    $interseccionModel = new InterseccionModel();
    $resultado = $interseccionModel->agregarInterseccion(
        $id_via_interseccion_1, 
        $distancia_insterseccion_via1, 
        $id_via_inserseccion_2, 
        $distancia_interseccion_via2, 
        $descripcion
    );

    $interseccionModel->cerrarConexion();
    
    if ($resultado) {
        $_SESSION['success'] = "Intersección agregada correctamente";
        header('Location: ../../views/administrator/home_admin.php');
    } else {
        $_SESSION['error'] = "Error al agregar la intersección";
        header('Location: ../../views/administrator/add_intersection_form.php');
    }
    
    exit();
}
?>