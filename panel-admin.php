<!-- Archivo mejorado 3 -->
<?php
include ("conexion.php");

// Variable para almacenar el término de búsqueda 
$termino_busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : "";


// Consulta SQL para obtener los calzados que coinciden con el término de búsqueda y tienen stock disponible
$sqlBuscarCalzados = "SELECT c.id_calzado, c.modelo, t.tipo_calzado, m.material, ma.marca, c.precio, c.cantidad 
                      FROM calzado c
                      INNER JOIN tipo t ON c.id_tipo = t.id_tipo
                      INNER JOIN material m ON c.id_material = m.id_material
                      INNER JOIN marca ma ON c.id_marca = ma.id_marca
                      WHERE c.modelo LIKE :termino_busqueda AND c.cantidad > 0";
$stmt=$conexion->prepare($sqlBuscarCalzados);
$stmt->bindValue(":termino_busqueda", "%" . $termino_busqueda . "%" );
$stmt->execute();
$resultadoBusqueda = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>

<head>
    <title>ShoeStock - Pantalla Principal del Administrador</title>
    <link rel="stylesheet" href="styles/panel.css">
    <script src="https://kit.fontawesome.com/50ce43599f.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include ("header/header.php"); ?>
    <div class="main-container">
        <!-- PARTE SUPERIOR -->
        <div class="superior">
            <a href="agregar-calzado.php" class="add-new-btn">AÑADIR NUEVO</a>
            <label class="titulo-principal">LISTA DE CALZADO</label>

            <div class="buscar">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="GET">
                    <input type="text" name="buscar" placeholder="Buscar...">
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
                    foreach($resultadoBusqueda as $row){
                        //Cinsultas optimizadas para obtener talla y color en una sola consulta
                        $talla_query="SELECT GROUP_CONCAT(talla) as tallas FROM talla t
                        INNER JOIN calzado_talla ct ON t.id_talla =ct.id_talla
                        WHERE ct.id_calzado=:id_calzado";

                        $stmt_talla=$conexion->prepare($talla_query);
                        $stmt_talla->execute([':id_calzado'=>$row['id_calzado']]);
                        $talla_row=$stmt_talla->fetch();
                        
                        $color_query="SELECT GROUP_CONCAT(c.nombre_color) AS colores FROM color c
                        INNER JOIN calzado_color cc ON c.id_color=cc.id_color
                        WHERE cc.id_calzado=:id_calzado";

                        $stmt_color=$conexion->prepare($color_query);
                        $stmt_color->execute([":id_calzado"=>$row['id_calzado']]);
                        $color_row=$stmt_color->fetch();

                    
                    ?>
                    <tr class="table-primary">
                        <td>
                            <a href="editar-calzado.php?id=<?php echo $row['id_calzado']; ?>"><i
                                    class="fa-solid fa-pen"></i></a>
                            <a href="eliminar/eliminar_calzado.php?id=<?php echo $row['id_calzado']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este calzado?')"><i
                                    class="fa-solid fa-trash"></i></a>
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
                            <?php echo $talla_row["tallas"] . ", ";?>
                        </td>

                        <td>
                            <?php echo $color_row["colores"] . ", ";?>
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
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>