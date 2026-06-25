document.addEventListener('DOMContentLoaded', () => {
    const selectBodega = document.getElementById('bodega');
    
    if (selectBodega) {
        selectBodega.addEventListener('change', async function () {
            const idBodega = this.value;
            const selectSucursal = document.getElementById('sucursal');

            // Limpiar opciones previas
            selectSucursal.innerHTML = '<option value=""></option>';

            if (idBodega === '') return;

            try {
                const response = await fetch(`get_sucursales.php?id_bodega=${idBodega}`);
                if (!response.ok) throw new Error('Error al obtener sucursales');

                const sucursales = await response.json();

                sucursales.forEach(sucursal => {
                    const option = document.createElement('option');
                    option.value = sucursal.id;
                    option.text = sucursal.nombre;
                    selectSucursal.appendChild(option);
                });
            } catch (error) {
                console.error(error);
                alert('No se pudieron cargar las sucursales asociadas.');
            }
        });
    }
});