<?php
include ("conexion.php");

function obtenerNombreRol($conexion, $id_rol)
{
    $sql = "SELECT rol FROM roles WHERE id_rol = $id_rol";
    $resultado = mysqli_query($conexion, $sql);
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        return $row['rol'];
    } else {
        return "Rol desconocido";
    }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_usuario = $_GET['id'];

    // Consulta para obtener la información del usuario
    $consulta_usuario = "SELECT * FROM usuario WHERE id_usuario = $id_usuario";
    $resultado_usuario = mysqli_query($conexion, $consulta_usuario);

    if (mysqli_num_rows($resultado_usuario) > 0) {
        $usuario = mysqli_fetch_assoc($resultado_usuario);
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
$resultadoRoles = $conexion->query($sqlRoles);

if (isset($_POST["actualizar"])) {
    // Obtener los datos actualizados del formulario
    $nombre_actualizado = mysqli_real_escape_string($conexion, $_POST["nombre"]);
    $apellido_actualizado = mysqli_real_escape_string($conexion, $_POST["apellido"]);
    $password_actualizado = mysqli_real_escape_string($conexion, $_POST["password"]);
    $rol_actualizado = mysqli_real_escape_string($conexion, $_POST["rol"]);

    // Obtener el ID del usuario que realizó la acción
    session_start();
    $id_usuario_accion = $_SESSION['id_usuario'];
    // Consulta para obtener el nombre de usuario a partir del id_usuario
    $sql_obtener_nombre_usuario = "SELECT nombre_user FROM usuario WHERE id_usuario = $id_usuario_accion";
    $resultado_nombre_usuario = mysqli_query($conexion, $sql_obtener_nombre_usuario);

    if ($resultado_nombre_usuario && mysqli_num_rows($resultado_nombre_usuario) > 0) {
        $row_nombre_usuario = mysqli_fetch_assoc($resultado_nombre_usuario);
        $nombre_usuario_accion = $row_nombre_usuario['nombre_user'];
    } else {
        $nombre_usuario_accion = '';
    }
    session_abort();


    // Actualizar la información del usuario en la base de datos
    $sqlActualizar = "UPDATE usuario 
                    SET nombre = '$nombre_actualizado', apellido='$apellido_actualizado',
                    password='$password_actualizado', id_rol='$rol_actualizado'
                    WHERE id_usuario = $id_usuario";
    $resultadoActualizar = $conexion->query($sqlActualizar);

    if ($resultadoActualizar) {
        // Insertar el registro en la tabla registro_acceso
        $fecha_acceso = date("Y-m-d H:i:s");
        $sqlInsertarRegistroAcceso = "INSERT INTO registro_acceso (id_usuario, nombre_usuario, fecha) 
                                      VALUES ($id_usuario_accion, '$nombre_usuario_accion', '$fecha_acceso')";
        $resultadoInsertarRegistroAcceso = $conexion->query($sqlInsertarRegistroAcceso);

        echo "<script>alert('Datos actualizados correctamente');</script>";
        // Redirigir al panel de administrador después de la actualización
        echo "<script>window.location.href='panel-admin.php';</script>";
        exit(); // Evitar ciclo de redirección
    } else {
        echo "<script>alert('Error al actualizar los datos');</script>";
        // Redirigir al panel de administrador después de mostrar el error
        echo "<script>window.location.href='panel-admin.php';</script>";
        exit(); // Evitar ciclo de redirección
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>ShoeStock - Editar Usuario</title>
    <link rel="stylesheet" href="styles/editar-usuario.css">
</head>

<body>
    <?php include ("header/header.php"); ?>

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
                        <input type="text" value="<?php echo $nombre; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <input type="text" value="<?php echo $apellido; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <input type="text" value="<?php echo $nombre_usuario; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <input type="text" value="<?php echo $password; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <input type="text" value="<?php echo obtenerNombreRol($conexion, $rol_id); ?>" readonly>
                        <!-- Aquí se llama a la función obtenerNombreRol -->
                    </div>

                </form>
            </div>

            <div class="actualizar">
                <form action="<?php echo $_SERVER["PHP_SELF"] . "?id=$id_usuario"; ?>" method="POST">
                    <div class="form-group">
                        <input type="text" name="nombre" value="<?php echo $nombre; ?>" placeholder="Nombre">
                    </div>

                    <div class="form-group">
                        <input type="text" name="apellido" value="<?php echo $apellido; ?>" placeholder="Apellido">
                    </div>

                    <div class="form-group">
                        <input class="no-editar" type="text" value="<?php echo $nombre_usuario; ?>" readonly>
                    </div>

                    <div class="form-group">
                        <input type="text" name="password" value="<?php echo $password; ?>" placeholder="Contraseña">
                    </div>

                    <div class="form-group">
                        <select name="rol">
                            <?php while ($row = $resultadoRoles->fetch_assoc()): ?>
                                <option value="<?php echo $row['id_rol']; ?>" <?php if ($row['id_rol'] == $rol_id)
                                       echo 'selected'; ?>><?php echo $row['rol']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Botón para enviar la actualización -->
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