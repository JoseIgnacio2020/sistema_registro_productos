<?php
require_once 'conexion.php';

if (!isset($db) && isset($GLOBALS['db'])) {
    $db = $GLOBALS['db'];
}

// Consultas para poblar los select iniciales
$query_bodegas = pg_query($db, "SELECT id, nombre FROM bodega ORDER BY nombre ASC");
$query_monedas = pg_query($db, "SELECT id, nombre FROM moneda ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Productos</title>
    <link rel="stylesheet" href="style.css">   
</head>
<body>
    
  <main class="tarjeta">
    <h1>Formulario de Productos</h1>

    <form id="formulario">
      <label for="codigo">Código</label><br>
      <input type="text" id="codigo" name="codigo" value=""><br><br>

      <label for="nombre">Nombre</label><br>
      <input type="text" id="nombre" name="nombre" value=""><br><br>

      <label for="bodega">Bodega</label><br>
      <select id="bodega" name="bodega">
        <option value=""></option>
        <?php while ($row = pg_fetch_assoc($query_bodegas)): ?>
            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
        <?php endwhile; ?>
      </select>
      <br><br>

      <label for="sucursal">Sucursal</label><br>
      <select id="sucursal" name="sucursal">
        <option value=""></option>
      </select>
      <br><br>

      <label for="moneda">Moneda</label><br>
      <select id="moneda" name="moneda">
        <option value=""></option>
        <?php while ($row = pg_fetch_assoc($query_monedas)): ?>
            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
        <?php endwhile; ?>
      </select>
      <br><br>

      <label for="precio">Precio</label><br>
      <input type="text" id="precio" name="precio" value="">
      <br><br>

      <label for="material">Material del Producto</label><br>
      <input type="checkbox" id="plastico" name="plastico" value="plastico"> <label>Plástico</label>
      <input type="checkbox" id="metal" name="metal" value="metal"> <label>Metal</label> 
      <input type="checkbox" id="madera" name="madera" value="madera"> <label>Madera</label>
      <input type="checkbox" id="vidrio" name="vidrio" value="vidrio"> <label>Vidrio</label>
      <input type="checkbox" id="textil" name="textil" value="textil"> <label>Textil</label>
      <br><br>
  
      <p><label for="descripcion">Descripción</label></p>
      <textarea id="descripcion" name="descripcion" rows="5" cols="50"></textarea>
      <br><br>

      <button type="submit">Guardar Producto</button>
    </form>
  </main>

  <script src="carga_sucursales.js"></script>
  <script src="app.js"></script>
</body>
</html>
<?php 
// Opcional: Cerrar la conexión al terminar la renderización
pg_close($db); 
?>