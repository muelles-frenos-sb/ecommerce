<style>
    #tabla_productos_disponibilidad_seleccionados tbody * {
        font-size: 0.9em;
        padding: 5px;
        vertical-align: middle;
    }
</style>

<?php $productos_disponibilidad = $this->logistica_model->obtener('productos_inventario_disponibilidad', ['bodega' => $datos['bodega']]); ?>

<?php if (empty($productos_disponibilidad)) { ?>
    <div class="text-center p-4 text-muted">
        <i class="fa fa-info-circle"></i> No hay productos parametrizados para esta bodega. Usa la búsqueda para agregarlos.
    </div>
<?php } else { ?>
    <div class="table-responsive">
        <table class="table-striped table-bordered" id="tabla_productos_disponibilidad_seleccionados">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Referencia</th>
                    <th class="text-center">Descripción</th>
                    <th class="text-center">Porcentaje</th>
                    <th class="text-center">Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($productos_disponibilidad as $item) { ?>
                    <tr>
                        <td class="text-center"><?php echo $item->producto_id; ?></td>
                        <td class="text-left"><?php echo $item->referencia; ?></td>
                        <td class="text-left"><?php echo $item->notas; ?></td>
                        <td class="text-center">
                            <input type="number" class="form-control form-control-sm" style="min-width:80px;" value="<?php echo $item->porcentaje; ?>" min="0" max="100" step="0.1" onchange="javascript:actualizarPorcentajeProductoDisponibilidad(<?php echo $item->id; ?>, this)">
                        </td>
                        <td class="text-center">
                            <button
                                type="button"
                                onclick="javascript:eliminarProductoDisponibilidad(<?php echo $item->id; ?>)"
                                class="btn btn-sm btn-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<script>
    eliminarProductoDisponibilidad = async (itemId) => {
        let confirmado = await confirmar('eliminar', '¿Está seguro de eliminar este producto de la parametrización de disponibilidad?')
        if (!confirmado) return

        await consulta('eliminar', {
            tipo: 'productos_inventario_disponibilidad',
            id: itemId
        }, false)

        mostrarAviso('exito', 'Producto eliminado de la parametrización de disponibilidad')
        cargarProductosDisponibilidadSeleccionados()
    }

    actualizarPorcentajeProductoDisponibilidad = async (itemId, campo) => {
        let fila = $(campo).closest('tr')
        let porcentaje = parseFloat(fila.find('input[type=number]').val())

        if (isNaN(porcentaje) || porcentaje < 0) {
            mostrarAviso('alerta', 'Ingresa un porcentaje válido para el producto.')
            return
        }
        if (porcentaje > 100) porcentaje = 100

        await consulta('actualizar', {
            tipo: 'productos_inventario_disponibilidad',
            id: itemId,
            porcentaje: porcentaje
        }, false)
    }
</script>
