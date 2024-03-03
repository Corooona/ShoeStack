<!-- archivo 2 -->
<?php
include("configuracion.php");
$conexion=mysqli_connect($server,$user,$pass,$db);

if(mysqli_connect_errno()){
    echo "no conectado", mysqli_connect_errno();
    exit();
}else{
    echo"conectado";
}
?>