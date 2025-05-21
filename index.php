<?php
session_start();
include("conexion.php");

// Configuración de seguridad
$max_attempts = 3;
$block_time = 10;

// Verificar bloqueo existente
if (isset($_SESSION['blocked_until'])) {
    $remaining_time = $_SESSION['blocked_until'] - time();

    if ($remaining_time > 0) {
        echo "<script>
                let seconds = $remaining_time;
                
                const timerInterval = setInterval(
                () => {
                    seconds--;
                    
                    if (seconds <= 0) {
                        clearInterval(timerInterval);
                        window.location.reload();
                    }
                }, 1000);
        </script>";
    } else {
        unset($_SESSION['blocked_until']);
        unset($_SESSION['login_attempts']);
    }
}

// INICIO DE SESIÓN
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['user']);
    $pass = trim($_POST['pass']);

    $sql = "SELECT id_usuario, id_rol, password FROM usuario WHERE nombre_user=:usuario";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(":usuario", $usuario);
    $stmt->execute();
    $row = $stmt->fetch();
    
    if ($row && password_verify($pass, $row['password'])) {
        // Login exitoso - resetear contador de intentos
        unset($_SESSION['login_attempts']);
        unset($_SESSION['blocked_until']);

        $_SESSION['id_usuario'] = $row['id_usuario'];
        $_SESSION['id_rol'] = $row['id_rol'];

        if ($row['id_rol'] == 1) {
            header("Location: panel-admin.php");
            exit();
        } elseif ($row['id_rol'] == 2) {
            header("Location: panel-empleado.php");
            exit();
        }
    } else {
         // Login fallido
         if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 1;
        } else {
            $_SESSION['login_attempts']++;
        }

        if ($_SESSION['login_attempts'] >= $max_attempts) {
            $_SESSION['blocked_until'] = time() + $block_time;
            echo "<script>
                alert('Demasiados intentos fallidos. Por favor espere 10 segundos.');
                setTimeout(() => { window.location.href=''; }, 100);
            </script>";
            exit();
        }
        
        echo "<script>alert('Usuario o contraseña incorrectos');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShoeStock - Login</title>
    <link rel="stylesheet" href="styles/login.css">
</head>

<body>
    <div class="container">
        <div class="left-side">
            <img src="images/LOGO.png" alt="Logo-Shoestack">
        </div>
        <div class="right-side">
            <form action="" method="POST" class="formulario">
                <h1 class="titulos">INICIO DE SESIÓN</h1>
                <h2 class="subtitulos">NOMBRE DE USUARIO</h2>
                <input type="text" name="user" class="input" placeholder="Usuario" required />
                <h2 class="subtitulos">CONTRASEÑA</h2>
                <input type="password" name="pass" class="input" placeholder="Contraseña" required />
                <button type="submit" class="button">INICIAR SESIÓN</button>
            </form>
        </div>
    </div>
</body>

</html>
