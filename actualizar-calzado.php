<?php
include("conexion.php");
// Obtener TIPOS de calzado de la base de datos
$sqlTipos = "SELECT * FROM tipo";
$resultadoTipos = $conexion->query($sqlTipos);
// Obtener MATERIALES de calzado de la base de datos
$sqlMateriales = "SELECT * FROM material";
$resultadoMateriales = $conexion->query($sqlMateriales);
// Obtener MARCAS de calzado de la base de datos
$sqlMarcas = "SELECT * FROM marca";
$resultadoMarcas = $conexion->query($sqlMarcas);


// Obtener todos los modelos de calzado para el autocompletado
$sqlModelos = "SELECT modelo FROM calzado";
$resultadoModelos = $conexion->query($sqlModelos);

// Inicializar variables para almacenar los datos del calzado seleccionado
$modelo = "";
$tipo = "";
$material = "";
$marca = "";
$precio = "";
$cantidad = "";

// Manejar el formulario enviado para actualizar los datos del calzado
if(isset($_POST["modelo_seleccionado"])) {
    $modelo_seleccionado = mysqli_real_escape_string($conexion, $_POST["modelo_seleccionado"]);
    
    // Consultar la base de datos para obtener los datos del calzado seleccionado
    $sqlDatosCalzado = "SELECT c.modelo, t.tipo_calzado, mt.material, m.marca, c.precio, c.cantidad 
                        FROM calzado c
                        INNER JOIN tipo t ON c.id_tipo = t.id_tipo
                        INNER JOIN material mt ON c.id_material = mt.id_material
                        INNER JOIN marca m ON c.id_marca = m.id_marca
                        WHERE c.modelo = '$modelo_seleccionado'";
    $resultadoDatosCalzado = $conexion->query($sqlDatosCalzado);
    
    // Verificar si se encontraron datos para el modelo seleccionado
    if($resultadoDatosCalzado->num_rows > 0) {
        // Obtener los datos del calzado seleccionado
        $row = $resultadoDatosCalzado->fetch_assoc();
        $modelo = $row["modelo"];
        $tipo = $row["tipo_calzado"];
        $material = $row["material"];
        $marca = $row["marca"];
        $precio = $row["precio"];
        $cantidad = $row["cantidad"];
    } else {
        // Limpiar los datos si no se encontraron resultados
        $modelo = "";
        $tipo = "";
        $material = "";
        $marca = "";
        $precio = "";
        $cantidad = "";
        echo "<script>alert('No se encontraron datos para el modelo seleccionado');</script>";
    }
}

// Manejar el envío del formulario de actualización
if(isset($_POST["actualizar"])){
    $modelo_actualizado = mysqli_real_escape_string($conexion, $_POST["modelo"]);
    $tipo_actualizado = mysqli_real_escape_string($conexion, $_POST["tipo"]);
    $material_actualizado = mysqli_real_escape_string($conexion, $_POST["material"]);
    $marca_actualizado = mysqli_real_escape_string($conexion, $_POST["marca"]);
    $precio_actualizado = mysqli_real_escape_string($conexion, $_POST["precio"]);
    $cantidad_actualizada = mysqli_real_escape_string($conexion, $_POST["cantidad"]);
    
    // Actualizar los datos en la base de datos
    $sqlActualizar = "UPDATE calzado 
                    SET id_tipo = '$tipo_actualizado', id_material='$material_actualizado',
                    id_marca='$marca_actualizado', precio = '$precio_actualizado', 
                    cantidad = '$cantidad_actualizada'
                    WHERE modelo = '$modelo_actualizado'";
    $resultadoActualizar = $conexion->query($sqlActualizar);
    
    if($resultadoActualizar) {
        echo "<script>alert('Datos actualizados correctamente');</script>";
        // Actualizar los valores mostrados en el formulario
        // $precio = $precio_actualizado;
        // $cantidad = $cantidad_actualizada;
    } else {
        echo "<script>alert('Error al actualizar los datos');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Calzado</title>
</head>
<body>

<h2>Actualizar Datos de Calzado</h2>

<!-- Formulario para seleccionar el modelo de calzado -->
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
    <select name="modelo_seleccionado" id="modelo_seleccionado">
        <option value="">Selecciona un modelo</option>
        <?php
        // Mostrar opciones de modelos para el autocompletado
        while($row = $resultadoModelos->fetch_assoc()) {
            echo "<option value='" . $row['modelo'] . "'>" . $row['modelo'] . "</option>";
        }
        ?>
    </select>
    <button type="submit">Buscar</button>
</form>

<!-- Formulario para mostrar y actualizar los datos del calzado seleccionado -->
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
    <label for="modelo">Modelo:</label>
    <input type="text" name="modelo" id="modelo" value="<?php echo $modelo; ?>" readonly><br><br>
    
    <!-- TIPO -->
    <select name="tipo" value="<?php echo $tipo; ?>">
            <option value="">Selecciona un tipo</option>
            <?php
            // Mostrar opciones de tipos de calzado
            while ($row = $resultadoTipos->fetch_assoc()) {
                echo "<option value='" . $row['id_tipo'] . "'>" . $row['tipo_calzado'] . "</option>";
            }
            ?>
        </select>
    <label>Tipo:</label>
    <input type="text" value="<?php echo $tipo; ?>" readonly>
    
    <!-- MATERIAL -->
    <select name="material" value="<?php echo $tipo; ?>">
            <option value="">Selecciona un tipo</option>
            <?php
            // Mostrar opciones de material de calzado
            while ($row = $resultadoMateriales->fetch_assoc()) {
                echo "<option value='" . $row['id_material'] . "'>" . $row['material'] . "</option>";
            }
            ?>
        </select>
    <label>Material:</label>
    <input type="text" value="<?php echo $material; ?>" readonly><br><br>
    
    <!-- MARCA -->
    <select name="marca" value="<?php echo $tipo; ?>">
            <option value="">Selecciona un tipo</option>
            <?php
            // Mostrar opciones de material de calzado
            while ($row = $resultadoMarcas->fetch_assoc()) {
                echo "<option value='" . $row['id_marca'] . "'>" . $row['marca'] . "</option>";
            }
            ?>
        </select>
    <label for="marca">Marca:</label>
    <input value="<?php echo $marca; ?>" readonly><br><br>
    
    <!-- PRECIO -->
    <label for="precio">Precio:</label>
    <input type="text" name="precio" id="precio" value="<?php echo $precio; ?>"><br><br>
    
    <!-- CANTIDAD -->
    <label for="cantidad">Cantidad:</label>
    <input type="text" name="cantidad" id="cantidad" value="<?php echo $cantidad; ?>"><br><br>

    <button type="submit" name="actualizar">Actualizar</button>
</form>

</body>
</html>
