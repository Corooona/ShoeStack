<?php
include ("conexion.php");

// Consulta para ver el calzado
$calzado_query = "SELECT c.id_calzado, c.modelo, t.tipo_calzado, m.material, ma.marca, c.precio, c.cantidad 
                  FROM calzado c
                  INNER JOIN tipo t ON c.id_tipo = t.id_tipo
                  INNER JOIN material m ON c.id_material = m.id_material
                  INNER JOIN marca ma ON c.id_marca = ma.id_marca";

$resultado = mysqli_query($conexion, $calzado_query);

?>

<!DOCTYPE html>
<html>

<head>
    <title>ShoeStock - Pantalla Principal del Empleado</title>
    <link rel="stylesheet" href="styles/panel.css">
    <script src="https://kit.fontawesome.com/50ce43599f.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("header/header.php"); ?>
    <div class="main-container">
        <!-- PARTE SUPERIOR -->
        <div class="superior">
            <a></a>
            <label class="titulo-principal">LISTA DE CALZADO</label>
            <input type="text" placeholder="Buscar...">
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
                while ($row = mysqli_fetch_assoc($resultado)) {
                    ?>
                    <tr class="table-primary">
                        <td>
                            <a href="#"><i class="fa-solid fa-pen"></i></a>
                        </td>

                        <td>
                            <?php echo $row["id_calzado"] ?>
                        </td>

                        <td>
                            <?php echo $row["modelo"] ?>
                        </td>

                        <td>
                            <?php echo $row["tipo_calzado"] ?>
                        </td>

                        <td>
                            <?php
                            $talla_query = "SELECT talla FROM talla t
                                            INNER JOIN calzado_talla ct ON t.id_talla = ct.id_talla
                                            WHERE ct.id_calzado = " . $row["id_calzado"];
                            $talla_resultado = mysqli_query($conexion, $talla_query);
                            while ($talla_row = mysqli_fetch_assoc($talla_resultado)) {
                                echo $talla_row["talla"] . ", ";
                            }
                            ?>
                        </td>

                        <td>
                            <?php
                            $color_query = "SELECT c.nombre_color FROM color c
                                            INNER JOIN calzado_color cc ON c.id_color = cc.id_color
                                            WHERE cc.id_calzado = " . $row["id_calzado"];
                            $color_resultado = mysqli_query($conexion, $color_query);
                            while ($color_row = mysqli_fetch_assoc($color_resultado)) {
                                echo $color_row["nombre_color"] . ", ";
                            }
                            ?>
                        </td>

                        <td>
                            <?php echo $row["material"] ?>
                        </td>

                        <td>
                            <?php echo $row["marca"] ?>
                        </td>

                        <td>
                            <?php echo $row["precio"] ?>
                        </td>

                        <td>
                            <?php echo $row["cantidad"] ?>
                        </td>
                    </tr>
                    <?php
                }
                mysqli_free_result($resultado);
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>