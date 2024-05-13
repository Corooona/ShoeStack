<?php
include ("conexion.php");

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

// Obtener roles disponibles desde la base de datos
$sqlRoles = "SELECT * FROM roles";
$resultRoles = $conexion->query($sqlRoles);

if (isset($_POST["registrar"])) {
    $nombre = mysqli_real_escape_string($conexion, $_POST["nombre"]);
    $apellido = mysqli_real_escape_string($conexion, $_POST["apellido"]);
    $user = mysqli_real_escape_string($conexion, $_POST["user"]);
    $pass = mysqli_real_escape_string($conexion, $_POST["pass"]);
    $rol = mysqli_real_escape_string($conexion, $_POST["rol"]);
    // Encriptar la contraseña
    $password_encriptado = sha1($pass);

    $sqluser = "SELECT id_usuario FROM usuario WHERE nombre_user = '$user'";
    $resultadouser = $conexion->query($sqluser);
    $filas = $resultadouser->num_rows;

    if ($filas > 0) {
        echo "<script>alert('El nombre de usuario ya existe');</script>";
    } else {
        // Insertar el nuevo usuario en la base de datos
        $sqlusuario = "INSERT INTO usuario(nombre,apellido,nombre_user,password,id_rol) 
                       VALUES('$nombre','$apellido','$user', '$pass', '$rol')";
        $resultadousuario = $conexion->query($sqlusuario);

        if ($resultadousuario) {
            $fecha_acceso = date("Y-m-d H:i:s");
            $sqlInsertarRegistroAcceso = "INSERT INTO registro_acceso (id_usuario, nombre_usuario, fecha) 
                                  VALUES ($id_usuario_accion, '$nombre_usuario_accion', '$fecha_acceso')";
            $resultadoInsertarRegistroAcceso = $conexion->query($sqlInsertarRegistroAcceso);
            
            session_unset();

echo "<script>alert('Registro exitoso');</script>";
        } else {
            echo "<script>alert('Error al registrarse');</script>";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>ShoeStock - Agregar Usuario</title>
    <link rel="stylesheet" href="styles/agregar-usuario.css">
</head>

<body>
    <?php include ("header/header.php"); ?>
    <div class="main-container">

        <div class="superior">
            <label class="titulo-principal">REGISTRAR USUARIO</label>
        </div>

        <div class="container">
            <div class="titulos">

                <h2>NOMBRE</h2>

                <h2>APELLIDO</h2>

                <h2>USUARIO</h2>

                <h2>CONTRASEÑA</h2>

                <h2>ROL</h2>


            </div>
            <div class="agregar">

                <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <div class="form-group">
                        <input type="text" name="nombre" placeholder="Nombre(s)" required>
                    </div>

                    <div class="form-group">
                        <input type="text" name="apellido" placeholder="Apellido(s)" required>
                    </div>

                    <div class="form-group">
                        <input type="text" name="user" placeholder="Usuario" required>
                    </div>

                    <div class="form-group">
                        <input type="text" name="pass" placeholder="Contraseña" required>
                    </div>


                    <div class="form-group">
                        <select name="rol" required>
                            <option value="" disabled selected>Seleccione un rol</option>
                            <?php
                            // Iterar sobre los roles y crear opciones
                            while ($row = $resultRoles->fetch_assoc()) {
                                echo "<option value='" . $row['id_rol'] . "'>" . $row['rol'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>



                    <button type="submit" name="registrar" class="registrar">Registrar</button>
                    <button type="reset" class="reset">Reset</button>
                </form>
            </div>


        </div>
    </div>


</body>

</html>