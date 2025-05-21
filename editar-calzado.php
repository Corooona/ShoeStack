<?php
include("conexion.php");

// Función para obtener el nombre de cualquier entidad (tipo, material, marca, color, talla)
function obtenerNombreEntidad($conexion, $tabla, $id) {
    $sql = "SELECT * FROM $tabla WHERE id_$tabla = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Manejar los diferentes nombres de columnas según la tabla
    switch($tabla) {
        case 'tipo': return $row['tipo_calzado'] ?? '';
        case 'material': return $row['material'] ?? '';
        case 'marca': return $row['marca'] ?? '';
        case 'color': return $row['nombre_color'] ?? '';
        case 'talla': return $row['talla'] ?? '';
        default: return '';
    }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_calzado = $_GET['id'];

    // Consulta para obtener la información del calzado utilizando PDO
    $consulta = "SELECT * FROM calzado WHERE id_calzado = :id_calzado";
    $stmt = $conexion->prepare($consulta);
    $stmt->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $modelo = $row['modelo'];
        $tipo_id = $row['id_tipo'];
        $material_id = $row['id_material'];
        $marca_id = $row['id_marca'];
        
        // Obtener nombres usando la nueva función
        $tipo = obtenerNombreEntidad($conexion, 'tipo', $tipo_id);
        $material = obtenerNombreEntidad($conexion, 'material', $material_id);
        $marca = obtenerNombreEntidad($conexion, 'marca', $marca_id);
        $precio = $row['precio'];
        $cantidad = $row['cantidad'];
    } else {
        echo "No se encontró el calzado con el ID proporcionado.";
    }
} else {
    echo "ID de calzado no proporcionado.";
}

session_start();
$id_usuario_accion = $_SESSION['id_usuario'] ?? null;
$nombre_usuario_accion = '';

if ($id_usuario_accion) {
    $sql_obtener_nombre_usuario = "SELECT nombre_user FROM usuario WHERE id_usuario = :id_usuario";
    $stmt_nombre_usuario = $conexion->prepare($sql_obtener_nombre_usuario);
    $stmt_nombre_usuario->bindParam(':id_usuario', $id_usuario_accion, PDO::PARAM_INT);
    $stmt_nombre_usuario->execute();

    if ($stmt_nombre_usuario->rowCount() > 0) {
        $row_nombre_usuario = $stmt_nombre_usuario->fetch(PDO::FETCH_ASSOC);
        $nombre_usuario_accion = $row_nombre_usuario['nombre_user'];
    }
}

// Preparar consultas para los select
$sqlTipos = "SELECT * FROM tipo";
$stmtTipos = $conexion->prepare($sqlTipos);
$stmtTipos->execute();

$sqlMateriales = "SELECT * FROM material";
$stmtMateriales = $conexion->prepare($sqlMateriales);
$stmtMateriales->execute();

$sqlMarcas = "SELECT * FROM marca";
$stmtMarcas = $conexion->prepare($sqlMarcas);
$stmtMarcas->execute();

// Función para registrar acceso
function registrarAcceso($conexion, $id_usuario, $nombre_usuario) {
    $fecha_acceso = date("Y-m-d H:i:s");
    $sqlInsertarRegistroAcceso = "INSERT INTO registro_acceso (id_usuario, nombre_usuario, fecha) 
                                VALUES (:id_usuario, :nombre_usuario, :fecha_acceso)";
    $stmt = $conexion->prepare($sqlInsertarRegistroAcceso);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->bindParam(':nombre_usuario', $nombre_usuario, PDO::PARAM_STR);
    $stmt->bindParam(':fecha_acceso', $fecha_acceso, PDO::PARAM_STR);
    return $stmt->execute();
}

// Actualizar información básica del calzado
if (isset($_POST["actualizar"])) {
    $id_calzado = $_GET['id'];
    $modelo_actualizado = htmlspecialchars($_POST["modelo"]);
    $tipo_actualizado = $_POST["tipo"];
    $material_actualizado = $_POST["material"];
    $marca_actualizado = $_POST["marca"];
    $precio_actualizado = $_POST["precio"];
    $cantidad_actualizada = $_POST["cantidad"];

    $sqlActualizar = "UPDATE calzado 
                    SET id_tipo = :tipo, id_material = :material, id_marca = :marca, 
                        precio = :precio, cantidad = :cantidad
                    WHERE id_calzado = :id_calzado";
    $stmtActualizar = $conexion->prepare($sqlActualizar);
    $stmtActualizar->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
    $stmtActualizar->bindParam(':tipo', $tipo_actualizado, PDO::PARAM_INT);
    $stmtActualizar->bindParam(':material', $material_actualizado, PDO::PARAM_INT);
    $stmtActualizar->bindParam(':marca', $marca_actualizado, PDO::PARAM_INT);
    $stmtActualizar->bindParam(':precio', $precio_actualizado, PDO::PARAM_STR);
    $stmtActualizar->bindParam(':cantidad', $cantidad_actualizada, PDO::PARAM_INT);
    
    if ($stmtActualizar->execute()) {
        registrarAcceso($conexion, $id_usuario_accion, $nombre_usuario_accion);
        echo "<script>alert('Datos actualizados correctamente'); window.location.href='panel-admin.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error al actualizar los datos'); window.location.href='panel-admin.php';</script>";
        exit();
    }
}

// Agregar colores al calzado
if (isset($_POST["agregar_colores"])) {
    $colores_seleccionados = $_POST["colores_seleccionados"] ?? [];
    
    if (!empty($colores_seleccionados)) {
        // Preparar una sola consulta con múltiples inserciones
        $stmt = $conexion->prepare("INSERT INTO calzado_color (id_calzado, id_color) VALUES (:id_calzado, :id_color)");
        
        // Iniciar transacción
        $conexion->beginTransaction();
        $insertados = 0;
        
        foreach ($colores_seleccionados as $color) {
            // Verificar si el color ya está asociado
            $sqlVerificar = "SELECT id_cal_color FROM calzado_color 
                           WHERE id_calzado = :id_calzado AND id_color = :id_color";
            $stmtVerificar = $conexion->prepare($sqlVerificar);
            $stmtVerificar->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
            $stmtVerificar->bindParam(':id_color', $color, PDO::PARAM_INT);
            $stmtVerificar->execute();
            
            if ($stmtVerificar->rowCount() == 0) {
                // Si no existe, insertar
                $stmt->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
                $stmt->bindParam(':id_color', $color, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $insertados++;
                }
            }
        }
        
        // Confirmar transacción
        $conexion->commit();
        
        if ($insertados > 0) {
            registrarAcceso($conexion, $id_usuario_accion, $nombre_usuario_accion);
            echo "<script>alert('Colores agregados correctamente'); window.location.href='';;</script>";
            exit();
        } else {
            echo "<script>alert('Los colores seleccionados ya están asociados al modelo'); window.location.href='';;</script>";
            exit();
        }
    } else {
        echo "<script>alert('No se seleccionaron colores para agregar'); window.location.href='';;</script>";
        exit();
    }
}

// Eliminar colores del calzado
if (isset($_POST["eliminar_colores"])) {
    $colores_seleccionados = $_POST["colores_seleccionados"] ?? [];
    
    if (!empty($colores_seleccionados)) {
        $placeholders = implode(',', array_fill(0, count($colores_seleccionados), '?'));
        $sqlEliminar = "DELETE FROM calzado_color 
                       WHERE id_calzado = ? AND id_color IN ($placeholders)";
        
        $stmtEliminar = $conexion->prepare($sqlEliminar);
        $params = array_merge([$id_calzado], $colores_seleccionados);
        
        if ($stmtEliminar->execute($params)) {
            registrarAcceso($conexion, $id_usuario_accion, $nombre_usuario_accion);
            echo "<script>alert('Colores eliminados correctamente'); window.location.href='';</script>";
            exit();
        } else {
            echo "<script>alert('Error al eliminar colores'); window.location.href='';</script>";
            exit();
        }
    } else {
        echo "<script>alert('No se seleccionaron colores para eliminar'); window.location.href='';</script>";
        exit();
    }
}

// Agregar tallas al calzado
if (isset($_POST["agregar_tallas"])) {
    $tallas_seleccionadas = $_POST["tallas_seleccionadas"] ?? [];
    
    if (!empty($tallas_seleccionadas)) {
        // Preparar una sola consulta con múltiples inserciones
        $stmt = $conexion->prepare("INSERT INTO calzado_talla (id_calzado, id_talla) VALUES (:id_calzado, :id_talla)");
        
        // Iniciar transacción
        $conexion->beginTransaction();
        $insertados = 0;
        
        foreach ($tallas_seleccionadas as $talla) {
            // Verificar si la talla ya está asociada
            $sqlVerificar = "SELECT id_cal_talla FROM calzado_talla 
                           WHERE id_calzado = :id_calzado AND id_talla = :id_talla";
            $stmtVerificar = $conexion->prepare($sqlVerificar);
            $stmtVerificar->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
            $stmtVerificar->bindParam(':id_talla', $talla, PDO::PARAM_INT);
            $stmtVerificar->execute();
            
            if ($stmtVerificar->rowCount() == 0) {
                // Si no existe, insertar
                $stmt->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
                $stmt->bindParam(':id_talla', $talla, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $insertados++;
                }
            }
        }
        
        // Confirmar transacción
        $conexion->commit();
        
        if ($insertados > 0) {
            registrarAcceso($conexion, $id_usuario_accion, $nombre_usuario_accion);
            echo "<script>alert('Tallas agregadas correctamente'); window.location.href='';</script>";
            exit();
        } else {
            echo "<script>alert('Las tallas seleccionadas ya están asociadas al modelo'); window.location.href='';</script>";
            exit();
        }
    } else {
        echo "<script>alert('No se seleccionaron tallas para agregar'); window.location.href='';</script>";
        exit();
    }
}

// Eliminar tallas del calzado
if (isset($_POST["eliminar_tallas"])) {
    $tallas_seleccionadas = $_POST["tallas_seleccionadas"] ?? [];
    
    if (!empty($tallas_seleccionadas)) {
        $placeholders = implode(',', array_fill(0, count($tallas_seleccionadas), '?'));
        $sqlEliminar = "DELETE FROM calzado_talla 
                       WHERE id_calzado = ? AND id_talla IN ($placeholders)";
        
        $stmtEliminar = $conexion->prepare($sqlEliminar);
        $params = array_merge([$id_calzado], $tallas_seleccionadas);
        
        if ($stmtEliminar->execute($params)) {
            registrarAcceso($conexion, $id_usuario_accion, $nombre_usuario_accion);
            echo "<script>alert('Tallas eliminadas correctamente'); window.location.href='';</script>";
            exit();
        } else {
            echo "<script>alert('Error al eliminar tallas'); window.location.href='';</script>";
            exit();
        }
    } else {
        echo "<script>alert('No se seleccionaron tallas para eliminar'); window.location.href='';</script>";
        exit();
    }
}

// Obtener colores y tallas disponibles
$sqlColores = "SELECT * FROM color";
$stmtColores = $conexion->prepare($sqlColores);
$stmtColores->execute();

$sqlTallas = "SELECT * FROM talla";
$stmtTallas = $conexion->prepare($sqlTallas);
$stmtTallas->execute();

// Obtener colores y tallas asociadas al calzado
$sqlColoresAsociados = "SELECT c.id_color, c.nombre_color
                       FROM calzado_color cc
                       INNER JOIN color c ON cc.id_color = c.id_color
                       WHERE cc.id_calzado = :id_calzado";
$stmtColoresAsociados = $conexion->prepare($sqlColoresAsociados);
$stmtColoresAsociados->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
$stmtColoresAsociados->execute();

$sqlTallasAsociadas = "SELECT t.id_talla, t.talla
                      FROM calzado_talla ct
                      INNER JOIN talla t ON ct.id_talla = t.id_talla
                      WHERE ct.id_calzado = :id_calzado";
$stmtTallasAsociadas = $conexion->prepare($sqlTallasAsociadas);
$stmtTallasAsociadas->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
$stmtTallasAsociadas->execute();

?>

<!DOCTYPE html>
<html>

<head>
    <title>ShoeStock - Editar Calzado</title>
    <link rel="stylesheet" href="styles/editar-calzado.css">
</head>

<body>
    <?php include("header/header.php"); ?>
    <div class="main-container">
        <div class="superior">
            <label class="titulo-principal">EDITAR <?php echo htmlspecialchars($modelo); ?></label>
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
                        <input type="text" id="modelo" value="<?php echo htmlspecialchars($modelo); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <input type="text" id="tipo" value="<?php echo htmlspecialchars($tipo); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <input type="text" id="material" value="<?php echo htmlspecialchars($material); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <input type="text" id="marca" value="<?php echo htmlspecialchars($marca); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <input type="text" id="precio" value="<?php echo htmlspecialchars($precio); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <input type="text" id="cantidad" value="<?php echo htmlspecialchars($cantidad); ?>" disabled>
                    </div>
                </form>
            </div>

            <div class="actualizar">
                <form action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $id_calzado; ?>" method="POST">
                    <div class="form-group">
                        <input class="noeditar" type="text" name="modelo" id="modelo" value="<?php echo htmlspecialchars($modelo); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <select name="tipo">
                            <?php foreach ($stmtTipos->fetchAll(PDO::FETCH_ASSOC) as $row): ?>
                                <option value="<?php echo $row['id_tipo']; ?>" <?php if ($row['id_tipo'] == $tipo_id) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($row['tipo_calzado']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <select name="material">
                            <?php foreach ($stmtMateriales->fetchAll(PDO::FETCH_ASSOC) as $row): ?>
                                <option value="<?php echo $row['id_material']; ?>" <?php if ($row['id_material'] == $material_id) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($row['material']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <select name="marca">
                            <?php foreach ($stmtMarcas->fetchAll(PDO::FETCH_ASSOC) as $row): ?>
                                <option value="<?php echo $row['id_marca']; ?>" <?php if ($row['id_marca'] == $marca_id) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($row['marca']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="text" name="precio" id="precio" value="<?php echo htmlspecialchars($precio); ?>">
                    </div>

                    <div class="form-group">
                        <input type="text" name="cantidad" id="cantidad" value="<?php echo htmlspecialchars($cantidad); ?>">
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
                <form action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $id_calzado; ?>" method="POST">
                    <div class="checkbox-container">
                        <?php 
                        $coloresDisponibles = $stmtColores->fetchAll(PDO::FETCH_ASSOC);
                        $coloresAsociados = $stmtColoresAsociados->fetchAll(PDO::FETCH_ASSOC);
                        
                        // Obtener IDs de colores ya asociados
                        $coloresAsociadosIds = array_column($coloresAsociados, 'id_color');
                        
                        foreach ($coloresDisponibles as $color): 
                            if (!in_array($color['id_color'], $coloresAsociadosIds)):
                        ?>
                            <div class="checkbox-item">
                                <input type="checkbox" name="colores_seleccionados[]" 
                                       value="<?php echo $color['id_color']; ?>" id="color_<?php echo $color['id_color']; ?>">
                                <label for="color_<?php echo $color['id_color']; ?>">
                                    <?php echo htmlspecialchars($color['nombre_color']); ?>
                                </label>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                    <button class="btn-agregar" type="submit" name="agregar_colores">Agregar Colores Seleccionados</button>
                </form>
            </div>

            <div class="tallas">
                <h2>Agregar Tallas</h2>
                <form action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $id_calzado; ?>" method="POST">
                    <div class="checkbox-container">
                        <?php 
                        $tallasDisponibles = $stmtTallas->fetchAll(PDO::FETCH_ASSOC);
                        $tallasAsociadas = $stmtTallasAsociadas->fetchAll(PDO::FETCH_ASSOC);
                        
                        // Obtener IDs de tallas ya asociadas
                        $tallasAsociadasIds = array_column($tallasAsociadas, 'id_talla');
                        
                        foreach ($tallasDisponibles as $talla): 
                            if (!in_array($talla['id_talla'], $tallasAsociadasIds)):
                        ?>
                            <div class="checkbox-item">
                                <input type="checkbox" name="tallas_seleccionadas[]" 
                                       value="<?php echo $talla['id_talla']; ?>" id="talla_<?php echo $talla['id_talla']; ?>">
                                <label for="talla_<?php echo $talla['id_talla']; ?>">
                                    <?php echo htmlspecialchars($talla['talla']); ?>
                                </label>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                    <button class="btn-agregar" type="submit" name="agregar_tallas">Agregar Tallas Seleccionadas</button>
                </form>
            </div>
        </div>

        <div class="eliminar">
            <div class="colores">
                <h2>Eliminar Colores</h2>
                <form action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $id_calzado; ?>" method="POST">
                    <?php if (count($coloresAsociados) > 0): ?>
                        <div class="checkbox-container">
                            <?php foreach ($coloresAsociados as $color): ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="colores_seleccionados[]" 
                                           value="<?php echo $color['id_color']; ?>" id="del_color_<?php echo $color['id_color']; ?>">
                                    <label for="del_color_<?php echo $color['id_color']; ?>">
                                        <?php echo htmlspecialchars($color['nombre_color']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="btn-eliminar" type="submit" name="eliminar_colores">Eliminar Colores Seleccionados</button>
                    <?php else: ?>
                        <p>No hay colores asociados a este modelo.</p>
                    <?php endif; ?>
                </form>
            </div>

            <div class="tallas">
                <h2>Eliminar Tallas</h2>
                <form action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $id_calzado; ?>" method="POST">
                    <?php if (count($tallasAsociadas) > 0): ?>
                        <div class="checkbox-container">
                            <?php foreach ($tallasAsociadas as $talla): ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="tallas_seleccionadas[]" 
                                           value="<?php echo $talla['id_talla']; ?>" id="del_talla_<?php echo $talla['id_talla']; ?>">
                                    <label for="del_talla_<?php echo $talla['id_talla']; ?>">
                                        <?php echo htmlspecialchars($talla['talla']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="btn-eliminar" type="submit" name="eliminar_tallas">Eliminar Tallas Seleccionadas</button>
                    <?php else: ?>
                        <p>No hay tallas asociadas a este modelo.</p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Seleccionar/deseleccionar todos los checkboxes
        function selectAll(containerId, check) {
            const container = document.getElementById(containerId);
            const checkboxes = container.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = check;
            });
        }
    </script>
</body>
</html>