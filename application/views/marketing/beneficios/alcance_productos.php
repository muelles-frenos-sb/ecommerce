<style>
    #tabla_alcance_productos tbody * {
        font-size: 0.9em;
        padding: 3px;
    }
</style>

<?php $productos = $this->productos_model->obtener('productos', $datos); ?>

<div class="table-responsive">
    <table class="table-bordered" id="tabla_alcance_productos" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center">Id</th>
                <th class="text-center">Referencia</th>
                <th class="text-center">Descripción</th>
                <th class="text-center">Marca</th>
                <th class="text-center">Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($productos as $producto) { ?>
                <tr>
                    <td class="text-center"><?php echo $producto->id; ?></td>
                    <td class="text-left"><?php echo $producto->referencia; ?></td>
                    <td class="text-left"><?php echo $producto->notas; ?></td>
                    <td class="text-center"><?php echo $producto->marca; ?></td>
                    <td class="text-center">
                        <button
                            type="button"
                            class="btn btn-success btn-agregar-producto pl-3 pr-3"
                            data-id="<?php echo $producto->id; ?>"
                            data-referencia="<?php echo htmlspecialchars($producto->referencia); ?>"
                            data-notas="<?php echo htmlspecialchars($producto->notas); ?>">
                            +
                        </button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        // Limpiar mensajes previos
        $('#contenedor_mensaje_producto').html('');

        // Inicializar DataTable
        const tabla = $('#tabla_alcance_productos').DataTable({
            deferRender: true,
            fixedHeader: true,
            info: false,
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            ordering: false,
            pageLength: 100,
            paging: false,
            processing: true,
            scrollCollapse: true,
            scroller: true,
            scrollX: false,
            scrollY: '215px',
            searching: false,
            stateSave: false,
        });

        // DELEGACIÓN DE EVENTOS: Escuchamos el click en la tabla
        $('#tabla_alcance_productos tbody').on('click', '.btn-agregar-producto', function() {
            // Extraer los datos del botón que fue presionado
            const id = $(this).data('id');
            const ref = $(this).data('referencia');
            const notas = $(this).data('notas');

            // Llamar a la función que procesa el agregado
            agregarProductoAlBeneficio(id, ref, notas);
        });
    });

    agregarProductoAlBeneficio = async (productoId, referencia, descripcion) => {
        console.log(productoId, referencia, descripcion)
        let beneficioId = $("#beneficio_id").val()

        let respuesta = await consulta('crear', {
            tipo: 'marketing_beneficios_productos',
            beneficio_id: beneficioId,
            producto_id: productoId
        }, false)

        if (respuesta && respuesta.resultado) {
            mostrarAviso('exito', `Producto "${referencia}" agregado al beneficio`)
            listarProductosSeleccionados()
        } else {
            mostrarAviso('error', `No se pudo agregar el producto "${referencia}"`)
        }
    }
</script>