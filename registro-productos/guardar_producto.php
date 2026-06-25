<?php
header('Content-Type: application/json');

// 1. Incluir la conexión centralizada
require_once 'conexion.php';

// Asegurar que la variable $db esté disponible globalmente
if (!isset($db) && isset($GLOBALS['db'])) {
    $db = $GLOBALS['db'];
}

// 2. Verificar que la petición sea estrictamente POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método de petición no permitido.']);
    exit;
}

// 3. Sanitización básica y captura de datos (Seguridad en la entrada)
$codigo      = isset($_POST['codigo']) ? trim(strip_tags($_POST['codigo'])) : '';
$nombre      = isset($_POST['nombre']) ? trim(strip_tags($_POST['nombre'])) : '';
$id_bodega   = isset($_POST['bodega']) ? intval($_POST['bodega']) : 0;
$id_sucursal = isset($_POST['sucursal']) ? intval($_POST['sucursal']) : 0;
$id_moneda   = isset($_POST['moneda']) ? intval($_POST['moneda']) : 0;
$precio      = isset($_POST['precio']) ? trim($_POST['precio']) : '';
$descripcion = isset($_POST['descripcion']) ? trim(strip_tags($_POST['descripcion'])) : '';

// 4. Procesamiento de los Checkboxes (Arreglo de PostgreSQL)
// Mapeamos los posibles nombres de checkboxes del formulario
$materiales_disponibles = ['plastico', 'metal', 'madera', 'vidrio', 'textil'];
$materiales_seleccionados = [];

foreach ($materiales_disponibles as $material) {
    if (isset($_POST[$material])) {
        // Sanitizamos el string por seguridad
        $materiales_seleccionados[] = '"' . pg_escape_string($db, $_POST[$material]) . '"';
    }
}

// Transformamos el array de PHP al formato de array de Postgres: {"madera","vidrio"}
$postgres_array_materiales = '{' . implode(',', $materiales_seleccionados) . '}';


try {
    // 5. Validación de UNICIDAD mediante consulta parametrizada ($1)
    $query_check = "SELECT COUNT(*) AS total FROM producto WHERE codigo = $1";
    $stmt_check = pg_prepare($db, "check_unicidad", $query_check);
    
    if (!$stmt_check) {
        throw new Exception("Error al preparar la consulta de unicidad.");
    }
    
    $result_check = pg_execute($db, "check_unicidad", array($codigo));
    $row_check = pg_fetch_assoc($result_check);
    
    if (intval($row_check['total']) > 0) {
        // El código ya existe. Retornamos el estado 'duplicado' exigido por el frontend
        echo json_encode(['status' => 'duplicado', 'message' => 'El código del producto ya está registrado.']);
        exit;
    }

    // 6. Inserción SEGURA (Consultas preparadas con marcadores de posición $1, $2, etc.)
    $query_insert = "INSERT INTO producto (codigo, nombre, id_bodega, id_sucursal, id_moneda, precio, materiales, descripcion) 
                     VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
    
    // Preparamos la sentencia en el servidor de la BD (Previene SQL Injection al 100%)
    $stmt_insert = pg_prepare($db, "insert_producto", $query_insert);
    
    if (!$stmt_insert) {
        throw new Exception("Error al preparar la sentencia de inserción.");
    }
    
    // Ejecutamos pasando los parámetros ordenados en un array plano
    $result_insert = pg_execute($db, "insert_producto", array(
        $codigo,
        $nombre,
        $id_bodega,
        $id_sucursal,
        $id_moneda,
        floatval($precio),
        $postgres_array_materiales,
        $descripcion
    ));

    if ($result_insert) {
        echo json_encode(['status' => 'success', 'message' => 'Producto registrado con éxito.']);
    } else {
        throw new Exception("No se pudo ejecutar la inserción en la base de datos.");
    }

} catch (Exception $e) {
    // Registramos el error internamente por seguridad, no se lo exponemos al usuario final
    error_log("Error en guardar_producto.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Ocurrió un error interno en el servidor al intentar guardar.']);
} finally {
    // 7. Cerrar la conexión de manera limpia
    pg_close($db);
}