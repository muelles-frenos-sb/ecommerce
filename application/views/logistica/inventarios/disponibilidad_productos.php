<style>
    #tabla_disponibilidad_productos tbody * {
        font-size: 0.9em;
        padding: 3px;
    }
</style>

<?php $productos = $this->productos_model->obtener('productos', $datos); ?>

<div class="table-responsive">
    <table class="table-bordered" id="tabla_disponibilidad_productos" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center">Id</th>
                <th class="text-center">Referencia</th>
                <th class="text-center">Descripción</th>
                <th class="text-center">Marca</th>
                <th class="text-center">Disponible</th>
                <th class="text-center">Porcentaje</th>
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
                    <td class="text-center"><?php echo isset($producto->disponible) ? $producto->disponible : 0; ?></td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm porcentaje_input" style="min-width:80px;" value="100" min="0" max="100" step="0.1">
                    </td>
                    <td class="text-center">
                        <button
                            type="button"
                            class="btn btn-success btn-agregar-producto-disponibilidad pl-3 pr-3"
                            data-id="<?php echo $producto->id; ?>"
                            data-referencia="<?php echo htmlspecialchars($producto->referencia, ENT_QUOTES, 'UTF-8'); ?>"
                            data-notas="<?php echo htmlspecialchars($producto->notas, ENT_QUOTES, 'UTF-8'); ?>">
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
        $('#contenedor_mensaje_producto_disponibilidad').html('')

        // Inicializar DataTable
        const tabla = $('#tabla_disponibilidad_productos').DataTable({
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
        })

        // Delegación de eventos: botón agregar producto
        $('#tabla_disponibilidad_productos tbody').on('click', '.btn-agregar-producto-disponibilidad', function() {
            const id = $(this).data('id')
            const ref = $(this).data('referencia')
            const notas = $(this).data('notas')

            agregarProductoDisponibilidad(id, ref, notas, this)
        })
    })
</script>
