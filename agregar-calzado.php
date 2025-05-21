<?php
include("conexion.php");

// SECCIÓN DE REGISTRO CALZADO
// Obtener tipos, materiales y marcas de calzado de la base de datos usando PDO
$sqlTipos = "SELECT * FROM tipo";
$stmtTipos = $conexion->prepare($sqlTipos);
$stmtTipos->execute();
$resultadoTipos = $stmtTipos->fetchAll();

$sqlMateriales = "SELECT * FROM material";
$stmtMateriales = $conexion->prepare($sqlMateriales);
$stmtMateriales->execute();
$resultadoMateriales = $stmtMateriales->fetchAll();

$sqlMarcas = "SELECT * FROM marca";
$stmtMarcas = $conexion->prepare($sqlMarcas);
$stmtMarcas->execute();
$resultadoMarcas = $stmtMarcas->fetchAll();

// Obtener el ID del calzado si está presente en la URL
$id_calzado = isset($_GET['id']) ? $_GET['id'] : null;

// Obtener información del usuario
session_start();
$id_usuario_accion = $_SESSION['id_usuario'];

// Obtener el nombre del usuario de la base de datos
$sql_obtener_nombre_usuario = "SELECT nombre_user FROM usuario WHERE id_usuario = :id_usuario";
$stmt_usuario = $conexion->prepare($sql_obtener_nombre_usuario);
$stmt_usuario->bindValue(":id_usuario", $id_usuario_accion, PDO::PARAM_INT);
$stmt_usuario->execute();
$row_nombre_usuario = $stmt_usuario->fetch();

$nombre_usuario_accion = $row_nombre_usuario ? $row_nombre_usuario['nombre_user'] : '';

if (isset($_POST["registrar"])) {
    // Recoger los datos del formulario
    $modelo = htmlspecialchars($_POST["modelo"]);
    $tipo = $_POST["tipo"];
    $material = $_POST["material"];
    $marca = $_POST["marca"];
    $precio = $_POST["precio"];
    $cantidad = $_POST["cantidad"];

    // Verificar si el modelo ya existe en la base de datos
    $sqlVerificarModelo = "SELECT id_calzado FROM calzado WHERE modelo = :modelo";
    $stmtVerificar = $conexion->prepare($sqlVerificarModelo);
    $stmtVerificar->bindValue(":modelo", $modelo);
    $stmtVerificar->execute();
    
    if ($stmtVerificar->rowCount() > 0) {
        echo "<script>alert('El modelo ya existe');</script>";
    } else {
        // Insertar el nuevo modelo de calzado
        $sqlInsertarModelo = "INSERT INTO calzado (modelo, id_tipo, id_material, id_marca, precio, cantidad) 
                              VALUES (:modelo, :tipo, :material, :marca, :precio, :cantidad)";
        $stmtInsertar = $conexion->prepare($sqlInsertarModelo);
        $stmtInsertar->bindValue(":modelo", $modelo);
        $stmtInsertar->bindValue(":tipo", $tipo);
        $stmtInsertar->bindValue(":material", $material);
        $stmtInsertar->bindValue(":marca", $marca);
        $stmtInsertar->bindValue(":precio", $precio);
        $stmtInsertar->bindValue(":cantidad", $cantidad);
        $stmtInsertar->execute();

        if ($stmtInsertar) {
            // Insertar el registro en la tabla registro_acceso
            $fecha_acceso = date("Y-m-d H:i:s");
            $sqlInsertarRegistroAcceso = "INSERT INTO registro_acceso (id_usuario, nombre_usuario, fecha) 
                                          VALUES (:id_usuario, :nombre_usuario, :fecha_acceso)";
            $stmtRegistro = $conexion->prepare($sqlInsertarRegistroAcceso);
            $stmtRegistro->bindValue(":id_usuario", $id_usuario_accion, PDO::PARAM_INT);
            $stmtRegistro->bindValue(":nombre_usuario", $nombre_usuario_accion);
            $stmtRegistro->bindValue(":fecha_acceso", $fecha_acceso);
            $stmtRegistro->execute();

            echo "<script>alert('Registro exitoso');</script>";

            if($SESSION['rol']==1){
                echo "<script>window.location.href='panel-admin.php';</script>";    
            }
            else{
                echo "<script>window.location.href='panel-empleado.php';</script>";
            }

            
        } else {
            echo "<script>alert('Error al registrarse');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                            <?php foreach ($resultadoTipos as $row) { ?>
                                <option value="<?php echo $row['id_tipo']; ?>"><?php echo htmlspecialchars($row['tipo_calzado']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="material" required>
                            <option value="">Selecciona un material</option>
                            <?php foreach ($resultadoMateriales as $row) { ?>
                                <option value="<?php echo $row['id_material']; ?>"><?php echo htmlspecialchars($row['material']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="marca" required>
                            <option value="">Selecciona una marca</option>
                            <?php foreach ($resultadoMarcas as $row) { ?>
                                <option value="<?php echo $row['id_marca']; ?>"><?php echo htmlspecialchars($row['marca']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="number" name="precio" placeholder="Precio" required>
                    </div>
                    <div class="form-group">
                        <input type="number" name="cantidad" placeholder="Stock" required>
                    </div>
                    <button type="submit" name="registrar" class="registrar">Registrar</button>
                    <button type="reset" class="reset">Reset</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
