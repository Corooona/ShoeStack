<?php
include("conexion.php");

// Función para obtener el nombre de cualquier entidad
function obtenerNombreEntidad($conexion, $tabla, $id) {
    $sql = "SELECT * FROM $tabla WHERE id_$tabla = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    switch($tabla) {
        case 'tipo': return $row['tipo_calzado'] ?? '';
        case 'material': return $row['material'] ?? '';
        case 'marca': return $row['marca'] ?? '';
        case 'color': return $row['nombre_color'] ?? '';
        case 'talla': return $row['talla'] ?? '';
        default: return '';
    }
}

// Verificar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de calzado no proporcionado.");
}

$id_calzado = $_GET['id'];

// Obtener información del calzado
$consulta = "SELECT * FROM calzado WHERE id_calzado = :id_calzado";
$stmt = $conexion->prepare($consulta);
$stmt->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    die("No se encontró el calzado con el ID proporcionado.");
}

$row = $stmt->fetch(PDO::FETCH_ASSOC);
$modelo = $row['modelo'];
$tipo_id = $row['id_tipo'];
$material_id = $row['id_material'];
$marca_id = $row['id_marca'];
$precio = $row['precio'];
$cantidad = $row['cantidad'];

// Obtener nombres
$tipo = obtenerNombreEntidad($conexion, 'tipo', $tipo_id);
$material = obtenerNombreEntidad($conexion, 'material', $material_id);
$marca = obtenerNombreEntidad($conexion, 'marca', $marca_id);

// Manejo de sesión
session_start();
$id_usuario_accion = $_SESSION['id_usuario'] ?? null;
$nombre_usuario_accion = '';

if ($id_usuario_accion) {
    $sql = "SELECT nombre_user FROM usuario WHERE id_usuario = :id_usuario";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario_accion, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $nombre_usuario_accion = $row['nombre_user'];
    }
}

// Función para registrar acciones
session_start();
$id_usuario_accion = $_SESSION['id_usuario'] ?? null;
$nombre_usuario_accion = '';

if ($id_usuario_accion) {
    $sql = "SELECT nombre_user FROM usuario WHERE id_usuario = :id_usuario";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario_accion, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $nombre_usuario_accion = $row['nombre_user'];
    }
}
function registrarAccion($conexion, $id_usuario, $nombre_usuario) {
    $sql = "INSERT INTO registro_acceso (id_usuario, nombre_usuario, fecha) 
            VALUES (:id_usuario, :nombre_usuario, NOW())";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->bindParam(':nombre_usuario', $nombre_usuario, PDO::PARAM_STR);
    return $stmt->execute();
}


// Actualizar información del calzado
if (isset($_POST["actualizar"])) {
    $cantidad_actualizada = $_POST["cantidad"] ?? $cantidad;

    // Validar datos
    if (!is_numeric($cantidad_actualizada)) {
        echo "<script>alert('La cantidad debe ser un número válido');</script>";
    } else {
        $sql = "UPDATE calzado 
                SET cantidad = :cantidad
                WHERE id_calzado = :id_calzado";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':cantidad', $cantidad_actualizada, PDO::PARAM_INT);
        $stmt->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            registrarAccion($conexion, $id_usuario_accion, $nombre_usuario_accion);
            echo "<script>alert('Cantidad actualizada correctamente'); window.location.href='panel-empleado.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error al actualizar los datos'); window.location.href='panel-empleado.php';</script>";
        }
    }
}

// Agregar colores al calzado
if (isset($_POST["agregar_colores"])) {
    $colores_seleccionados = $_POST["colores_seleccionados"] ?? [];
    
    if (empty($colores_seleccionados)) {
        echo "<script>alert('No se seleccionaron colores para agregar');</script>";
    } else {
        $errores = false;
        
        // Iniciar transacción para garantizar integridad
        $conexion->beginTransaction();
        
        try {
            foreach ($colores_seleccionados as $color) {
                // Verificar si el color ya está asociado
                $sqlVerificar = "SELECT COUNT(*) FROM calzado_color 
                             WHERE id_calzado = :id_calzado AND id_color = :id_color";
                $stmtVerificar = $conexion->prepare($sqlVerificar);
                $stmtVerificar->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
                $stmtVerificar->bindParam(':id_color', $color, PDO::PARAM_INT);
                $stmtVerificar->execute();
                
                if ($stmtVerificar->fetchColumn() == 0) {
                    // Insertar nuevo color
                    $sqlInsertar = "INSERT INTO calzado_color (id_calzado, id_color) 
                                  VALUES (:id_calzado, :id_color)";
                    $stmtInsertar = $conexion->prepare($sqlInsertar);
                    $stmtInsertar->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
                    $stmtInsertar->bindParam(':id_color', $color, PDO::PARAM_INT);
                    $stmtInsertar->execute();
                }
            }
            
            // Confirmar transacción
            $conexion->commit();
            registrarAccion($conexion, $id_usuario_accion, $nombre_usuario_accion);
            echo "<script>alert('Colores agregados correctamente'); window.location.href='" . $_SERVER["PHP_SELF"] . "?id=" . $id_calzado . "';</script>";
        } catch (PDOException $e) {
            // Revertir transacción en caso de error
            $conexion->rollBack();
            echo "<script>alert('Error al agregar colores: " . $e->getMessage() . "');</script>";
        }
    }
}

// Eliminar colores del calzado
if (isset($_POST["eliminar_colores"])) {
    $colores_seleccionados = $_POST["colores_eliminar"] ?? [];
    
    if (empty($colores_seleccionados)) {
        echo "<script>alert('No se seleccionaron colores para eliminar');</script>";
    } else {
        // Iniciar transacción
        $conexion->beginTransaction();
        
        try {
            $placeholders = implode(',', array_fill(0, count($colores_seleccionados), '?'));
            $sqlEliminar = "DELETE FROM calzado_color 
                           WHERE id_calzado = ? AND id_color IN ($placeholders)";
            
            $stmtEliminar = $conexion->prepare($sqlEliminar);
            $params = array_merge([$id_calzado], $colores_seleccionados);
            
            $stmtEliminar->execute($params);
            
            // Confirmar transacción
            $conexion->commit();
            registrarAccion($conexion, $id_usuario_accion, $nombre_usuario_accion);
            echo "<script>alert('Colores eliminados correctamente'); window.location.href='" . $_SERVER["PHP_SELF"] . "?id=" . $id_calzado . "';</script>";
        } catch (PDOException $e) {
            // Revertir transacción en caso de error
            $conexion->rollBack();
            echo "<script>alert('Error al eliminar colores: " . $e->getMessage() . "');</script>";
        }
    }
}

// Agregar tallas al calzado
if (isset($_POST["agregar_tallas"])) {
    $tallas_seleccionadas = $_POST["tallas_seleccionadas"] ?? [];
    
    if (empty($tallas_seleccionadas)) {
        echo "<script>alert('No se seleccionaron tallas para agregar');</script>";
    } else {
        // Iniciar transacción
        $conexion->beginTransaction();
        
        try {
            foreach ($tallas_seleccionadas as $talla) {
                // Verificar si la talla ya está asociada
                $sqlVerificar = "SELECT COUNT(*) FROM calzado_talla 
                             WHERE id_calzado = :id_calzado AND id_talla = :id_talla";
                $stmtVerificar = $conexion->prepare($sqlVerificar);
                $stmtVerificar->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
                $stmtVerificar->bindParam(':id_talla', $talla, PDO::PARAM_INT);
                $stmtVerificar->execute();
                
                if ($stmtVerificar->fetchColumn() == 0) {
                    // Insertar nueva talla
                    $sqlInsertar = "INSERT INTO calzado_talla (id_calzado, id_talla) 
                                  VALUES (:id_calzado, :id_talla)";
                    $stmtInsertar = $conexion->prepare($sqlInsertar);
                    $stmtInsertar->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
                    $stmtInsertar->bindParam(':id_talla', $talla, PDO::PARAM_INT);
                    $stmtInsertar->execute();
                }
            }
            
            // Confirmar transacción
            $conexion->commit();
            registrarAccion($conexion, $id_usuario_accion, $nombre_usuario_accion);
            echo "<script>alert('Tallas agregadas correctamente'); window.location.href='" . $_SERVER["PHP_SELF"] . "?id=" . $id_calzado . "';</script>";
        } catch (PDOException $e) {
            // Revertir transacción en caso de error
            $conexion->rollBack();
            echo "<script>alert('Error al agregar tallas: " . $e->getMessage() . "');</script>";
        }
    }
}

// Eliminar tallas del calzado
if (isset($_POST["eliminar_tallas"])) {
    $tallas_seleccionadas = $_POST["tallas_eliminar"] ?? [];
    
    if (empty($tallas_seleccionadas)) {
        echo "<script>alert('No se seleccionaron tallas para eliminar');</script>";
    } else {
        // Iniciar transacción
        $conexion->beginTransaction();
        
        try {
            $placeholders = implode(',', array_fill(0, count($tallas_seleccionadas), '?'));
            $sqlEliminar = "DELETE FROM calzado_talla 
                           WHERE id_calzado = ? AND id_talla IN ($placeholders)";
            
            $stmtEliminar = $conexion->prepare($sqlEliminar);
            $params = array_merge([$id_calzado], $tallas_seleccionadas);
            
            $stmtEliminar->execute($params);
            
            // Confirmar transacción
            $conexion->commit();
            registrarAccion($conexion, $id_usuario_accion, $nombre_usuario_accion);
            echo "<script>alert('Tallas eliminadas correctamente'); window.location.href='" . $_SERVER["PHP_SELF"] . "?id=" . $id_calzado . "';</script>";
        } catch (PDOException $e) {
            // Revertir transacción en caso de error
            $conexion->rollBack();
            echo "<script>alert('Error al eliminar tallas: " . $e->getMessage() . "');</script>";
        }
    }
}

// Obtener todos los colores disponibles
$sqlTodosColores = "SELECT * FROM color";
$stmtTodosColores = $conexion->prepare($sqlTodosColores);
$stmtTodosColores->execute();
$todosColores = $stmtTodosColores->fetchAll(PDO::FETCH_ASSOC);

// Obtener colores asociados al calzado
$sqlColoresAsociados = "SELECT c.id_color, c.nombre_color 
                       FROM calzado_color cc 
                       JOIN color c ON cc.id_color = c.id_color 
                       WHERE cc.id_calzado = :id_calzado";
$stmtColoresAsociados = $conexion->prepare($sqlColoresAsociados);
$stmtColoresAsociados->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
$stmtColoresAsociados->execute();
$coloresAsociados = $stmtColoresAsociados->fetchAll(PDO::FETCH_ASSOC);

// Obtener todas las tallas disponibles
$sqlTodasTallas = "SELECT * FROM talla";
$stmtTodasTallas = $conexion->prepare($sqlTodasTallas);
$stmtTodasTallas->execute();
$todasTallas = $stmtTodasTallas->fetchAll(PDO::FETCH_ASSOC);

// Obtener tallas asociadas al calzado
$sqlTallasAsociadas = "SELECT t.id_talla, t.talla 
                      FROM calzado_talla ct 
                      JOIN talla t ON ct.id_talla = t.id_talla 
                      WHERE ct.id_calzado = :id_calzado";
$stmtTallasAsociadas = $conexion->prepare($sqlTallasAsociadas);
$stmtTallasAsociadas->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
$stmtTallasAsociadas->execute();
$tallasAsociadas = $stmtTallasAsociadas->fetchAll(PDO::FETCH_ASSOC);

// Crear arrays de IDs para facilitar las comparaciones
$idsColoresAsociados = array_column($coloresAsociados, 'id_color');
$idsTallasAsociadas = array_column($tallasAsociadas, 'id_talla');
?>

<!DOCTYPE html>
<html>
<head>
    <title>ShoeStock - Editar Calzado</title>
    <link rel="stylesheet" href="styles/editar-calzado.css">
    <style>
        /* Estilos adicionales para mejorar la selección múltiple */
        .select-container {
            margin-bottom: 15px;
        }
        
        select[multiple] {
            width: 100%;
            min-height: 120px;
            padding: 8px;
        }
        
        .select-hint {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .btn-agregar, .btn-eliminar {
            padding: 8px 15px;
            margin-top: 10px;
            cursor: pointer;
        }
        
        .btn-agregar {
            background-color: #4CAF50;
            color: white;
            border: none;
        }
        
        .btn-eliminar {
            background-color: #f44336;
            color: white;
            border: none;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .datos-actuales {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        
        .datos-lista {
            list-style-type: none;
            padding: 0;
        }
        
        .datos-lista li {
            margin-bottom: 5px;
            padding: 3px;
            background-color: #eee;
            border-radius: 3px;
            display: inline-block;
            margin-right: 5px;
        }
    </style>
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
            
            <div class="actualizar">
                <form action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $id_calzado; ?>" method="POST">
                    <div class="form-group">
                        <input class="noeditar" type="text" name="modelo" value="<?php echo htmlspecialchars($modelo); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <input type="text" name="tipo" value="<?php echo htmlspecialchars($tipo); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <input type="text" name="material" value="<?php echo htmlspecialchars($material); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <input type="text" name="marca" value="<?php echo htmlspecialchars($marca); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <input class="noeditar" type="text" name="precio" value="<?php echo htmlspecialchars($precio); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <input type="number" name="cantidad" value="<?php echo htmlspecialchars($cantidad); ?>">
                    </div>

                    <div class="botones">
                        <button type="submit" name="actualizar" class="actualizar">Actualizar Cantidad</button>
                        <button type="reset" class="reset">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="tyc">
            <!-- SECCIÓN DE COLORES -->
            <div class="colores">
                <h2>Gestión de Colores</h2>
                
                <!-- Colores actuales -->
                <div class="datos-actuales">
                    <h3>Colores actuales:</h3>
                    <?php if (count($coloresAsociados) > 0): ?>
                        <ul class="datos-lista">
                            <?php foreach ($coloresAsociados as $color): ?>
                                <li><?php echo htmlspecialchars($color['nombre_color']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay colores asociados a este calzado.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Agregar colores -->
                <form action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $id_calzado; ?>" method="POST">
                    <div class="select-container">
                        <h3>Agregar colores:</h3>
                        <select name="colores_seleccionados[]" multiple>
                            <?php foreach ($todosColores as $color): ?>
                                <?php if (!in_array($color['id_color'], $idsColoresAsociados)): ?>
                                    <option value="<?php echo $color['id_color']; ?>">
                                        <?php echo htmlspecialchars($color['nombre_color']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <div class="select-hint">Mantén presionada la tecla Ctrl (o Cmd en Mac) para seleccionar múltiples opciones</div>
                    </div>
                    <button class="btn-agregar" type="submit" name="agregar_colores">Agregar Colores</button>
                </form>
                
                <!-- Eliminar colores -->
                <form action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $id_calzado; ?>" method="POST">
                    <div class="select-container">
                        <h3>Eliminar colores:</h3>
                        <select name="colores_eliminar[]" multiple>
                            <?php foreach ($coloresAsociados as $color): ?>
                                <option value="<?php echo $color['id_color']; ?>">
                                    <?php echo htmlspecialchars($color['nombre_color']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="select-hint">Mantén presionada la tecla Ctrl (o Cmd en Mac) para seleccionar múltiples opciones</div>
                    </div>
                    <button class="btn-eliminar" type="submit" name="eliminar_colores">Eliminar Colores</button>
                </form>
            </div>
            
            <!-- SECCIÓN DE TALLAS -->
            <div class="tallas">
                <h2>Gestión de Tallas</h2>
                
                <!-- Tallas actuales -->
                <div class="datos-actuales">
                    <h3>Tallas actuales:</h3>
                    <?php if (count($tallasAsociadas) > 0): ?>
                        <ul class="datos-lista">
                            <?php foreach ($tallasAsociadas as $talla): ?>
                                <li><?php echo htmlspecialchars($talla['talla']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay tallas asociadas a este calzado.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Agregar tallas -->
                <form action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $id_calzado; ?>" method="POST">
                    <div class="select-container">
                        <h3>Agregar tallas:</h3>
                        <select name="tallas_seleccionadas[]" multiple>
                            <?php foreach ($todasTallas as $talla): ?>
                                <?php if (!in_array($talla['id_talla'], $idsTallasAsociadas)): ?>
                                    <option value="<?php echo $talla['id_talla']; ?>">
                                        <?php echo htmlspecialchars($talla['talla']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <div class="select-hint">Mantén presionada la tecla Ctrl (o Cmd en Mac) para seleccionar múltiples opciones</div>
                    </div>
                    <button class="btn-agregar" type="submit" name="agregar_tallas">Agregar Tallas</button>
                </form>
                
                <!-- Eliminar tallas -->
                <form action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $id_calzado; ?>" method="POST">
                    <div class="select-container">
                        <h3>Eliminar tallas:</h3>
                        <select name="tallas_eliminar[]" multiple>
                            <?php foreach ($tallasAsociadas as $talla): ?>
                                <option value="<?php echo $talla['id_talla']; ?>">
                                    <?php echo htmlspecialchars($talla['talla']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="select-hint">Mantén presionada la tecla Ctrl (o Cmd en Mac) para seleccionar múltiples opciones</div>
                    </div>
                    <button class="btn-eliminar" type="submit" name="eliminar_tallas">Eliminar Tallas</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>