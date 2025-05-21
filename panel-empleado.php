<?php
include("conexion.php");

// Variable para almacenar el término de búsqueda
$termino_busqueda = "";

// Verificar si se ha enviado un término de búsqueda
if (isset($_GET['buscar'])) {
    $termino_busqueda = $_GET['buscar'];
}

// Consulta SQL para obtener los calzados que coinciden con el término de búsqueda y tienen stock disponible
$sqlBuscarCalzados = "SELECT c.id_calzado, c.modelo, t.tipo_calzado, m.material, ma.marca, c.precio, c.cantidad 
                      FROM calzado c
                      INNER JOIN tipo t ON c.id_tipo = t.id_tipo
                      INNER JOIN material m ON c.id_material = m.id_material
                      INNER JOIN marca ma ON c.id_marca = ma.id_marca
                      WHERE c.modelo LIKE :termino_busqueda AND c.cantidad > 0";
$stmtBuscarCalzados = $conexion->prepare($sqlBuscarCalzados);
$stmtBuscarCalzados->bindValue(":termino_busqueda", "%" . $termino_busqueda . "%");
$stmtBuscarCalzados->execute();
$resultadoBusqueda = $stmtBuscarCalzados->fetchAll(PDO::FETCH_ASSOC);

// Consulta para ver el calzado
$calzado_query = "SELECT c.id_calzado, c.modelo, t.tipo_calzado, m.material, ma.marca, c.precio, c.cantidad 
                  FROM calzado c
                  INNER JOIN tipo t ON c.id_tipo = t.id_tipo
                  INNER JOIN material m ON c.id_material = m.id_material
                  INNER JOIN marca ma ON c.id_marca = ma.id_marca";
$stmtCalzado = $conexion->query($calzado_query);
$resultado = $stmtCalzado->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShoeStock - Pantalla Principal del Administrador</title>
    <link rel="stylesheet" href="styles/panel.css">
    <script src="https://kit.fontawesome.com/50ce43599f.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("header/header.php"); ?>
    <div class="main-container">
        <!-- PARTE SUPERIOR -->
        <div class="superior">
            <a href="agregar-calzado.php" class="add-new-btn">AÑADIR NUEVO</a>
            <label class="titulo-principal">LISTA DE CALZADO</label>

            <div class="buscar">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="GET">
                    <input type="text" name="buscar" placeholder="Buscar..." value="<?php echo htmlspecialchars($termino_busqueda); ?>">
                    <button type="submit"><i class="fa-sharp fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
        </div>

        <!-- TABLA -->
        <table>
            <thead>
                <tr>
                    <th>ACCIÓN</th>
                    <th>ID</th>
                    <th>Modelo</th>
                    <th>Tipo</th>
                    <th>Talla</th>
                    <th>Color</th>
                    <th>Material</th>
                    <th>Marca</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($resultadoBusqueda as $row) {
                    ?>
                    <tr class="table-primary">
                        <td>
                            <a href="edit-cal-emp.php?id=<?php echo htmlspecialchars($row['id_calzado']); ?>"><i class="fa-solid fa-pen"></i></a>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["id_calzado"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["modelo"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["tipo_calzado"]); ?>
                        </td>

                        <td>
                            <?php
                            // Obtener tallas relacionadas con este calzado
                            $talla_query = "SELECT talla FROM talla t
                                            INNER JOIN calzado_talla ct ON t.id_talla = ct.id_talla
                                            WHERE ct.id_calzado = :id_calzado";
                            $stmtTalla = $conexion->prepare($talla_query);
                            $stmtTalla->bindParam(':id_calzado', $row["id_calzado"], PDO::PARAM_INT);
                            $stmtTalla->execute();
                            $talla_resultado = $stmtTalla->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($talla_resultado as $talla_row) {
                                echo htmlspecialchars($talla_row["talla"]) . ", ";
                            }
                            ?>
                        </td>

                        <td>
                            <?php
                            // Obtener colores relacionados con este calzado
                            $color_query = "SELECT c.nombre_color FROM color c
                                            INNER JOIN calzado_color cc ON c.id_color = cc.id_color
                                            WHERE cc.id_calzado = :id_calzado";
                            $stmtColor = $conexion->prepare($color_query);
                            $stmtColor->bindParam(':id_calzado', $row["id_calzado"], PDO::PARAM_INT);
                            $stmtColor->execute();
                            $color_resultado = $stmtColor->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($color_resultado as $color_row) {
                                echo htmlspecialchars($color_row["nombre_color"]) . ", ";
                            }
                            ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["material"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["marca"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["precio"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["cantidad"]); ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>
