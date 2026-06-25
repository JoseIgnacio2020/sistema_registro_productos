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
    <h1>Formulario de Producto</h1>

    <form id="formulario">

      <div class="grupo-formulario">
        <label for="codigo">Código</label>
        <input type="text" id="codigo" name="codigo">
      </div>

      <div class="grupo-formulario">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre">
      </div>

      <div class="grupo-formulario">
        <label for="bodega">Bodega</label>
        <select id="bodega" name="bodega">
          <option value=""></option>
          <?php while ($row = pg_fetch_assoc($query_bodegas)): ?>
            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="grupo-formulario">
        <label for="sucursal">Sucursal</label>
        <select id="sucursal" name="sucursal">
          <option value=""></option>
        </select>
      </div>

      <div class="grupo-formulario">
        <label for="moneda">Moneda</label>
        <select id="moneda" name="moneda">
          <option value=""></option>
          <?php while ($row = pg_fetch_assoc($query_monedas)): ?>
            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="grupo-formulario">
        <label for="precio">Precio</label>
        <input type="text" id="precio" name="precio">
      </div>

      <div class="grupo-formulario completo">
        <label class="label-bloque">Material del Producto</label>
        <div class="contenedor-checkboxes">
          <input type="checkbox" id="plastico" name="plastico" value="plastico"> <label for="plastico">Plástico</label>
          <input type="checkbox" id="metal" name="metal" value="metal"> <label for="metal">Metal</label>
          <input type="checkbox" id="madera" name="madera" value="madera"> <label for="madera">Madera</label>
          <input type="checkbox" id="vidrio" name="vidrio" value="vidrio"> <label for="vidrio">Vidrio</label>
          <input type="checkbox" id="textil" name="textil" value="textil"> <label for="textil">Textil</label>
        </div>
      </div>

      <div class="grupo-formulario completo">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="4"></textarea>
      </div>

      <div class="grupo-boton">
        <button type="submit">Guardar Producto</button>
      </div>

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