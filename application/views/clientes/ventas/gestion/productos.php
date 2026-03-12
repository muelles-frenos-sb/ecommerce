<style>
    #tabla_productos_encontrados tbody * {
        font-size: 0.9em;
        padding: 3px;
    }
</style>

<?php $productos = $this->productos_model->obtener('productos', $datos); ?>

<div class="table-responsive">
    <table class="table-bordered" id="tabla_productos_encontrados">
        <thead>
            <tr>
                <th class="text-center">Id</th>
                <th class="text-center">Referencia</th>
                <th class="text-center">Notas</th>
                <th class="text-center">Bodega</th>
                <th class="text-center">Stock</th>
                <th class="text-center d-none">Costo</th>
                <th class="text-center">Margen</th>
                <th class="text-center">Lista</th>
                <th class="text-center">Precio unitario</th>
                <th class="text-center"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($productos as $producto) {
                $precios_disponibles = $this->productos_model->obtener('producto_listas_precio_disponibles', ['producto_id' => $producto->id]);
                $inventario_disponible = $this->productos_model->obtener('producto_bodegas_disponibles', ['producto_id' => $producto->id]);
            ?>
                <tr>
                    <td id="producto_id"><?php echo $producto->id; ?></td>

                    <!-- Referencia -->
                    <td class="text-left"><?php echo $producto->referencia; ?></td>

                    <!-- Notas -->
                    <td><?php echo $producto->notas; ?></td>

                    <!-- Bodega -->
                    <td class="text-center" width="120">
                        <!-- Se cargan las bodegas donde el producto tiene disponibilidad -->
                        <select id="<?php echo "producto_{$producto->id}_bodega"; ?>" data-id="<?php echo $producto->id; ?>" onChange="javascript:actualizarDisponibilidad(<?php echo $producto->id; ?>)" class="form-control">
                            <?php
                            foreach($inventario_disponible as $bodega) {
                                echo "<option value='$bodega->codigo' data-disponibilidad='$bodega->disponible' data-costo='$bodega->costo_promedio_unitario'>$bodega->codigo ($bodega->disponible)</option>";
                            }
                            ?>
                        </select>
                    </td>

                    <!-- Stock -->
                    <td class="text-center" id="<?php echo "producto_{$producto->id}_disponibilidad"; ?>">-</td>

                    <!-- Costo -->
                    <td class="text-center d-none" id="<?php echo "producto_{$producto->id}_costo"; ?>">-</td>

                    <!-- Margen -->
                    <td class="text-center" id="<?php echo "producto_{$producto->id}_margen"; ?>">-</td>
                    
                    <!-- Lista de precio -->
                    <td class="text-center" width="120">
                        <!-- Se cargan las listas donde el producto tiene disponibilidad -->
                        <select id="<?php echo "producto_{$producto->id}_lista_precio"; ?>" data-id="<?php echo $producto->id; ?>" onChange="javascript:actualizarPrecioUnitario(<?php echo $producto->id; ?>)" class="form-control lista_precio">
                            <?php
                            foreach($precios_disponibles as $lista) {
                                echo "<option value='$lista->lista_precio' data-precio='$lista->precio'>$lista->lista_precio (".formato_precio($lista->precio).")</option>";
                            }

                            // Si hay inventario disponible, se habilita la lista personalizada
                            if(!empty($inventario_disponible)) echo "<option value='005' data-precio='0'>005 (PERSONALIZADA)</option>";
                            ?>
                        </select>
                    </td>

                    <!-- Precio unitario -->
                    <td class="text-center" id="<?php echo "producto_{$producto->id}_precio"; ?>" data-precio_unitario="<?php echo $producto->precio; ?>">-</td>

                    <!-- Agregar -->
                    <td class="text-center">
                        <button
                            type="button"
                            id="<?php echo "producto_{$producto->id}_boton"; ?>"
                            onClick="javascript:seleccionarItem({
                                producto_id: <?php echo $producto->id ?>,
                                referencia: '<?php echo $producto->referencia; ?>',
                                unidad_inventario: '<?php echo $producto->unidad_inventario; ?>',
                                precio_personalizado: true,
                            })"
                            class="btn btn-success pl-3 pr-3">
                            +
                        </button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
    actualizarDisponibilidad = productoId => {
        let disponibilidad = $(`#producto_${productoId}_bodega option:selected`).data('disponibilidad')

        $(`#producto_${productoId}_disponibilidad`).text(disponibilidad)
    }

    actualizarPrecioUnitario = productoId => {
        let precio = $(`#producto_${productoId}_lista_precio option:selected`).data('precio')
        
        $(`#producto_${productoId}_precio`).text(`$${formatearNumero(precio)}`)
        $(`#producto_${productoId}_precio`).data('precio_unitario', precio)
    }

    actualizarSubtotal = productoId => {
        let subtotal = $(`#producto_${productoId}_subtotal option:selected`).attr('data-subtotal')
        
        $(`#producto_${productoId}_subtotal`).text(`$${formatearNumero(subtotal)}`)
        $(`#producto_${productoId}_subtotal`).data('subtotal', subtotal)
    }

    calcularMargenProducto = productoId => {
        let costo = $(`#producto_${productoId}_bodega option:selected`).data('costo')
        let precio = $(`#producto_${productoId}_lista_precio option:selected`).data('precio')
        var margen = '-'

        if(costo != '' && precio != '') {
            // Margen = ( precio - costo ) / precio  * 100
            margen = (( precio - costo ) / precio * 100 ).toFixed(1) + '%'
        }

        costo = (costo != '') ? `$${formatearNumero(parseInt(costo))}` : '-'

        $(`#producto_${productoId}_costo`).text(costo)
        $(`#producto_${productoId}_margen`).text(margen)
    }

    seleccionarItem = datos => {
        let bodegaSeleccionada = $(`#producto_${datos.producto_id}_bodega option:selected`).val()
        let listaPrecioSeleccionada = $(`#producto_${datos.producto_id}_lista_precio option:selected`).val()

        if(!bodegaSeleccionada || !listaPrecioSeleccionada) return

        let precio = $(`#producto_${datos.producto_id}_precio`).data('precio_unitario')

        agregarProducto({
            id: datos.producto_id,
            precio: parseFloat(precio),
            referencia: datos.referencia,
            unidad_inventario: datos.unidad_inventario,
            unidad_inventario: datos.unidad_inventario,
            lista_precio: listaPrecioSeleccionada,
            bodega: bodegaSeleccionada,
        })

        cargarInterfaz('clientes/ventas/gestion/carrito', 'contenedor_resultado_carrito')
    }

    $().ready(() => {
        var bodegaPorDefecto = $('#cliente_bodega option:selected').data('codigo')

        // Si se cambia la bodega o la lista de precios
        $(`select[id^='producto_']`).on('change', function() {
            let productoId = $(this).data('id')
            
            calcularMargenProducto(productoId)
        })

        $('#contenedor_mensaje_producto').html(``)

        new DataTable('#tabla_productos_encontrados', {
            deferRender: true,
            fixedHeader: true,
            info: false,
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            createdRow: function(row, data, dataIndex) {
                let productoId = $(row).find('#producto_id').text()
                let bodegaProducto = $(row).find(`#producto_${productoId}_bodega`).val()
                let existeBodegaEnProducto = $(`#producto_${productoId}_bodega`).find(`option[value="${bodegaPorDefecto}"]`).val()

                // SI el producto está en la bodega por defecto, selecciona la bodega
                if(existeBodegaEnProducto) $(`#producto_${productoId}_bodega`).val(bodegaPorDefecto)

                let listaPrecio = $(row).find(`#producto_${productoId}_lista_precio`).val()
                let cantidad = $(row).find(`#producto_${productoId}_cantidad`).val()
                let boton = $(row).find(`#producto_${productoId}_boton`)

                if(!bodegaProducto || !listaPrecio) {
                    boton.hide()
                    return  
                }
                
                actualizarDisponibilidad(productoId)
                actualizarPrecioUnitario(productoId)
                actualizarSubtotal(productoId)
                calcularMargenProducto(productoId)
            },
            ordering: true,
            orderCellsTop: true,
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
    })
</script>