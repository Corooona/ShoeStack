<?php
// Inicia la sesión si no está iniciada
session_start();

// Destruye la sesión actual
session_destroy();

// Redirige a la página de inicio o a donde desees
header("Location: ../index.php");
exit();

?>