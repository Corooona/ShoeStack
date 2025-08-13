<?php
include("conexion.php");

//SECCION DE REGISTRO CALZADO
// Obtener tipos de calzado de la base de datos
$sqlTipos = "SELECT * FROM tipo";
$resultadoTipos = $conexion->query($sqlTipos);
// Obtener materiales de calzado de la base de datos
$sqlMateriales = "SELECT * FROM material";
$resultadoMateriales = $conexion->query($sqlMateriales);
// Obtener marcas de calzado de la base de datos
$sqlMarcas = "SELECT * FROM marca";
$resultadoMarcas = $conexion->query($sqlMarcas);

// Obtener calzado de la base de datos
$sqlCalzado = "SELECT * FROM calzado";
$resultadoCalzado = $conexion->query($sqlCalzado);

//SECION DE REGISTRO COLOR
// Obtener color de la base de datos
$sqlColor = "SELECT * FROM color";
$resultadoColor = $conexion->query($sqlColor);

//SECION DE REGISTRO TALLA
// Obtener talla de la base de datos
$sqlTalla = "SELECT * FROM talla";
$resultadoTalla = $conexion->query($sqlTalla);

//SECCION TABLA DE LOS DATOS
$sqlCalzadoTabla = "SELECT c.modelo, t.tipo_calzado AS tipo, mt.material AS material, m.marca, c.precio, c.cantidad 
               FROM calzado c
               INNER JOIN tipo t ON c.id_tipo = t.id_tipo
               INNER JOIN material mt ON c.id_material = mt.id_material
               INNER JOIN marca m ON c.id_marca = m.id_marca";

//SECCION TABLA DE COLORES

$sqlColorTabla = "SELECT cc.id_calzado, c.modelo, co.nombre_color
               FROM calzado_color cc
               INNER JOIN calzado c ON cc.id_calzado = c.id_calzado
               INNER JOIN color co ON cc.id_color = co.id_color";

// SECCION TABLA DE TALLAS
$sqlTallaTabla = "SELECT ct.id_calzado, c.modelo, t.talla
              FROM calzado_talla ct
              INNER JOIN calzado c ON ct.id_calzado = c.id_calzado
              INNER JOIN talla t ON ct.id_talla = t.id_talla";


// Eliminación de colores
if (isset($_POST["eliminar-color"])) {
    $modelo = mysqli_real_escape_string($conexion, $_POST["id_calzado"]);
    $color = mysqli_real_escape_string($conexion, $_POST["id_color"]);

    $queryEliminarColor = "DELETE FROM calzado_color WHERE id_calzado = '$modelo' AND id_color = '$color'";
    $resultadoEliminarColor = $conexion->query($queryEliminarColor);

    if ($resultadoEliminarColor) {
        echo "<script>alert('Color eliminado correctamente');</script>";
    } else {
        echo "<script>alert('Error al eliminar el color');</script>";
    }
}

// Eliminación de tallas
if (isset($_POST["eliminar-talla"])) {
    $modelo = mysqli_real_escape_string($conexion, $_POST["id_calzado"]);
    $talla = mysqli_real_escape_string($conexion, $_POST["id_talla"]);

    $queryEliminarTalla = "DELETE FROM calzado_talla WHERE id_calzado = '$modelo' AND id_talla = '$talla'";
    $resultadoEliminarTalla = $conexion->query($queryEliminarTalla);

    if ($resultadoEliminarTalla) {
        echo "<script>alert('Talla eliminada correctamente');</script>";
    } else {
        echo "<script>alert('Error al eliminar la talla');</script>";
    }
}


if (isset($_POST["registrar"])) {
    $modelo = mysqli_real_escape_string($conexion, $_POST["modelo"]);
    $tipo = mysqli_real_escape_string($conexion, $_POST["tipo"]);
    $material = mysqli_real_escape_string($conexion, $_POST["material"]);
    $marca = mysqli_real_escape_string($conexion, $_POST["marca"]);
    $precio = mysqli_real_escape_string($conexion, $_POST["precio"]);
    $cantidad = mysqli_real_escape_string($conexion, $_POST["cantidad"]);

    $queryCalzado = "SELECT id_calzado FROM calzado WHERE modelo = '$modelo'";
    $conecQueryCalzado = $conexion->query($queryCalzado);
    $filas = $conecQueryCalzado->num_rows;

    if ($filas > 0) {
        echo "<script>alert('El modelo ya existe');</script>";
    } else {
        // Insertar el nuevo modelo
        $sqlInsertarModelo = "INSERT INTO calzado(modelo,id_tipo,id_material,id_marca,precio,cantidad) 
                       VALUES('$modelo','$tipo','$material', '$marca', '$precio', '$cantidad')";
        $conecCalzado = $conexion->query($sqlInsertarModelo);

        if ($conecCalzado) {
            echo "<script>alert('Registro exitoso');</script>";
        } else {
            echo "<script>alert('Error al registrarse');</script>";
        }
    }
}



if (isset($_POST["registrar-color"])) {
    $modelo = mysqli_real_escape_string($conexion, $_POST["id_calzado"]);
    // Obtener los colores seleccionados
    $colores = $_POST["colores"];

    foreach ($colores as $color) {
        // Verificar si el par (id_calzado, id_color) ya existe en la tabla calzado_color
        $queryCalzadoColor = "SELECT id_cal_color FROM calzado_color WHERE id_calzado = '$modelo' AND id_color = '$color'";
        $conecQueryCalzadoColor = $conexion->query($queryCalzadoColor);
        $filas = $conecQueryCalzadoColor->num_rows;

        if ($filas > 0) {
            echo "<script>alert('El color ya está dentro del modelo');</script>";
        } else {
            // Insertar el nuevo color para el modelo
            $sqlInsertarColor = "INSERT INTO calzado_color(id_calzado,id_color) 
                           VALUES('$modelo','$color')";
            $conecCalzado = $conexion->query($sqlInsertarColor);

            if ($conecCalzado) {
                echo "<script>alert('Registro exitoso');</script>";
            } else {
                echo "<script>alert('Error al registrarse');</script>";
            }
        }
    }
}

if (isset($_POST["registrar-talla"])) {
    $modelo = mysqli_real_escape_string($conexion, $_POST["id_calzado"]);
    // Verificar si se han seleccionado tallas
    if(isset($_POST["tallas"]) && !empty($_POST["tallas"])) {
        // Recorrer todas las tallas seleccionadas
        foreach($_POST["tallas"] as $talla) {
            // Verificar si la talla ya está asociada al modelo
            $queryCalzadoTalla = "SELECT id_cal_talla FROM calzado_talla WHERE id_calzado = '$modelo' AND id_talla = '$talla'";
            $conecQueryCalzadoTalla = $conexion->query($queryCalzadoTalla);
            $filas = $conecQueryCalzadoTalla->num_rows;

            if ($filas > 0) {
                echo "<script>alert('La talla ya está dentro del modelo');</script>";
            } else {
                // Insertar la nueva talla para el modelo seleccionado
                $sqlInsertarTalla = "INSERT INTO calzado_talla(id_calzado,id_talla) 
                           VALUES('$modelo','$talla')";
                $conecCalzado = $conexion->query($sqlInsertarTalla);

                if (!$conecCalzado) {
                    echo "<script>alert('Error al registrar la talla');</script>";
                }
            }
        }
        echo "<script>alert('Registro exitoso de tallas');</script>";
    } else {
        echo "<script>alert('Debe seleccionar al menos una talla');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro De Calzado</title>
</head>

<body>

    <!-- REGISTRO DEL CALZADO -->
    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
        <input type="text" name="modelo" placeholder="Modelo" required>
        <select name="tipo" required>
            <option value="">Selecciona un tipo</option>
            <?php
            // Mostrar opciones de tipos de calzado
            while ($row = $resultadoTipos->fetch_assoc()) {
                echo "<option value='" . $row['id_tipo'] . "'>" . $row['tipo_calzado'] . "</option>";
            }
            ?>
        </select>
        <select name="material" required>
            <option value="">Selecciona un material</option>
            <?php

            while ($row = $resultadoMateriales->fetch_assoc()) {
                echo "<option value='" . $row['id_material'] . "'>" . $row['material'] . "</option>";
            }
            ?>
        </select>
        <select name="marca" required>
            <option value="">Selecciona una marca</option>
            <?php
            // Mostrar opciones de marca
            while ($row = $resultadoMarcas->fetch_assoc()) {
                echo "<option value='" . $row['id_marca'] . "'>" . $row['marca'] . "</option>";
            }
            ?>
        </select>
        <input type="text" name="precio" placeholder="Precio" required>
        <input type="text" name="cantidad" placeholder="Stock" required>

        <button type="submit" name="registrar">Registrar</button>
        <button type="reset">Reset</button>
    </form>

    <!-- REGISTRO DEL COLOR -->
    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
    <select name="id_calzado" required>
        <option value="">Selecciona un modelo</option>
        <?php
        // Mostrar opciones de modelo
        while ($row = $resultadoCalzado->fetch_assoc()) {
            echo "<option value='" . $row['id_calzado'] . "'>" . $row['modelo'] . "</option>";
        }
        ?>
    </select>
    <label>Colores disponibles:</label><br>
    <?php
    // Mostrar checkboxes para los colores
    while ($row = $resultadoColor->fetch_assoc()) {
        echo "<input type='checkbox' name='colores[]' value='" . $row['id_color'] . "'>" . $row['nombre_color'] . "<br>";
    }
    ?>
    <button type="submit" name="registrar-color">Registrar</button>
    <button type="reset">Reset</button>
</form>

<?php
$resultadoCalzado->data_seek(0); // Reiniciar puntero de calzado
$resultadoColor->data_seek(0); // Reiniciar puntero de colores
?>
<!-- Eliminación de colores -->
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
        <select name="id_calzado" required>
            <option value="">Selecciona un modelo</option>
            <?php while ($row = $resultadoCalzado->fetch_assoc()) { ?>
                <option value="<?php echo $row['id_calzado']; ?>"><?php echo $row['modelo']; ?></option>
            <?php } ?>
        </select>
        <select name="id_color" required>
            <option value="">Selecciona un Color</option>
            <?php while ($row = $resultadoColor->fetch_assoc()) { ?>
                <option value="<?php echo $row['id_color']; ?>"><?php echo $row['nombre_color']; ?></option>
            <?php } ?>
        </select>

        <button type="submit" name="eliminar-color">Eliminar</button>
    </form>
    


     <!-- REGISTRO DE TALLA -->
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
<select name="id_calzado" required>
            <option value="">Selecciona un modelo</option>
            <?php
            // Mostrar opciones de marca
            $resultadoCalzado->data_seek(0);
            while ($row = $resultadoCalzado->fetch_assoc()) {
                echo "<option value='" . $row['id_calzado'] . "'>" . $row['modelo'] . "</option>";
            }
            ?>
        </select>
    <div>
        <label>Selecciona las tallas:</label><br>
        <?php
        // Mostrar opciones de tallas como checkboxes
        while ($row = $resultadoTalla->fetch_assoc()) {
            echo "<input type='checkbox' name='tallas[]' value='" . $row['id_talla'] . "'>" . $row['talla'] . "<br>";
        }
        ?>
    </div>

    <button type="submit" name="registrar-talla">Registrar</button>
    <button type="reset">Reset</button>
</form> 

 <!-- Eliminación de tallas -->
 <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
        <select name="id_calzado" required>
            <option value="">Selecciona un modelo</option>
            <?php while ($row = $resultadoCalzado->fetch_assoc()) { ?>
                <option value="<?php echo $row['id_calzado']; ?>"><?php echo $row['modelo']; ?></option>
            <?php } ?>
        </select>
        <select name="id_talla" required>
            <option value="">Selecciona una Talla</option>
            <?php while ($row = $resultadoTalla->fetch_assoc()) { ?>
                <option value="<?php echo $row['id_talla']; ?>"><?php echo $row['talla']; ?></option>
            <?php } ?>
        </select>

        <button type="submit" name="eliminar-talla">Eliminar</button>
    </form>
    
    <!-- TABLA DONDE SE MUESTRAN LOS CALZADOS -->
    <table>
        <thead>
            <tr>
                <th scope="col">Modelo</th>
                <th scope="col">Tipo</th>
                <th scope="col">Material</th>
                <th scope="col">Marca</th>
                <th scope="col">Precio</th>
                <th scope="col">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $resultadoTablaCalzado = $conexion->query($sqlCalzadoTabla);
            while ($row = mysqli_fetch_assoc($resultadoTablaCalzado)) {
                ?>
                <tr>
                    <td>
                        <?php echo $row["modelo"] ?>
                    </td>

                    <td>
                        <?php echo $row["tipo"] ?>
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


                    <td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>


    <!-- TABLA DONDE SE MUESTRAN LOS COLORES -->
    <table>
        <thead>
            <tr>
                <th scope="col">Modelo</th>
                <th scope="col">Color</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $resultadoTablaColor = $conexion->query($sqlColorTabla);
            while ($row = mysqli_fetch_assoc($resultadoTablaColor)) {
                ?>
                <tr>
                    <td>
                        <?php echo $row["modelo"] ?>
                    </td>

                    <td>
                        <?php echo $row["nombre_color"] ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

    <!-- TABLA DONDE SE MUESTRAN LAS TALLAS -->
    <table>
        <thead>
            <tr>
                <th scope="col">Modelo</th>
                <th scope="col">Talla</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $resultadoTablaTalla = $conexion->query($sqlTallaTabla);
            while ($row = mysqli_fetch_assoc($resultadoTablaTalla)) {
                ?>
                <tr>
                    <td>
                        <?php echo $row["modelo"] ?>
                    </td>

                    <td>
                        <?php echo $row["talla"] ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</body>

</html>