// Expresiones regulares
const regexSoloLetrasYNumeros = /^[a-zA-Z0-9]+$/;
const regexPrecio = /^\d+(\.\d{1,2})?$/;

// Formulario y Campos
const formulario = document.getElementById('formulario');
const codigo = document.getElementById('codigo');
const nombre = document.getElementById('nombre');
const bodega = document.getElementById('bodega');
const sucursal = document.getElementById('sucursal');
const moneda = document.getElementById('moneda');
const precio = document.getElementById('precio');
const descripcion = document.getElementById('descripcion');

formulario.addEventListener('submit', async function (event) {
    event.preventDefault();

    //Validaciones de los campos del Formulario
    if (!validaCodigo()) return;
    if (!validaNombre()) return;
    if (!validaBodega()) return;
    if (!validaSucursal()) return;
    if (!validaMoneda()) return;
    if (!validaPrecio()) return;
    if (!validaMateriales()) return;
    if (!validaDescripcion()) return;

    // Envío mediante AJAX (Fetch API) al archivo PHP
    const formData = new FormData(formulario);

    try {
        const response = await fetch('guardar_producto.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Error en la comunicación con el servidor');
        }

        const resultado = await response.json();

        if (resultado.status === 'duplicado') {
            alert(resultado.message); 
            codigo.focus();
        } else if (resultado.status === 'success') {
            alert(resultado.message); 
            formulario.reset();   
        } else {
            // Captura cualquier otro error controlado que envíe el servidor
            alert("Error al registrar: " + resultado.message);
        }

    } catch (error) {
        console.error('Error AJAX:', error);
        alert("Ocurrió un error al procesar la solicitud en el servidor.");
    }
});

function validaCodigo() {
    const valor = codigo.value.trim();
    if (valor === '') {
        alert("El código del producto no puede estar en blanco.");
        return false;
    }
    if (valor.length < 5 || valor.length > 15) {
        alert("El código del producto debe tener entre 5 y 15 caracteres.");
        return false;
    }
    if (!regexSoloLetrasYNumeros.test(valor)) {
        alert("El código del producto debe contener letras y números.");
        return false;
    }
    return true;
}

function validaNombre() {
    const valor = nombre.value.trim();
    if (valor === '') {
        alert("El nombre del producto no puede estar en blanco.");
        return false;
    }
    if (valor.length < 2 || valor.length > 50) {
        alert("El nombre del producto debe tener entre 2 y 50 caracteres.");
        return false;
    }
    return true;
}

function validaBodega() {
    if (bodega.value === '') {
        alert("Debe seleccionar una bodega.");
        return false;
    }
    return true;
}

function validaSucursal() {
    if (sucursal.value === '') {
        alert("Debe seleccionar una sucursal para la bodega seleccionada.");
        return false;
    }
    return true;
}

function validaMoneda() {
    if (moneda.value === '') {
        alert("Debe seleccionar una moneda para el producto.");
        return false;
    }
    return true;
}

function validaPrecio() {
    const valor = precio.value.trim();
    if (valor === '') {
        alert("El precio del producto no puede estar en blanco.");
        return false;
    }
    if (!regexPrecio.test(valor) || parseFloat(valor) < 0) {
        alert("El precio del producto debe ser un número positivo con hasta dos decimales.");
        return false;
    }
    return true;
}

function validaMateriales() {
    const seleccionados = formulario.querySelectorAll('input[type="checkbox"]:checked').length;
    if (seleccionados < 2) {
        alert("Debe seleccionar al menos dos materiales para el producto.");
        return false;
    }
    return true;
}

function validaDescripcion() {
    const valor = descripcion.value.trim();
    if (valor === '') {
        alert("La descripción del producto no puede estar en blanco.");
        return false;
    }
    if (valor.length < 10 || valor.length > 1000) {
        alert("La descripción del producto debe tener entre 10 y 1000 caracteres.");
        return false;
    }
    return true;
}