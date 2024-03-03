<!-- paso 3 -->
<?php
include("conexion.php");

if(!empty($_POST)){
	$usuario = mysqli_real_escape_string($conexion,$_POST['user']);
	$pass = mysqli_real_escape_string($conexion,$_POST['pass']);
	$password_encriptado = sha1($pass);
	$sql="SELECT id_usuario FROM usuario WHERE nombre_user='$usuario' AND password='$pass' ";

	$resultado = $conexion->query($sql);
	$rows = $resultado->num_rows;
	
	if($rows>0){
		$row=$resultado->fetch_assoc();
		$_SESSION['id_usuario']= $row["id_usuario"];
		header("Location: inicio.php");
	}
	else{
		echo  "<script>
		alert('Verifica usuario o contraseña');
		window.location= 'index.php';
		</script";
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
    <title>Document</title>
</head>
<body>
    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST" >
		<input type="text" class="form-control"  name="user"placeholder="Usuario" />
	    <input type="password" name="pass"class="form-control" placeholder="Contraseña" />
	    <button type="submit">Ingresar</button>
    </form>

</body>
</html>