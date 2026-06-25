<?php
// conexion.php
const DB_HOST = "localhost";
const DB_PORT = "5432";
const DB_NAME = "productos";
const DB_USER = "postgres";
const DB_PASSWORD = "admin2026"; 

$connection_string = "host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD;

$db = pg_connect($connection_string);

if (!$db) {
    die("Error crítico: No se pudo conectar a PostgreSQL. Verifica las credenciales o si el servicio está activo.");
}

$GLOBALS['db'] = $db; 
?>