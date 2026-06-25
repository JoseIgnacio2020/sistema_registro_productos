<?php
header('Content-Type: application/json');

require_once 'conexion.php';

// Si usamos la asignación global nos aseguramos de que el editor y PHP la reconozcan
if (!isset($db) && isset($GLOBALS['db'])) {
    $db = $GLOBALS['db'];
}

$id_bodega = isset($_GET['id_bodega']) ? intval($_GET['id_bodega']) : 0;

if ($id_bodega <= 0) {
    echo json_encode([]);
    exit;
}

// Consultar sucursales filtradas por la bodega elegida usando parámetros ($1)
$query = "SELECT id, nombre FROM sucursal WHERE id_bodega = $1 ORDER BY nombre ASC";
$result = pg_query_params($db, $query, array($id_bodega));

$sucursales = [];
while ($row = pg_fetch_assoc($result)) {
    $sucursales[] = [
        "id" => (int)$row['id'],
        "nombre" => $row['nombre']
    ];
}

pg_free_result($result);
pg_close($db);

echo json_encode($sucursales);