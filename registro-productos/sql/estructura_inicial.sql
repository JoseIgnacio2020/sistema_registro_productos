-- =============================================================================
-- SCRIPT DE CONFIGURACIÓN INICIAL - SISTEMA DE PRODUCTOS
-- Módulo: PostgreSQL Nativo para Apache (XAMPP)
-- =============================================================================

-- 1. CREACIÓN DE LA BASE DE DATOS
-- Nota: Si ejecutas este script en pgAdmin 4, asegúrate de estar conectado a la BD "postgres" antes de crear esta.
CREATE DATABASE productos
    WITH
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'Spanish_Chile.1252'
    LC_CTYPE = 'Spanish_Chile.1252'
    LOCALE_PROVIDER = 'libc'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1
    IS_TEMPLATE = False;

COMMENT ON DATABASE productos IS 'Base de datos que almacena los datos de los productos.';

-- ¡IMPORTANTE! Si corres este script por lotes en una consola, asegúrate de conectarte a la BD "productos" antes de seguir.
-- \c productos;

-- =============================================================================
-- 2. CREACIÓN DE TABLAS MAESTRAS (DICCIONARIOS)
-- =============================================================================

-- Tabla: Bodega
CREATE TABLE IF NOT EXISTS bodega (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

COMMENT ON TABLE bodega IS 'Almacena las bodegas principales del sistema.';

-- Tabla: Sucursal (Relación jerárquica con Bodega)
CREATE TABLE IF NOT EXISTS sucursal (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_bodega INT NOT NULL,
    CONSTRAINT fk_sucursal_bodega 
        FOREIGN KEY (id_bodega) 
        REFERENCES bodega(id) 
        ON DELETE CASCADE ON UPDATE CASCADE
);

COMMENT ON TABLE sucursal IS 'Almacena las sucursales que pertenecen de manera exclusiva a una bodega.';

-- Tabla: Moneda
CREATE TABLE IF NOT EXISTS moneda (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(15) NOT NULL UNIQUE
);

COMMENT ON TABLE moneda IS 'Almacena los tipos de moneda soportados para los precios.';

-- =============================================================================
-- 3. CREACIÓN DE LA TABLA PRINCIPAL
-- =============================================================================

-- Tabla: Producto
CREATE TABLE IF NOT EXISTS producto (
    codigo VARCHAR(15) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    id_bodega INT NOT NULL,
    id_sucursal INT NOT NULL,
    id_moneda INT NOT NULL,
    precio NUMERIC(12, 2) NOT NULL CHECK (precio >= 0),
    materiales VARCHAR(30)[] NOT NULL DEFAULT '{}',
    descripcion VARCHAR(1000),
    
    CONSTRAINT fk_producto_bodega
        FOREIGN KEY (id_bodega)
        REFERENCES bodega(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
        
    CONSTRAINT fk_producto_sucursal
        FOREIGN KEY (id_sucursal)
        REFERENCES sucursal(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
        
    CONSTRAINT fk_producto_moneda
        FOREIGN KEY (id_moneda)
        REFERENCES moneda(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

COMMENT ON TABLE producto IS 'Almacena los datos principales de los productos del sistema.';
COMMENT ON COLUMN producto.materiales IS 'Contiene los materiales seleccionados mediante los checkboxes en formato de arreglo.';

-- =============================================================================
-- 4. INSERCIÓN DE DATOS DE PRUEBA (SEEDERS)
-- =============================================================================

-- Insertar Bodegas iniciales
INSERT INTO bodega (nombre) VALUES 
('bodega 1'),
('bodega 2'),
('bodega 3');

-- Insertar Sucursales asignadas a sus respectivas bodegas
INSERT INTO sucursal (nombre, id_bodega) VALUES 
('sucursal 1', 1),
('sucursal 2', 1),
('sucursal 3', 2);

-- Insertar Monedas iniciales
INSERT INTO moneda (nombre) VALUES 
('Peso Chileno'),
('Dólar'),
('Euro');

-- Insertar un Producto de ejemplo (para validar la estructura relacional)
INSERT INTO producto (codigo, nombre, id_bodega, id_sucursal, id_moneda, precio, materiales, descripcion)
VALUES (
    'PROD01K', 
    'Set Comedor', 
    1, 
    2, 
    2, 
    1500.00, 
    '{"madera", "vidrio"}', 
    'Elegante set de comedor de madera natural, incluye mesa y sillas. Diseño clásico y duradero.'
);

-- =============================================================================
-- 5. QUERY DE AUDITORÍA Y COMPROBACIÓN DE MAESTROS (INNER JOIN)
-- =============================================================================
/* Para ejecutar y auditar los nombres cruzados, utiliza la siguiente consulta:

SELECT 
    p.codigo AS "Código Producto",
    p.nombre AS "Nombre Producto",
    b.nombre AS "Bodega",
    s.nombre AS "Sucursal",
    m.nombre AS "Moneda",
    p.precio AS "Precio",
    p.materiales AS "Materiales (Array)",
    p.descripcion AS "Descripción"
FROM producto p
INNER JOIN bodega b ON p.id_bodega = b.id
INNER JOIN sucursal s ON p.id_sucursal = s.id
INNER JOIN moneda m ON p.id_moneda = m.id
ORDER BY p.codigo ASC;
*/