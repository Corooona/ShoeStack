<?php
include ("conexion.php");

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


if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_calzado = $_GET['id'];

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
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>ShoeStock - Agregar Calzado</title>
    <link rel="stylesheet" href="styles/editar-calzado.css">
</head>

<body>
    <?php include ("header/header.php"); ?>
    <div class="main-container">
        <h2>LOL</h2>

        <div class="container">
            <div class="titulos">

                <h2>MODELO</h2>

                <h2>TIPO</h2>

                <h2>MATERIAL</h2>

                <h2>MARCA</h2>

                <h2>PRECIO</h2>

                <h2>CANTIDAD</h2>

            </div>

            <div class="insertar">
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
            </div>
        </div>
    </div>
</body>

</html>