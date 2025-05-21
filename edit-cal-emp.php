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
    $valores = [];
    
    foreach ($colores_seleccionados as $color) {
        // Verificar si el color ya está asociado
        $sqlVerificar = "SELECT id_cal_color FROM calzado_color 
                         WHERE id_calzado = :id_calzado AND id_color = :id_color";
        $stmtVerificar = $conexion->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
        $stmtVerificar->bindParam(':id_color', $color, PDO::PARAM_INT);
        $stmtVerificar->execute();
        
        if ($stmtVerificar->rowCount() == 0) {
            $valores[] = "(:id_calzado, $color)";
        }
    }
    
    if (!empty($valores)) {
        $sqlInsertar = "INSERT INTO calzado_color (id_calzado, id_color) VALUES " . implode(",", $valores);
        $stmtInsertar = $conexion->prepare($sqlInsertar);
        $stmtInsertar->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
        
        if ($stmtInsertar->execute()) {
            registrarAccion($conexion, $id_usuario_accion, $nombre_usuario_accion);
            echo "<script>alert('Colores agregados correctamente'); window.location.href='panel-empleado.php';</script>";
        } else {
            echo "<script>alert('Error al agregar colores'); window.location.href='panel-empleado.php';</script>";
        }
    } else {
        echo "<script>alert('Los colores seleccionados ya están asociados al modelo'); window.location.href='panel-empleado.php';</script>";
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
            registrarAccion($conexion, $id_usuario_accion, $nombre_usuario_accion);
            echo "<script>alert('Colores eliminados correctamente'); window.location.href='panel-empleado.php';</script>";
        } else {
            echo "<script>alert('Error al eliminar colores'); window.location.href='panel-empleado.php';</script>";
        }
    } else {
        echo "<script>alert('No se seleccionaron colores para eliminar'); window.location.href='panel-empleado.php';</script>";
    }
}

// Agregar tallas al calzado
if (isset($_POST["agregar_tallas"])) {
    $tallas_seleccionadas = $_POST["tallas_seleccionadas"] ?? [];
    $valores = [];
    
    foreach ($tallas_seleccionadas as $talla) {
        // Verificar si la talla ya está asociada
        $sqlVerificar = "SELECT id_cal_talla FROM calzado_talla 
                         WHERE id_calzado = :id_calzado AND id_talla = :id_talla";
        $stmtVerificar = $conexion->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
        $stmtVerificar->bindParam(':id_talla', $talla, PDO::PARAM_INT);
        $stmtVerificar->execute();
        
        if ($stmtVerificar->rowCount() == 0) {
            $valores[] = "(:id_calzado, $talla)";
        }
    }
    
    if (!empty($valores)) {
        $sqlInsertar = "INSERT INTO calzado_talla (id_calzado, id_talla) VALUES " . implode(",", $valores);
        $stmtInsertar = $conexion->prepare($sqlInsertar);
        $stmtInsertar->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
        
        if ($stmtInsertar->execute()) {
            registrarAccion($conexion, $id_usuario_accion, $nombre_usuario_accion);
            echo "<script>alert('Tallas agregadas correctamente'); window.location.href='panel-empleado.php';</script>";
        } else {
            echo "<script>alert('Error al agregar tallas'); window.location.href='panel-empleado.php';</script>";
        }
    } else {
        echo "<script>alert('Las tallas seleccionadas ya están asociadas al modelo'); window.location.href='panel-empleado.php';</script>";
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
            registrarAccion($conexion, $id_usuario_accion, $nombre_usuario_accion);
            echo "<script>alert('Tallas eliminadas correctamente'); window.location.href='panel-empleado.php';</script>";
        } else {
            echo "<script>alert('Error al eliminar tallas'); window.location.href='panel-empleado.php';</script>";
        }
    } else {
        echo "<script>alert('No se seleccionaron tallas para eliminar'); window.location.href='panel-empleado.php';</script>";
    }
}

// Obtener todos los colores disponibles
$sqlTodosColores = "SELECT * FROM color";
$stmtTodosColores = $conexion->prepare($sqlTodosColores);
$stmtTodosColores->execute();

// Obtener colores asociados al calzado
$sqlColoresAsociados = "SELECT c.id_color, c.nombre_color 
                       FROM calzado_color cc 
                       JOIN color c ON cc.id_color = c.id_color 
                       WHERE cc.id_calzado = :id_calzado";
$stmtColoresAsociados = $conexion->prepare($sqlColoresAsociados);
$stmtColoresAsociados->bindParam(':id_calzado', $id_calzado, PDO::PARAM_INT);
$stmtColoresAsociados->execute();

// Obtener todas las tallas disponibles
$sqlTodasTallas = "SELECT * FROM talla";
$stmtTodasTallas = $conexion->prepare($sqlTodasTallas);
$stmtTodasTallas->execute();

// Obtener tallas asociadas al calzado
$sqlTallasAsociadas = "SELECT t.id_talla, t.talla 
                      FROM calzado_talla ct 
                      JOIN talla t ON ct.id_talla = t.id_talla 
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
                        <input type="text" name="tipo" id="tipo" value="<?php echo htmlspecialchars($tipo); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <input type="text" name="material" id="material" value="<?php echo htmlspecialchars($material); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <input type="text" name="marca" id="marca" value="<?php echo htmlspecialchars($marca); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <input class="noeditar" type="text" name="precio" id="precio" value="<?php echo htmlspecialchars($precio); ?>" readonly>
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
                        <input type="hidden" name="modelo_agregar_colores" value="<?php echo htmlspecialchars($modelo); ?>">
                        <?php foreach ($stmtTodosColores->fetchAll(PDO::FETCH_ASSOC) as $color): ?>
                            <input type="checkbox" name="colores_seleccionados[]" value="<?php echo $color['id_color']; ?>">
                            <?php echo htmlspecialchars($color['nombre_color']); ?><br>
                        <?php endforeach; ?>
                        <button class="btn-agregar" type="submit" name="agregar_colores">Agregar Colores</button>
                    </form>
                </div>

                <div class="tallas">
                    <h2>Agregar Tallas</h2>
                    <form action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $id_calzado; ?>" method="POST">
                        <input type="hidden" name="modelo_agregar_tallas" value="<?php echo htmlspecialchars($modelo); ?>">
                        <?php foreach ($stmtTodasTallas->fetchAll(PDO::FETCH_ASSOC) as $talla): ?>
                            <input type="checkbox" name="tallas_seleccionadas[]" value="<?php echo $talla['id_talla']; ?>">
                            <?php echo htmlspecialchars($talla['talla']); ?><br>
                        <?php endforeach; ?>
                        <button class="btn-agregar" type="submit" name="agregar_tallas">Agregar Tallas</button>
                    </form>
                </div>
            </div>

            <div class="eliminar">
                <div class="colores">
                    <h2>Eliminar Colores</h2>
                    <form action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $id_calzado; ?>" method="POST">
                        <input type="hidden" name="modelo_eliminar_colores" value="<?php echo htmlspecialchars($modelo); ?>">
                        <?php foreach ($stmtColoresAsociados->fetchAll(PDO::FETCH_ASSOC) as $color): ?>
                            <input type="checkbox" name="colores_seleccionados[]" value="<?php echo $color['id_color']; ?>">
                            <?php echo htmlspecialchars($color['nombre_color']); ?><br>
                        <?php endforeach; ?>
                        <button class="btn-eliminar" type="submit" name="eliminar_colores">Eliminar Colores</button>
                    </form>
                </div>

                <div class="tallas">
                    <h2>Eliminar Tallas</h2>
                    <form action="<?php echo $_SERVER["PHP_SELF"] . '?id=' . $id_calzado; ?>" method="POST">
                        <input type="hidden" name="modelo_eliminar_tallas" value="<?php echo htmlspecialchars($modelo); ?>">
                        <?php foreach ($stmtTallasAsociadas->fetchAll(PDO::FETCH_ASSOC) as $talla): ?>
                            <input type="checkbox" name="tallas_seleccionadas[]" value="<?php echo $talla['id_talla']; ?>">
                            <?php echo htmlspecialchars($talla['talla']); ?><br>
                        <?php endforeach; ?>
                        <button class="btn-eliminar" type="submit" name="eliminar_tallas">Eliminar Tallas</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>