<?php
    require_once '../../models/authentication_model/AuthenticateConection.php';
    $usser = $_POST['usuario'];
    $pass = $_POST['contrasenia'];
    
    $auth= new AuthenticateConection();
    $user = $auth->findUsser($usser);

    //print_r ($user)
    if ($user && password_verify($pass, $user['contrasenia'])){
        session_start();
        $_SESSION['id'] = $user['ID'];
        $_SESSION['nombre'] = $user['Nombre'];
        $_SESSION['apellido'] = $user['Apellido'];
        $_SESSION['usuario'] = $user['nombre_usuario'];
        $_SESSION['rol'] = $user['nombre_tipo'];

        switch ($user['rol']) {
            case 1:
                header('Location: ../../views/administrator/home_admin.php');
                $auth->closeConection();
                exit;
            case 2:
                header('Location: ../../views/traffic_monitor/traffic_view.php');
                $auth->closeConection();
                exit;
            case 3:
                header('Location: ../../views/traffic_supervisor/home_supervisor.php');
                $auth->closeConection();
                exit;
            default:
                header('Location: ../../views/login/login.php');
                $auth->closeConection();
                exit;
        }
    }else{
        header('Location: ../../views/login/login.php');
    }

?>