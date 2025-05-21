<?php
include("conexion.php");

// Consulta SQL para obtener todos los usuarios con su rol usando PDO
$sqlUsuarios = "SELECT u.id_usuario, u.nombre, u.apellido, u.nombre_user, u.password, r.rol
                FROM usuario u
                INNER JOIN roles r ON u.id_rol = r.id_rol";
$stmtUsuarios = $conexion->prepare($sqlUsuarios);
$stmtUsuarios->execute();

// Obtener todos los registros de usuarios
$resultadoUsuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                foreach ($resultadoUsuarios as $row) {
                ?>
                    <tr class="table-primary">
                        <td>
                            <a href="editar-usuario.php?id=<?php echo htmlspecialchars($row['id_usuario']); ?>"><i class="fa-solid fa-pen"></i></a>
                            <a href="eliminar/eliminar_usuarios.php?id=<?php echo htmlspecialchars($row['id_usuario']); ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')"><i class="fa-solid fa-trash"></i></a>
                        </td>
                        <td><?php echo htmlspecialchars($row["id_usuario"]); ?></td>
                        <td><?php echo htmlspecialchars($row["nombre"]); ?></td>
                        <td><?php echo htmlspecialchars($row["apellido"]); ?></td>
                        <td><?php echo htmlspecialchars($row["nombre_user"]); ?></td>
                        <td><?php echo htmlspecialchars($row["rol"]); ?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>
