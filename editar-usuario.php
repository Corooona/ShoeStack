<?php
include("conexion.php");

// Función para obtener el nombre del rol del usuario
function obtenerNombreRol($conexion, $id_rol)
{
    $sql = "SELECT rol FROM roles WHERE id_rol = :id_rol";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['rol'] : "Rol desconocido";
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_usuario = $_GET['id'];

    // Consulta para obtener la información del usuario usando PDO
    $sqlUsuario = "SELECT * FROM usuario WHERE id_usuario = :id_usuario";
    $stmtUsuario = $conexion->prepare($sqlUsuario);
    $stmtUsuario->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmtUsuario->execute();
    $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $nombre = $usuario['nombre'];
        $apellido = $usuario['apellido'];
        $nombre_usuario = $usuario['nombre_user'];
        $password = $usuario['password'];
        $rol_id = $usuario['id_rol'];
    } else {
        echo "No se encontró el usuario con el ID proporcionado.";
    }
} else {
    echo "ID de usuario no proporcionado.";
}

// Consultas para obtener los roles disponibles
$sqlRoles = "SELECT * FROM roles";
$stmtRoles = $conexion->prepare($sqlRoles);
$stmtRoles->execute();
$resultadoRoles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST["actualizar"])) {
    // Obtener los datos actualizados del formulario
    $nombre_actualizado = htmlspecialchars($_POST["nombre"]);
    $apellido_actualizado = htmlspecialchars($_POST["apellido"]);
    $password_actualizado = ($_POST["password"]);
    $rol_actualizado = $_POST["rol"];

    // Si se ha proporcionado una nueva contraseña, encriptarla
    if (!empty($password_actualizado)) {
        $password_actualizado = password_hash($password_actualizado, PASSWORD_DEFAULT);
    } else {
        // Si la contraseña está vacía, mantener la contraseña actual (sin cambios)
        $password_actualizado = $usuario['password'];
    }

    // Obtener el ID del usuario que realizó la acción
    session_start();
    $id_usuario_accion = $_SESSION['id_usuario'];

    // Consulta para obtener el nombre de usuario
    $sql_obtener_nombre_usuario = "SELECT nombre_user FROM usuario WHERE id_usuario = :id_usuario";
    $stmtUsuarioAccion = $conexion->prepare($sql_obtener_nombre_usuario);
    $stmtUsuarioAccion->bindParam(':id_usuario', $id_usuario_accion, PDO::PARAM_INT);
    $stmtUsuarioAccion->execute();
    $row_nombre_usuario = $stmtUsuarioAccion->fetch(PDO::FETCH_ASSOC);
    $nombre_usuario_accion = $row_nombre_usuario ? $row_nombre_usuario['nombre_user'] : '';

    // Actualizar la información del usuario
    $sqlActualizar = "UPDATE usuario 
                      SET nombre = :nombre, apellido = :apellido, password = :password, id_rol = :id_rol
                      WHERE id_usuario = :id_usuario";
    $stmtActualizar = $conexion->prepare($sqlActualizar);
    $stmtActualizar->bindParam(':nombre', $nombre_actualizado, PDO::PARAM_STR);
    $stmtActualizar->bindParam(':apellido', $apellido_actualizado, PDO::PARAM_STR);
    $stmtActualizar->bindParam(':password', $password_actualizado, PDO::PARAM_STR);
    $stmtActualizar->bindParam(':id_rol', $rol_actualizado, PDO::PARAM_INT);
    $stmtActualizar->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmtActualizar->execute();

    if ($stmtActualizar->rowCount() > 0) {
        // Insertar el registro en la tabla registro_acceso
        $fecha_acceso = date("Y-m-d H:i:s");
        $sqlInsertarRegistroAcceso = "INSERT INTO registro_acceso (id_usuario, nombre_usuario, fecha) 
                                      VALUES (:id_usuario, :nombre_usuario, :fecha_acceso)";
        $stmtInsertarRegistroAcceso = $conexion->prepare($sqlInsertarRegistroAcceso);
        $stmtInsertarRegistroAcceso->bindParam(':id_usuario', $id_usuario_accion, PDO::PARAM_INT);
        $stmtInsertarRegistroAcceso->bindParam(':nombre_usuario', $nombre_usuario_accion, PDO::PARAM_STR);
        $stmtInsertarRegistroAcceso->bindParam(':fecha_acceso', $fecha_acceso, PDO::PARAM_STR);
        $stmtInsertarRegistroAcceso->execute();

        echo "<script>alert('Datos actualizados correctamente');</script>";
        echo "<script>window.location.href='lista-usuarios.php';</script>";
        exit(); // Evitar ciclo de redirección
    } else {
        echo "<script>alert('Error al actualizar los datos');</script>";
        echo "<script>window.location.href='lista-usuarios.php';</script>";
        exit(); // Evitar ciclo de redirección
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShoeStock - Editar Usuario</title>
    <link rel="stylesheet" href="styles/editar-usuario.css">
</head>

<body>
    <?php include("header/header.php"); ?>

    <div class="main-container">
        <div class="superior">
            <label class="titulo-principal">EDITAR USUARIO</label>
        </div>

        <div class="container">
            <div class="titulos">
                <h2>NOMBRE</h2>
                <h2>APELLIDO</h2>
                <h2>USUARIO</h2>
                <h2>CONTRASEÑA</h2>
                <h2>ROL</h2>
            </div>

            <div class="datos">
                <form>
                    <div class="form-group">
                        <input type="text" value="<?php echo htmlspecialchars($nombre); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <input type="text" value="<?php echo htmlspecialchars($apellido); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <input type="text" value="<?php echo htmlspecialchars($nombre_usuario); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <input type="text" value="<?php echo htmlspecialchars($password); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <input type="text" value="<?php echo htmlspecialchars(obtenerNombreRol($conexion, $rol_id)); ?>" readonly>
                    </div>
                </form>
            </div>

            <div class="actualizar">
                <form action="<?php echo $_SERVER["PHP_SELF"] . "?id=$id_usuario"; ?>" method="POST">
                    <div class="form-group">
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" placeholder="Nombre">
                    </div>

                    <div class="form-group">
                        <input type="text" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" placeholder="Apellido">
                    </div>

                    <div class="form-group">
                        <input class="no-editar" type="text" value="<?php echo htmlspecialchars($nombre_usuario); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <input type="text" name="password" value="<?php echo htmlspecialchars($password); ?>" placeholder="Contraseña">
                    </div>

                    <div class="form-group">
                        <select name="rol">
                            <?php foreach ($resultadoRoles as $row): ?>
                                <option value="<?php echo $row['id_rol']; ?>" <?php if ($row['id_rol'] == $rol_id) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($row['rol']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="botones">
                        <button type="submit" name="actualizar" class="actualizar">Actualizar</button>
                        <button type="reset" class="reset">Reset</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</body>

</html>
