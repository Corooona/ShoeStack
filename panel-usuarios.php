<?php
include("conexion.php");

//CONSULTA PARA VER LOS USUARIOS
$usuarios = "SELECT * from usuario";

/*
DE AQUI VAMOS A OBTENER LOS DATOS  DEL USUARIO QUE ESTAMOS BUSCANDO
$nombre = mysqli_real_escape_string($conexion, $_POST["nombre"]);
$apellido = mysqli_real_escape_string($conexion, $_POST["apellido"]);
$user = mysqli_real_escape_string($conexion, $_POST["user"]);
$pass = mysqli_real_escape_string($conexion, $_POST["pass"]);
$rol = mysqli_real_escape_string($conexion, $_POST["rol"]);
*/

if(isset($_POST["registrar"])) {
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

    if ($filas > 0){
        echo "<script>alert('El nombre de usuario ya existe');</script>";
    } else {
        // Insertar el nuevo usuario en la base de datos
        $sqlusuario = "INSERT INTO usuario(nombre,apellido,nombre_user,password,id_rol) 
                       VALUES('$nombre','$apellido','$user', '$pass', '$rol')";
        $resultadousuario = $conexion->query($sqlusuario);

        if($resultadousuario) {
            echo "<script>alert('Registro exitoso');</script>";
        } else {
            echo "<script>alert('Error al registrarse');</script>";
        }
    }
}

if (isset($_POST["delete"])) {
    $user = mysqli_real_escape_string($conexion, $_POST["user"]);
    // Verificar si el usuario existe antes de intentar eliminarlo
    $query = "SELECT * FROM usuario WHERE nombre_user='$user'";
    $result = mysqli_query($conexion, $query);
    if (mysqli_num_rows($result) == 0) {
        // Mostrar un mensaje de error y evitar ejecutar la consulta de eliminación
        echo "<script>alert('El usuario no existe');</script>";
    } else {
        // El usuario existe, ejecutar la consulta de eliminación
        $eliminar = "DELETE FROM usuario WHERE nombre_user='$user'";
        $resultadoEliminar = mysqli_query($conexion, $eliminar);

        // Verificar si alguna fila fue afectada por la consulta de eliminación
        if (mysqli_affected_rows($conexion) > 0) {
            // Mostrar mensaje de éxito si se eliminó el usuario
            echo "<script>alert('Se ha eliminado con éxito');</script>";
        } else {
            // Mostrar mensaje de error si no se eliminó el usuario (esto podría ocurrir si el usuario ya fue eliminado)
            echo "<script>alert('No se pudo eliminar');</script>";
        }
    }
}

if (isset($_POST["update"])) {
    $nombre = mysqli_real_escape_string($conexion, $_POST["nombre"]);
    $apellido = mysqli_real_escape_string($conexion, $_POST["apellido"]);
    $user = mysqli_real_escape_string($conexion, $_POST["user"]);
    $pass = mysqli_real_escape_string($conexion, $_POST["pass"]);
    // Verificar si el usuario existe antes de intentar actualizar
    $query = "SELECT * FROM usuario WHERE nombre_user='$user'";
    $result = mysqli_query($conexion, $query);
    if (mysqli_num_rows($result) == 0) {
        // Mostrar un mensaje de error y evitar ejecutar la consulta de actualizar
        echo "<script>alert('El usuario no existe');</script>";
    } else {
        // El usuario existe, ejecutar la consulta de actualizar
        $actualizar = "UPDATE usuario SET nombre='$nombre', apellido='$apellido', nombre_user='$user', password='$pass'
        WHERE nombre_user='$user'";

        $resultadoUpdate = mysqli_query($conexion, $actualizar);

        // Verificar si alguna fila fue afectada por la consulta de eliminación
        if (mysqli_affected_rows($conexion) > 0) {
            // Mostrar mensaje de éxito si se eliminó el usuario
            echo "<script>alert('Se ha actualizado usuario con éxito');</script>";
        } else {
            // Mostrar mensaje de error si no se eliminó el usuario (esto podría ocurrir si el usuario ya fue eliminado)
            echo "<script>alert('No se pudo actualizar');</script>";
        }
    }
}


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <!-- FORMULARIO PARA REGISTRAR USUARIO -->
    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
        <input type="text" name="nombre" placeholder="Nombre(s)" required>
        <input type="text" name="apellido" placeholder="Apellido(s)" required>
        <input type="text" name="user" placeholder="Usuario" required>
        <input type="text" name="pass" placeholder="Contraseña" required>
        <input type="text" name="rol" placeholder="Rol" required>

        <button type="submit" name="registrar">Registrar</button>
        <button type="reset">Reset</button>
    </form>

    <!-- FORMULARIO PARA ELIMINAR USUARIO -->
    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
        <label">Usuario</label>
            <input name="user" required>
            <input type="submit" name="delete" value="Eliminar">
            <input type="reset" value="Limpiar">
    </form>

    <!-- FORMULARIO PARA ACTUALIZAR USUARIO -->
    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
        <input name="nombre" type="text" required>
        <input name="apellido" type="text" required>
        <input name="user" type="text" required>
        <input name="pass" type="text" required>
        <input type="submit" name="update" value="Actualizar">
        <input type="reset" value="Limpiar">
    </form>

    <!-- TABLA DONDE SE MUESTRAN LOS USUARIOS -->
    <table>
        <thead>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Apellido</th>
                <th scope="col">Usuario</th>
                <th scope="col">Contraseña</th>
                <th scope="col">id_rol</th>
            </tr>
        </thead>
        <tbody>
            <?php $resultado = mysqli_query($conexion, $usuarios);

            while ($row = mysqli_fetch_assoc($resultado)) {
                ?>
                <tr class="table-primary">
                    <td>
                        <?php echo $row["nombre"] ?>
                    </td>

                    <td>
                        <?php echo $row["apellido"] ?>
                    </td>

                    <td>
                        <?php echo $row["nombre_user"] ?>
                    </td>

                    <td>
                        <?php echo $row["password"] ?>
                    </td>

                    <td>
                        <?php echo $row["id_rol"] ?>
                    </td>


                    <td>
                </tr>
                <?php
            }
            mysqli_free_result($resultado) ?>
        </tbody>
    </table>
</body>

</html>