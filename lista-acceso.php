<?php
include("conexion.php");

// Consulta SQL para obtener los registros de acceso usando PDO
$sqlRegistrosAcceso = "SELECT id_registro, id_usuario, nombre_usuario, fecha FROM registro_acceso";
$stmtRegistrosAcceso = $conexion->prepare($sqlRegistrosAcceso);
$stmtRegistrosAcceso->execute();

// Obtener todos los registros
$resultadoRegistrosAcceso = $stmtRegistrosAcceso->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShoeStock - Lista de Acceso</title>
    <link rel="stylesheet" href="styles/lista-acceso.css">
    <script src="https://kit.fontawesome.com/50ce43599f.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("header/header.php"); ?>
    <div class="main-container">
        <!-- PARTE SUPERIOR -->
        <div class="superior">
            <label class="titulo-principal">LISTA DE ACCESO</label>
        </div>

        <div class="datos">
            <!-- TABLA -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ID Usuario</th>
                        <th>Nombre Usuario</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Mostrar los registros de acceso
                    foreach ($resultadoRegistrosAcceso as $row) {
                    ?>
                        <tr class="table-primary">
                            <td><?php echo htmlspecialchars($row["id_registro"]); ?></td>
                            <td><?php echo htmlspecialchars($row["id_usuario"]); ?></td>
                            <td><?php echo htmlspecialchars($row["nombre_usuario"]); ?></td>
                            <td><?php echo htmlspecialchars($row["fecha"]); ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
