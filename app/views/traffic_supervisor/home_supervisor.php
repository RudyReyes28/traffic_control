<?php
    session_start();
    if(!isset($_SESSION['usuario'])){
        header('Location: ../../views/login/login.php');
    }
    echo "Bienvenido ".$_SESSION['usuario'];
?>