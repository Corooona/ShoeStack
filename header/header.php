<!-- header.php -->

<style>
    .header {
        font-family: sans-serif;
        background-color: #6CBFDF;
        display: flex;
        align-items: center;
        padding: 10px;
    }

    .header img {
        height: 50px;
    }

    .header h1 {
        margin: 0 20px;
        color: #2A4B6A;
    }

    .header nav {
        margin-left: auto;
    }

    .header nav a {
        color: blue;
        margin-left: 20px;
        font-weight: bold;
    }
</style>

<!-- CODIGO DE PHP QUE INDICA CUÁL SERÁ EL INICIO -->
<!-- ADMIN O EMPLEADO -->
<?php
session_start();

if (!empty($_POST)) {
    $usuario = mysqli_real_escape_string($conexion, $_POST['user']);
    $pass = mysqli_real_escape_string($conexion, $_POST['pass']);
    $password_encriptado = sha1($pass);
    $sql = "SELECT id_usuario, id_rol FROM usuario WHERE nombre_user='$usuario' AND password='$pass'";

    $resultado = $conexion->query($sql);
    $rows = $resultado->num_rows;

    if ($rows > 0) {
        $row = $resultado->fetch_assoc();
        $_SESSION['id_usuario'] = $row["id_usuario"];
        $_SESSION['id_rol'] = $row["id_rol"];
    } else {
        echo "<script>alert('Verifica usuario o contraseña'); window.location= 'index.php';</script>";
    }
}
?>


<div class="header">
    <img src="images/LOGO.png" alt="ShoeStock Logo">
    <h1>ShoeStock</h1>
    <nav>
        <?php if (!empty($_SESSION['id_rol'])): ?>
            <?php if ($_SESSION['id_rol'] == '1'): ?>
                <a href="panel-admin.php">INICIO</a>
                <a href="lista-usuarios.php">LISTA DE USUARIOS</a>
                <a href="lista-acceso.php">VER ACCESO</a>
            <?php elseif ($_SESSION['id_rol'] == '2'): ?>
                <a href="panel-empleado.php">INICIO</a>
            <?php endif; ?>
        <?php endif; ?>
        <a href="logout/logout.php">LOGOUT</a>
    </nav>
</div>
