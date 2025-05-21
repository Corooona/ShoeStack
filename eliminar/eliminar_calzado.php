<?php
include("../conexion.php");

// Verificar si se ha enviado un ID de calzado para eliminar
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_calzado = $_GET['id'];
    
    try {
        // Iniciar transacción para garantizar la integridad de los datos
        $conexion->beginTransaction();
        
        // Primero eliminar registros relacionados en calzado_color
        $sqlEliminarColor = "DELETE FROM calzado_color WHERE id_calzado = :id_calzado";
        $stmtColor = $conexion->prepare($sqlEliminarColor);
        $stmtColor->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
        $stmtColor->execute();
        
        // Luego eliminar registros relacionados en calzado_talla
        $sqlEliminarTalla = "DELETE FROM calzado_talla WHERE id_calzado = :id_calzado";
        $stmtTalla = $conexion->prepare($sqlEliminarTalla);
        $stmtTalla->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
        $stmtTalla->execute();
        
        // Finalmente eliminar el calzado
        $sqlEliminarCalzado = "DELETE FROM calzado WHERE id_calzado = :id_calzado";
        $stmtCalzado = $conexion->prepare($sqlEliminarCalzado);
        $stmtCalzado->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
        $stmtCalzado->execute();
        
        // Confirmar la transacción
        $conexion->commit();
        
        // Redireccionar con mensaje de éxito
        header("Location: ../panel-admin.php?success=1");
        exit();
    } catch (PDOException $e) {
        // Revertir cambios en caso de error
        $conexion->rollBack();
        
        // Redireccionar con mensaje de excepción
        header("Location: ../panel-admin.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Redireccionar si no hay ID
    header("Location: ../panel-admin.php?error=ID_no_proporcionado");
    exit();
}
?>