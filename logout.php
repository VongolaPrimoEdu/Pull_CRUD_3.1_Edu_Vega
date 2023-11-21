<?php
session_start();

// Elimina todas las variables de sesión
$_SESSION = array();

// Invalida la sesión
session_destroy();

// Redirige al usuario al formulario de inicio de sesión
header("Location: form_login.php");
exit;
?>