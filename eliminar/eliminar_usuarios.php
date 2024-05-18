<?php
include("../conexion.php");

// Verificar si se ha enviado el ID del usuario a eliminar
if (isset($_GET['id'])) {
    // Obtener el ID del usuario a eliminar
    $id_usuario_eliminar = $_GET['id'];

    // Consulta SQL para eliminar el usuario
    $sqlEliminarUsuario = "DELETE FROM usuario WHERE id_usuario = $id_usuario_eliminar";

    // Ejecutar la consulta
    if ($conexion->query($sqlEliminarUsuario) === TRUE) {
        // Redireccionar de nuevo a la página de lista de usuarios después de eliminar
        header("Location: ../lista-usuarios.php");
        exit();
    } else {
        echo "Error al eliminar usuario: " . $conexion->error;
    }
} else {
    // Si no se proporcionó un ID de usuario válido, redireccionar a la lista de usuarios
    header("Location: ../lista-usuarios.php");
    exit();
}
?>
