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
}

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
    

    $sqlActualizar = "UPDATE calzado 
                    SET cantidad = '$cantidad_actualizada'
                    WHERE modelo = '$modelo_actualizado'";
    $resultadoActualizar = $conexion->query($sqlActualizar);

    if ($resultadoActualizar) {
        // Insertar el registro en la tabla registro_acceso
        $fecha_acceso = date("Y-m-d H:i:s");
        $sqlInsertarRegistroAcceso = "INSERT INTO registro_acceso (id_usuario, nombre_usuario, fecha) 
                                      VALUES ($id_usuario_accion, '$nombre_usuario_accion', '$fecha_acceso')";
        $resultadoInsertarRegistroAcceso = $conexion->query($sqlInsertarRegistroAcceso);

        echo "<script>alert('Datos actualizados correctamente');</script>";
        
        echo "<script>window.location.href='panel-empleado.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error al actualizar los datos');</script>";
        // Redirigir al panel de administrador después de mostrar el error
        echo "<script>window.location.href='panel-admin.php';</script>";
        exit(); //EVITA CICLO
    }

}

// Agregar colores al calzado seleccionado
// Agregar colores al calzado seleccionado
if (isset($_POST["agregar_colores"])) {
    $modelo_agregar_colores = mysqli_real_escape_string($conexion, $_POST["modelo_agregar_colores"]);
    $colores_seleccionados = isset($_POST["colores_seleccionados"]) ? $_POST["colores_seleccionados"] : array();
    $id_calzado = mysqli_fetch_assoc($conexion->query("SELECT id_calzado FROM calzado WHERE modelo = '$modelo_agregar_colores'"))['id_calzado'];

    // Crear una consulta SQL para insertar varios colores a la vez
    $sqlInsertarColores = "INSERT INTO calzado_color (id_calzado, id_color) VALUES ";
    $valores = array();

    foreach ($colores_seleccionados as $color) {
        // Verificar si el color ya está asociado al modelo
        $queryColoresAsociados = "SELECT id_cal_color FROM calzado_color WHERE id_calzado = $id_calzado AND id_color = $color";
        $resultadoColoresAsociados = $conexion->query($queryColoresAsociados);

        if ($resultadoColoresAsociados->num_rows == 0) {
            $valores[] = "($id_calzado, '$color')";
        }
    }

    $sqlInsertarColores .= implode(",", $valores);

    // Ejecutar la consulta solo si se seleccionaron colores nuevos
    if (!empty($valores)) {
        $conecCalzado = $conexion->query($sqlInsertarColores);

        if ($conecCalzado) {
            $fecha_acceso = date("Y-m-d H:i:s");
            $sqlInsertarRegistroAcceso = "INSERT INTO registro_acceso (id_usuario, nombre_usuario, fecha) 
                                          VALUES ($id_usuario_accion, '$nombre_usuario_accion', '$fecha_acceso')";
            $resultadoInsertarRegistroAcceso = $conexion->query($sqlInsertarRegistroAcceso);
            echo "<script>alert('Colores agregados correctamente');</script>";
            echo "<script>window.location.href='panel-admin.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error al agregar los colores');</script>";
            echo "<script>window.location.href='panel-admin.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Los colores seleccionados ya están asociados al modelo');</script>";
        echo "<script>window.location.href='panel-admin.php';</script>";
        exit();
    }
}

// Agregar tallas al calzado seleccionado
if (isset($_POST["agregar_tallas"])) {
    $modelo_agregar_tallas = mysqli_real_escape_string($conexion, $_POST["modelo_agregar_tallas"]);
    $tallas_seleccionadas = isset($_POST["tallas_seleccionadas"]) ? $_POST["tallas_seleccionadas"] : array();
    $id_calzado = mysqli_fetch_assoc($conexion->query("SELECT id_calzado FROM calzado WHERE modelo = '$modelo_agregar_tallas'"))['id_calzado'];

    // Crear una consulta SQL para insertar varias tallas a la vez
    $sqlInsertarTallas = "INSERT INTO calzado_talla (id_calzado, id_talla) VALUES ";
    $valores = array();

    foreach ($tallas_seleccionadas as $talla) {
        // Verificar si la talla ya está asociada al modelo
        $queryTallasAsociadas = "SELECT id_cal_talla FROM calzado_talla WHERE id_calzado = $id_calzado AND id_talla = $talla";
        $resultadoTallasAsociadas = $conexion->query($queryTallasAsociadas);

        if ($resultadoTallasAsociadas->num_rows == 0) {
            $valores[] = "($id_calzado, '$talla')";
        }
    }

    $sqlInsertarTallas .= implode(",", $valores);

    // Ejecutar la consulta solo si se seleccionaron tallas nuevas
    if (!empty($valores)) {
        $conecCalzado = $conexion->query($sqlInsertarTallas);

        if ($conecCalzado) {
            $fecha_acceso = date("Y-m-d H:i:s");
            $sqlInsertarRegistroAcceso = "INSERT INTO registro_acceso (id_usuario, nombre_usuario, fecha) 
                                          VALUES ($id_usuario_accion, '$nombre_usuario_accion', '$fecha_acceso')";
            $resultadoInsertarRegistroAcceso = $conexion->query($sqlInsertarRegistroAcceso);
            echo "<script>alert('Tallas agregadas correctamente');</script>";
            echo "<script>window.location.href='panel-admin.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error al agregar las tallas');</script>";
            echo "<script>window.location.href='panel-admin.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Las tallas seleccionadas ya están asociadas al modelo');</script>";
        echo "<script>window.location.href='panel-admin.php';</script>";
        exit();
    }
}


// Eliminar colores del calzado seleccionado
if (isset($_POST["eliminar_colores"])) {
    $modelo_eliminar_colores = mysqli_real_escape_string($conexion, $_POST["modelo_eliminar_colores"]);
    $colores_seleccionados = isset($_POST["colores_seleccionados"]) ? $_POST["colores_seleccionados"] : array();
    $id_calzado = mysqli_fetch_assoc($conexion->query("SELECT id_calzado FROM calzado WHERE modelo = '$modelo_eliminar_colores'"))['id_calzado'];

    // Verificar si los colores seleccionados están asociados al modelo
    $coloresAsociadosQuery = "SELECT id_color
                              FROM calzado_color
                              WHERE id_calzado = $id_calzado
                              AND id_color IN (" . implode(",", $colores_seleccionados) . ")";
    $coloresAsociadosResult = $conexion->query($coloresAsociadosQuery);
    $coloresAsociados = array();
    while ($row = $coloresAsociadosResult->fetch_assoc()) {
        $coloresAsociados[] = $row['id_color'];
    }

    // Crear una consulta SQL para eliminar varios colores a la vez
    $sqlEliminarColores = "DELETE FROM calzado_color
                           WHERE id_calzado = $id_calzado
                           AND id_color IN (" . implode(",", $coloresAsociados) . ")";

    // Ejecutar la consulta solo si se seleccionaron colores asociados
    if (!empty($coloresAsociados)) {
        $conecEliminarColor = $conexion->query($sqlEliminarColores);

        if ($conecEliminarColor) {
            $fecha_acceso = date("Y-m-d H:i:s");
            $sqlInsertarRegistroAcceso = "INSERT INTO registro_acceso (id_usuario, nombre_usuario, fecha) 
                                          VALUES ($id_usuario_accion, '$nombre_usuario_accion', '$fecha_acceso')";
            $resultadoInsertarRegistroAcceso = $conexion->query($sqlInsertarRegistroAcceso);
            echo "<script>alert('Colores eliminados correctamente');</script>";
            echo "<script>window.location.href='panel-admin.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error al eliminar los colores');</script>";
            echo "<script>window.location.href='panel-admin.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Los colores seleccionados no están asociados al modelo');</script>";
        echo "<script>window.location.href='panel-admin.php';</script>";
        exit();
    }
}

// Eliminar tallas del calzado seleccionado
if (isset($_POST["eliminar_tallas"])) {
    $modelo_eliminar_tallas = mysqli_real_escape_string($conexion, $_POST["modelo_eliminar_tallas"]);
    $tallas_seleccionadas = isset($_POST["tallas_seleccionadas"]) ? $_POST["tallas_seleccionadas"] : array();
    $id_calzado = mysqli_fetch_assoc($conexion->query("SELECT id_calzado FROM calzado WHERE modelo = '$modelo_eliminar_tallas'"))['id_calzado'];

    // Verificar si las tallas seleccionadas están asociadas al modelo
    $tallasAsociadasQuery = "SELECT id_talla 
                             FROM calzado_talla
                             WHERE id_calzado = $id_calzado
                             AND id_talla IN (" . implode(",", $tallas_seleccionadas) . ")";
    $tallasAsociadasResult = $conexion->query($tallasAsociadasQuery);
    $tallasAsociadas = array();
    while ($row = $tallasAsociadasResult->fetch_assoc()) {
        $tallasAsociadas[] = $row['id_talla'];
    }

    // Crear una consulta SQL para eliminar varias tallas a la vez
    $sqlEliminarTallas = "DELETE FROM calzado_talla
                          WHERE id_calzado = $id_calzado
                          AND id_talla IN (" . implode(",", $tallasAsociadas) . ")";

    // Ejecutar la consulta solo si se seleccionaron tallas asociadas
    if (!empty($tallasAsociadas)) {
        $conecEliminarTalla = $conexion->query($sqlEliminarTallas);

        if ($conecEliminarTalla) {
            $fecha_acceso = date("Y-m-d H:i:s");
            $sqlInsertarRegistroAcceso = "INSERT INTO registro_acceso (id_usuario, nombre_usuario, fecha) 
                                          VALUES ($id_usuario_accion, '$nombre_usuario_accion', '$fecha_acceso')";
            $resultadoInsertarRegistroAcceso = $conexion->query($sqlInsertarRegistroAcceso);
            echo "<script>alert('Tallas eliminadas correctamente');</script>";
            echo "<script>window.location.href='panel-admin.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error al eliminar las tallas');</script>";
            echo "<script>window.location.href='panel-admin.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Las tallas seleccionadas no están asociadas al modelo');</script>";
        echo "<script>window.location.href='panel-admin.php';</script>";
        exit();
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
        <div class="superior">
            <label class="titulo-principal">EDITAR <?php echo $modelo; ?></label>
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
            <div class="datos">
                <form>
                    <div class="form-group">
                        <input type="text" id="modelo" value="<?php echo $modelo; ?>" disabled>
                    </div>

                    <div class="form-group">
                        <input type="text" id="tipo" value="<?php echo $tipo; ?>" disabled>
                    </div>

                    <div class="form-group">
                        <input type="text" id="material" value="<?php echo $material; ?>" disabled>
                    </div>

                    <div class="form-group">
                        <input type="text" id="marca" value="<?php echo $marca; ?>" disabled>
                    </div>

                    <div class="form-group">
                        <input type="text" id="precio" value="<?php echo $precio; ?>" disabled>
                    </div>

                    <div class="form-group">
                        <input type="text" id="cantidad" value="<?php echo $cantidad; ?>" disabled>
                    </div>
                </form>
            </div>

            <div class="actualizar">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">

                    <div class="form-group">
                        <input class="noeditar" type="text" name="modelo" id="modelo" value="<?php echo $modelo; ?>"
                            readonly>
                    </div>

                    <div class="form-group">
                    <input type="text" name="tipo" id="tipo" value="<?php echo $tipo; ?>" readonly>
                    </div>

                    <div class="form-group">
                    <input type="text" name="material" id="material" value="<?php echo $material; ?>" readonly>
                        
                    </div>

                    <div class="form-group">
                    <input type="text" name="marca" id="marca" value="<?php echo $marca; ?>" readonly>
                    
                    </div>

                    <div class="form-group">
                        <input class="noeditar" type="text" name="precio" id="precio" value="<?php echo $precio; ?>"readonly>
                    </div>

                    <div class="form-group">
                        <input type="text" name="cantidad" id="cantidad" value="<?php echo $cantidad; ?>">
                    </div>

                    <div class="botones">
                        <button type="submit" name="actualizar" class="actualizar">Actualizar</button>
                        <button type="reset" class="reset">Reset</button>
                    </div>
                </form>
            </div>

        </div>

        <div class="tyc">
            <div class="agregar">
                <div class="colores">
                    <h2>Agregar Colores</h2>
                    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                        <input type="hidden" name="modelo_agregar_colores" value="<?php echo $modelo; ?>">
                        <?php
                        // Mostrar checkboxes para los colores
                        $sqlColores = "SELECT * FROM color";
                        $resultadoColores = $conexion->query($sqlColores);
                        while ($row = $resultadoColores->fetch_assoc()) {
                            echo "<input type='checkbox' name='colores_seleccionados[]' value='" . $row['id_color'] . "'>" . $row['nombre_color'] . "<br>";
                        }
                        ?>
                        <button class="btn-agregar" type="submit" name="agregar_colores">Agregar Colores</button>
                    </form>
                </div>




                <div class="tallas">
                    <h2>Agregar Tallas</h2>
                    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                        <input type="hidden" name="modelo_agregar_tallas" value="<?php echo $modelo; ?>">
                        <?php
                        // Mostrar checkboxes para las tallas
                        $sqlTallas = "SELECT * FROM talla";
                        $resultadoTallas = $conexion->query($sqlTallas);
                        while ($row = $resultadoTallas->fetch_assoc()) {
                            echo "<input type='checkbox' name='tallas_seleccionadas[]' value='" . $row['id_talla'] . "'>" . $row['talla'] . "<br>";
                        }
                        ?>
                        <button class="btn-agregar" type="submit" name="agregar_tallas">Agregar Tallas</button>
                    </form>
                </div>

            </div>

            <div class="eliminar">

                <div class="colores">
                <h2>Eliminar Colores</h2>
                    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                        <input type="hidden" name="modelo_eliminar_colores" value="<?php echo $modelo; ?>">

                        <?php
                        // Mostrar checkboxes para los colores asociados al modelo
                        $sqlColoresAsociados = "SELECT c.id_color, c.nombre_color
                            FROM calzado_color cc
                            INNER JOIN color c ON cc.id_color = c.id_color
                            WHERE cc.id_calzado = (SELECT id_calzado FROM calzado WHERE modelo = '$modelo')";
                        $resultadoColoresAsociados = $conexion->query($sqlColoresAsociados);
                        while ($row = $resultadoColoresAsociados->fetch_assoc()) {
                            echo "<input type='checkbox' name='colores_seleccionados[]' value='" . $row['id_color'] . "'>" . $row['nombre_color'] . "<br>";
                        }
                        ?>
                        <button class="btn-eliminar" type="submit" name="eliminar_colores">Eliminar Colores</button>
                    </form>
                </div>

                <div class="tallas">
                    <h2>Eliminar Tallas</h2>
                    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                        <input type="hidden" name="modelo_eliminar_tallas" value="<?php echo $modelo; ?>">

                        <?php
                        // Mostrar checkboxes para las tallas asociadas al modelo
                        $sqlTallasAsociadas = "SELECT t.id_talla, t.talla
                           FROM calzado_talla ct
                           INNER JOIN talla t ON ct.id_talla = t.id_talla
                           WHERE ct.id_calzado = (SELECT id_calzado FROM calzado WHERE modelo = '$modelo')";
                        $resultadoTallasAsociadas = $conexion->query($sqlTallasAsociadas);
                        while ($row = $resultadoTallasAsociadas->fetch_assoc()) {
                            echo "<input type='checkbox' name='tallas_seleccionadas[]' value='" . $row['id_talla'] . "'>" . $row['talla'] . "<br>";
                        }
                        ?>
                        <button class="btn-eliminar" type="submit" name="eliminar_tallas">Eliminar Tallas</button>
                    </form>
                </div>
            </div>
        </div>


    </div>

</body>

</html>