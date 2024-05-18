<?php
include("../conexion.php");

// Verificar si se ha enviado un ID de calzado para eliminar
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_calzado = $_GET['id'];

    // Consulta SQL para eliminar el calzado
    $sqlEliminarCalzado = "DELETE FROM calzado WHERE id_calzado = $id_calzado";

    // Ejecutar la consulta de eliminación
    if ($conexion->query($sqlEliminarCalzado) === TRUE) {
        echo "El calzado ha sido eliminado correctamente.";
    } else {
        echo "Error al eliminar el calzado: " . $conexion->error;
    }

    // Redireccionar de vuelta a la página principal
    header("Location: ../panel-admin.php");
    exit();
} else {
    echo "ID de calzado no proporcionado.";
}
?>
