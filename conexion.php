<!-- Archivo Mejorado - 1 -->
<?php
$server="localhost";
$user="root";
$pass="";
$db="shoestock";

try{
    $conexion= new PDO("mysql:host=$server;dbname:$db",$user,$root);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    $conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
}catch(PDOException $err){
    die("Error de conexión: " . $err->getMessage());
}
?>