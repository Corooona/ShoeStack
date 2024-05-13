<?php
include("conexion.php");

if (!empty($_POST)) {
    $usuario = mysqli_real_escape_string($conexion, $_POST['user']);
    $pass = mysqli_real_escape_string($conexion, $_POST['pass']);
    $password_encriptado = sha1($pass);
    $sql = "SELECT id_usuario, id_rol FROM usuario WHERE nombre_user='$usuario' AND password='$pass'";

    $resultado = $conexion->query($sql);
    $rows = $resultado->num_rows;

    if ($rows > 0) {
        $row = $resultado->fetch_assoc();
        session_start();
        $_SESSION['id_usuario'] = $row["id_usuario"];
        $_SESSION['id_rol'] = $row["id_rol"];

        if ($row['id_rol'] == '1') {
            header("Location: panel-admin.php"); // Redirige a la página de administrador
            exit(); //EVITA CICLO
        } elseif ($row['id_rol'] == '2') {
            header("Location: panel-empleado.php"); 
            exit();
        } else {
            echo "<script>alert('Rol de usuario no válido'); window.location= 'index.php';</script>";
        }
    } else {
        echo "<script>alert('Verifica usuario o contraseña'); window.location= 'index.php';</script>";
    }
}


//CONSULTA PARA VER LOS USUARIOS
$usuarios = "SELECT * from usuario";

?>


<!DOCTYPE html>
<html lang="en">

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
			<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST" class="formulario">
				<h1 class="titulos">INICIO DE SESION</h1>

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