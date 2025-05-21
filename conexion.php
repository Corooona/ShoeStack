<!-- Archivo Mejorado - 1 -->
<?php
$env = parse_ini_file('.env');
$server = $env['DB_HOST'];
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];
$db = $env['DB_NAME'];
try {
    $conexion = new PDO("mysql:host=$server;dbname=$db;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    } catch (PDOException $err) {
    die("Error de conexión: " . $err->getMessage());
}
?>