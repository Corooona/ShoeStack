<?php
include("conexion.php");

// Obtener roles disponibles desde la base de datos usando PDO
$sqlRoles = "SELECT * FROM roles";
$stmtRoles = $conexion->prepare($sqlRoles);
$stmtRoles->execute();
$resultRoles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST["registrar"])) {
    // Obtener los valores del formulario
    $nombre = htmlspecialchars($_POST["nombre"]);
    $apellido = htmlspecialchars($_POST["apellido"]);
    $user = htmlspecialchars($_POST["user"]);
    $pass = $_POST["pass"];
    $rol = htmlspecialchars($_POST["rol"]);

    // Encriptar la contraseña
    $password_encriptado = password_hash($pass, PASSWORD_DEFAULT);

    // Verificar si el usuario ya existe en la base de datos
    $sqlVerificarUsuario = "SELECT id_usuario FROM usuario WHERE nombre_user = :user";
    $stmtVerificar = $conexion->prepare($sqlVerificarUsuario);
    $stmtVerificar->bindValue(":user", $user);
    $stmtVerificar->execute();
    $filas = $stmtVerificar->rowCount();

    if ($filas > 0) {
        echo "<script>alert('El nombre de usuario ya existe');</script>";
        echo "<script>window.location.href='lista-usuarios.php';</script>";
    } else {
        // Insertar el nuevo usuario en la base de datos
        $sqlInsertarUsuario = "INSERT INTO usuario(nombre, apellido, nombre_user, password, id_rol) 
                               VALUES(:nombre, :apellido, :user, :password, :rol)";
        $stmtInsertar = $conexion->prepare($sqlInsertarUsuario);
        $stmtInsertar->bindValue(":nombre", $nombre);
        $stmtInsertar->bindValue(":apellido", $apellido);
        $stmtInsertar->bindValue(":user", $user);
        $stmtInsertar->bindValue(":password", $password_encriptado);
        $stmtInsertar->bindValue(":rol", $rol);
        
        if ($stmtInsertar->execute()) {
            echo "<script>alert('Registro exitoso');</script>";
            echo "<script>window.location.href='lista-usuarios.php';</script>";
            exit();
        } else {
            $errorInfo = $stmtInsertar->errorInfo();
                echo "<script>alert('Error al registrarse: ".addslashes($errorInfo[2])."');</script>";
            
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShoeStock - Agregar Usuario</title>
    <link rel="stylesheet" href="styles/agregar-usuario.css">
</head>
<body>
    <?php include("header/header.php"); ?>
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
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
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
                        <input type="password" name="pass" placeholder="Contraseña" required>
                    </div>

                    <div class="form-group">
                        <select name="rol" required>
                            <option value="" disabled selected>Seleccione un rol</option>
                            <?php
                            foreach ($resultRoles as $row) {
                                echo "<option value='" . $row['id_rol'] . "'>" . htmlspecialchars($row['rol']) . "</option>";
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
