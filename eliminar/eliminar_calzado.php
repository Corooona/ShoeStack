<?php
include("conexion.php");

// Verifica si se proporcionó un ID de calzado
if(isset($_GET['id']) && !empty($_GET['id'])) {
    // Obtiene el ID del calzado a eliminar
    $id_calzado = $_GET['id'];

    // Consulta SQL para eliminar el calzado
    $eliminar_query = "DELETE FROM calzado WHERE id_calzado = $id_calzado";

    // Ejecuta la consulta
    if(mysqli_query($conexion, $eliminar_query)) {
        // Redirige de vuelta a la página principal
        header("Location: panel.php");
        exit();
    } else {
        echo "Error al eliminar el calzado: " . mysqli_error($conexion);
    }
} else {
    echo "ID de calzado no proporcionado";
}
?>
