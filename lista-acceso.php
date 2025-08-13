<?php
include("conexion.php");

// Consulta SQL para obtener los registros de acceso
$sqlRegistrosAcceso = "SELECT id_registro, id_usuario, nombre_usuario, fecha FROM registro_acceso";
$resultadoRegistrosAcceso = $conexion->query($sqlRegistrosAcceso);
?>

<!DOCTYPE html>
<html>

<head>
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
                while ($row = mysqli_fetch_assoc($resultadoRegistrosAcceso)) {
                ?>
                    <tr class="table-primary">
                        <td><?php echo $row["id_registro"]; ?></td>
                        <td><?php echo $row["id_usuario"]; ?></td>
                        <td><?php echo $row["nombre_usuario"]; ?></td>
                        <td><?php echo $row["fecha"]; ?></td>
                    </tr>
                <?php
                }
                mysqli_free_result($resultadoRegistrosAcceso);
                ?>
            </tbody>
        </table>
        </div>
    </div>
</body>

</html>
