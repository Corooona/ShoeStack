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

$id_calzado = isset($_GET['id']) ? $_GET['id'] : null; // Obtener el ID del calzado si está presente en la URL

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
        $sqlInsertarModelo = "INSERT INTO calzado(modelo, id_tipo, id_material, id_marca, precio, cantidad) 
                       VALUES('$modelo','$tipo','$material', '$marca', '$precio', '$cantidad')";
        $conecCalzado = $conexion->query($sqlInsertarModelo);

        if ($conecCalzado) {
            echo "<script>alert('Registro exitoso');</script>";
            echo "<script>window.location.href='panel-admin.php';</script>";
        } else {
            echo "<script>alert('Error al registrarse');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ShoeStock - Agregar Calzado</title>
    <link rel="stylesheet" href="styles/agregar-calzado.css">
</head>
<body>
    <?php include("header/header.php"); ?>
    <div class="main-container">
    <div class="superior">
            <label class="titulo-principal">REGISTRAR CALZADO</label>
        </div>
        <div class="container">
            <div class="titulos">
                <h2>MODELO</h2>
                <h2>TIPO</h2>
                <h2>MATERIAL</h2>
                <h2>MARCA</h2>
                <h2>PRECIO</h2>
                <h2>CANTIDAD</h2>
            </div>
            <div class="agregar">
                <form action="<?php echo $_SERVER["PHP_SELF"] . (isset($id_calzado) ? '?id=' . $id_calzado : ''); ?>" method="POST">
                    <div class="form-group">
                        <input type="text" name="modelo" placeholder="Modelo" required>
                    </div>
                    <div class="form-group">
                        <select name="tipo" required>
                            <option value="">Selecciona un tipo</option>
                            <?php while ($row = $resultadoTipos->fetch_assoc()) { ?>
                                <option value="<?php echo $row['id_tipo']; ?>"><?php echo $row['tipo_calzado']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="material" required>
                            <option value="">Selecciona un material</option>
                            <?php while ($row = $resultadoMateriales->fetch_assoc()) { ?>
                                <option value="<?php echo $row['id_material']; ?>"><?php echo $row['material']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="marca" required>
                            <option value="">Selecciona una marca</option>
                            <?php while ($row = $resultadoMarcas->fetch_assoc()) { ?>
                                <option value="<?php echo $row['id_marca']; ?>"><?php echo $row['marca']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" name="precio" placeholder="Precio" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="cantidad" placeholder="Stock" required>
                    </div>
                    <button type="submit" name="registrar" class="registrar">Registrar</button>
                    <button type="reset" class="reset">Reset</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
