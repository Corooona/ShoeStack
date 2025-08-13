<?php
include("conexion.php");

// Consulta SQL para obtener todos los usuarios con su rol
$sqlUsuarios = "SELECT u.id_usuario, u.nombre, u.apellido, u.nombre_user, u.password, r.rol
                FROM usuario u
                INNER JOIN roles r ON u.id_rol = r.id_rol";

$resultadoUsuarios = $conexion->query($sqlUsuarios);
?>

<!DOCTYPE html>
<html>

<head>
    <title>ShoeStock - Lista Usuarios</title>
    <link rel="stylesheet" href="styles/lista-usuarios.css">
    <script src="https://kit.fontawesome.com/50ce43599f.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("header/header.php"); ?>
    <div class="main-container">
        <!-- PARTE SUPERIOR -->
        <div class="superior">
            <a href="agregar-usuario.php" class="add-new-btn">AÑADIR NUEVO</a>
            <label class="titulo-principal">LISTA DE USUARIOS</label>
        </div>

        <!-- TABLA -->
        <table>
            <thead>
                <tr>
                    <th>ACCIÓN</th>
                    <th>ID</th>
                    <th>NOMBRE</th>
                    <th>APELLIDO</th>
                    <th>NOMBRE DE USUARIO</th>
                    <th>ROL</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($resultadoUsuarios)) {
                ?>
                    <tr class="table-primary">
                        <td>
                            <a href="editar-usuario.php?id=<?php echo $row['id_usuario']; ?>"><i class="fa-solid fa-pen"></i></a>
                            <a href="eliminar/eliminar_usuarios.php?id=<?php echo $row['id_usuario']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')"><i class="fa-solid fa-trash"></i></a>
                        </td>

                        <td><?php echo $row["id_usuario"]; ?></td>
                        <td><?php echo $row["nombre"]; ?></td>
                        <td><?php echo $row["apellido"]; ?></td>
                        <td><?php echo $row["nombre_user"]; ?></td>
                        <td><?php echo $row["rol"]; ?></td>
                    </tr>
                <?php
                }
                mysqli_free_result($resultadoUsuarios);
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>
