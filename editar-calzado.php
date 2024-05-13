<?php
include ("conexion.php");

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_calzado = $_GET['id'];

    $sqlTipoCalzado = "SELECT tipo_calzado FROM tipo WHERE id_tipo = (SELECT id_tipo FROM calzado WHERE id_calzado = $id_calzado)";
    $sqlMaterial = "SELECT material FROM material WHERE id_material = (SELECT id_material FROM calzado WHERE id_calzado = $id_calzado)";
    $sqlMarca = "SELECT marca FROM marca WHERE id_marca = (SELECT id_marca FROM calzado WHERE id_calzado = $id_calzado)";

    $consulta = "SELECT * FROM calzado WHERE id_calzado = $id_calzado";
    $resultado = mysqli_query($conexion, $consulta);

    if (mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        $modelo = $row['modelo'];
        // Obtener tipo, material y marca del calzado
        $tipo_id = $row['id_tipo'];
        $material_id = $row['id_material'];
        $marca_id = $row['id_marca'];

        // Obtener el nombre del tipo, material y marca
        $tipo = mysqli_fetch_assoc(mysqli_query($conexion, $sqlTipoCalzado))['tipo_calzado'];
        $material = mysqli_fetch_assoc(mysqli_query($conexion, $sqlMaterial))['material'];
        $marca = mysqli_fetch_assoc(mysqli_query($conexion, $sqlMarca))['marca'];
        $precio = $row['precio'];
        $cantidad = $row['cantidad'];
    } else {
        echo "No se encontró el calzado con el ID proporcionado.";
    }
} else {
    echo "ID de calzado no proporcionado.";
}

$sqlTipos = "SELECT * FROM tipo";
$resultadoTipos = $conexion->query($sqlTipos);
$sqlMateriales = "SELECT * FROM material";
$resultadoMateriales = $conexion->query($sqlMateriales);
$sqlMarcas = "SELECT * FROM marca";
$resultadoMarcas = $conexion->query($sqlMarcas);

if (isset($_POST["actualizar"])) {
    $modelo_actualizado = mysqli_real_escape_string($conexion, $_POST["modelo"]);
    $tipo_actualizado = mysqli_real_escape_string($conexion, $_POST["tipo"]);
    $material_actualizado = mysqli_real_escape_string($conexion, $_POST["material"]);
    $marca_actualizado = mysqli_real_escape_string($conexion, $_POST["marca"]);
    $precio_actualizado = mysqli_real_escape_string($conexion, $_POST["precio"]);
    $cantidad_actualizada = mysqli_real_escape_string($conexion, $_POST["cantidad"]);


    // Obtener el ID del usuario que realizó la acción
    session_start();
    $id_usuario_accion = $_SESSION['id_usuario'];
    // Consulta para obtener el nombre de usuario a partir del id_usuario
    $sql_obtener_nombre_usuario = "SELECT nombre_user FROM usuario WHERE id_usuario = $id_usuario_accion";
    $resultado_nombre_usuario = mysqli_query($conexion, $sql_obtener_nombre_usuario);

    if ($resultado_nombre_usuario && mysqli_num_rows($resultado_nombre_usuario) > 0) {
        $row_nombre_usuario = mysqli_fetch_assoc($resultado_nombre_usuario);
        $nombre_usuario_accion = $row_nombre_usuario['nombre_user'];
    } else {
        $nombre_usuario_accion = '';
    }
    session_abort();

    $sqlActualizar = "UPDATE calzado 
                    SET id_tipo = '$tipo_actualizado', id_material='$material_actualizado',
                    id_marca='$marca_actualizado', precio = '$precio_actualizado', 
                    cantidad = '$cantidad_actualizada'
                    WHERE modelo = '$modelo_actualizado'";
    $resultadoActualizar = $conexion->query($sqlActualizar);

    if ($resultadoActualizar) {
        // Insertar el registro en la tabla registro_acceso
        $fecha_acceso = date("Y-m-d H:i:s");
        $sqlInsertarRegistroAcceso = "INSERT INTO registro_acceso (id_usuario, nombre_usuario, fecha) 
                                      VALUES ($id_usuario_accion, '$nombre_usuario_accion', '$fecha_acceso')";
        $resultadoInsertarRegistroAcceso = $conexion->query($sqlInsertarRegistroAcceso);

        echo "<script>alert('Datos actualizados correctamente');</script>";
        // Redirigir al panel de administrador después de la actualización
        echo "<script>window.location.href='panel-admin.php';</script>";
        exit(); // Evitar ciclo de redirección
    } else {
        echo "<script>alert('Error al actualizar los datos');</script>";
        // Redirigir al panel de administrador después de mostrar el error
        echo "<script>window.location.href='panel-admin.php';</script>";
        exit(); //EVITA CICLO
    }

}
?>

<!DOCTYPE html>
<html>

<head>
    <title>ShoeStock - Editar Calzado</title>
    <link rel="stylesheet" href="styles/editar-calzado.css">
</head>

<body>
    <?php include ("header/header.php"); ?>
    <div class="main-container">
        <h2>EDITAR <?php echo $modelo; ?></h2>

        <div class="container">
            <div class="titulos">
                
                <h1>MODELO</h1>
                
                <h1>TIPO</h1>

                <h1>MATERIAL</h1>

                <h1>MARCA</h1>

                <h1>PRECIO</h1>

                <h1>CANTIDAD</h1>
                
            </div>
            <div class="datos">
                <form>
                    <div class="form-group">
                        <label for="modelo">Modelo</label>
                        <input type="text" id="modelo" value="<?php echo $modelo; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo</label>
                        <input type="text" id="tipo" value="<?php echo $tipo; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="material">Material</label>
                        <input type="text" id="material" value="<?php echo $material; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="marca">Marca</label>
                        <input type="text" id="marca" value="<?php echo $marca; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="precio">Precio</label>
                        <input type="text" id="precio" value="<?php echo $precio; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="cantidad">Cantidad</label>
                        <input type="text" id="cantidad" value="<?php echo $cantidad; ?>" disabled>
                    </div>
                </form>
            </div>

            <div class="actualizar">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <label for="modelo">Modelo:</label>
                    <input type="text" name="modelo" id="modelo" value="<?php echo $modelo; ?>" readonly><br><br>


                    <label for="tipo">Tipo:</label>
                    <select name="tipo">
                        <?php while ($row = $resultadoTipos->fetch_assoc()) : ?>
                            <option value="<?php echo $row['id_tipo']; ?>" <?php if ($row['id_tipo'] == $tipo_id) echo 'selected'; ?>><?php echo $row['tipo_calzado']; ?></option>
                        <?php endwhile; ?>
                    </select><br><br>

                    <label for="material">Material:</label>
                    <select name="material">
                        <?php while ($row = $resultadoMateriales->fetch_assoc()) : ?>
                            <option value="<?php echo $row['id_material']; ?>" <?php if ($row['id_material'] == $material_id) echo 'selected'; ?>><?php echo $row['material']; ?></option>
                        <?php endwhile; ?>
                    </select><br><br>

                    <label for="marca">Marca:</label>
                    <select name="marca">
                        <?php while ($row = $resultadoMarcas->fetch_assoc()) : ?>
                            <option value="<?php echo $row['id_marca']; ?>" <?php if ($row['id_marca'] == $marca_id) echo 'selected'; ?>><?php echo $row['marca']; ?></option>
                        <?php endwhile; ?>
                    </select><br><br>

                    <label for="precio">Precio:</label>
                    <input type="text" name="precio" id="precio" value="<?php echo $precio; ?>"><br><br>

                    <label for="cantidad">Cantidad:</label>
                    <input type="text" name="cantidad" id="cantidad" value="<?php echo $cantidad; ?>"><br><br>

                    <button type="submit" name="actualizar">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>