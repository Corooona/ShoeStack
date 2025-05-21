<?php
include("../conexion.php");

// Verificar si se ha enviado el ID del usuario a eliminar
if (isset($_GET['id'])) {
    // Obtener el ID del usuario a eliminar
    $id_usuario_eliminar = $_GET['id'];

    try {
        // Usar consultas preparadas para mayor seguridad
        $sqlEliminarUsuario = "DELETE FROM usuario WHERE id_usuario = :id_usuario";
        $stmt = $conexion->prepare($sqlEliminarUsuario);
        $stmt->bindParam(':id_usuario', $id_usuario_eliminar, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            // Redireccionar solo si la eliminación fue exitosa
            header("Location: ../lista-usuarios.php?success=1");
            exit();
        } else {
            // Mostrar error y no redireccionar
            $errorInfo = $stmt->errorInfo();
            header("Location: ../lista-usuarios.php?error=" . urlencode($errorInfo[2]));
            exit();
        }
    } catch (PDOException $e) {
        // Manejar errores de PDO
        header("Location: ../lista-usuarios.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Si no se proporcionó un ID de usuario válido
    header("Location: ../lista-usuarios.php?error=ID no proporcionado");
    exit();
}
?>