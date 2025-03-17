<?php
// Controlador para agregar usuario
session_start();
if(!isset($_SESSION['usuario'])){
    header('Location: ../../views/login/login.php');
    exit();
}

require_once '../../models/admin_dao/UsuarioModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
    $nombre_usuario = isset($_POST['nombre_usuario']) ? trim($_POST['nombre_usuario']) : '';
    $contrasenia = isset($_POST['contrasenia']) ? $_POST['contrasenia'] : '';
    $confirmar_contrasenia = isset($_POST['confirmar_contrasenia']) ? $_POST['confirmar_contrasenia'] : '';
    $rol = isset($_POST['rol']) ? (int)$_POST['rol'] : 0;
    
    // Validación básica
    if (empty($nombre) || empty($apellido) || empty($nombre_usuario) || empty($contrasenia) || empty($rol)) {
        $_SESSION['error'] = "Todos los campos son obligatorios";
        header('Location: ../../views/administrator/add_user_form.php');
        exit();
    }
    
    if ($contrasenia !== $confirmar_contrasenia) {
        $_SESSION['error'] = "Las contraseñas no coinciden";
        header('Location: ../../views/administrator/add_user_form.php');
        exit();
    }
    
    // Hash de la contraseña
    $contrasenia_hash = password_hash($contrasenia, PASSWORD_DEFAULT);
    
    $usuarioModel = new UsuarioModel();
    
    // Verificar si el nombre de usuario ya existe
    if ($usuarioModel->checkUsuarioExists($nombre_usuario)) {
        $_SESSION['error'] = "El nombre de usuario ya existe, por favor elija otro";
        header('Location: ../../views/administrator/add_user_form.php');
        exit();
    }
    
    $resultado = $usuarioModel->agregarUsuario($nombre, $apellido, $nombre_usuario, $contrasenia_hash, $rol);
    
    $usuarioModel->cerrarConexion();
    if ($resultado) {
        $_SESSION['success'] = "Usuario agregado correctamente";
        header('Location: ../../views/administrator/home_admin.php');
    } else {
        $_SESSION['error'] = "Error al agregar el usuario";
        header('Location: ../../views/administrator/add_user_form.php');
    }
    
    exit();
}
?>