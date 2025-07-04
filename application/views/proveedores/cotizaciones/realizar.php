<?php 
$datos = [
    'cotizacion_id' => $cotizacion_id,
    'nit' => $nit
];

// Se obtienen los datos de la solicitud de cotización
$solicitud_detalle = $this->proveedores_model->obtener('proveedores_maestro_solicitudes_detalle', $datos);

// Se obtiene el detalle de la cotización, en caso de que el proveedor ya la haya hecho
$cotizacion_detalle = $this->proveedores_model->obtener('proveedores_cotizaciones_detalle', $datos);

echo "<input type='hidden' id='cotizacion_id' value='$cotizacion_id'>";
echo "<input type='hidden' id='proveedor_nit' value='$nit'>";
if(!empty($cotizacion_detalle)) echo "<input type='hidden' id='cotizacion_detalle' value='1'>";
?>

<div class="block">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Productos requeridos en la solicitud de cotización</h1>
        </div>

        <div class="card mb-lg-0">
            <?php
            if(empty($solicitud_detalle)) { ?>
                <div class='container'>
                    <div class='alert alert-danger alert-lg mb-3 alert-dismissible fade show'>
                        Para la solicitud de cotización que elegiste, no hay productos disponibles de las marcas que distribuyes.
                    </div>
                </div>
            <?php } ?>

            <?php if(!empty($solicitud_detalle)) { ?>
                <div class="card-body card-body--padding--2">
                    <div class="alert alert-info alert-lg mb-3 alert-dismissible fade show">
                        Indícanos el precio que ofreces al lado de cada producto que tengas disponible. <!-- <a href="">Esta es la lista de marcas</a> -->
                    </div>

                    <div class="row" id="contenedor_cotizacion_detalle">
                        <div class="table-responsive p-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">Id</th>
                                        <th scope="col" class="text-center">Marca</th>
                                        <th scope="col" class="text-center">Referencia</th>
                                        <th scope="col" class="text-center">Descripción</th>
                                        <th scope="col" class="text-center">Cantidad</th>
                                        <th scope="col" class="text-center">Precio</th>
                                        <th scope="col" class="text-center">Observación</th>
                                    </tr>
                                </thead>
                                <tbody id="listado_cotizacion_detalle">
                                    <?php 
                                    foreach ($solicitud_detalle as $registro) {
                                            $index = array_search($registro->producto_id, array_column($cotizacion_detalle, "producto_id"));
                                            
                                            $precio_item = ($index) ? $cotizacion_detalle[$index]->precio : 0;
                                            $observacion_item = ($index) ? $cotizacion_detalle[$index]->observacion : '';
                                            $cotizacion_detalle_id = ($index) ? $cotizacion_detalle[$index]->id : 0;

                                    ?>
                                        <tr id="listado_producto_<?php echo $registro->id; ?>"
                                            data-producto-id="<?php echo $registro->producto_id; ?>"
                                            data-solicitud-detalle-id="<?php echo $registro->id; ?>"
                                            <?php if (!isset($cotizacion_detalle)) echo " data-cotizacion-detalle-id='$cotizacion_detalle_id' "?>
                                        />
                                            <td><?php echo $registro->producto_id; ?></td>
                                            <td><?php echo $registro->producto_marca; ?></td>
                                            <td><?php echo $registro->producto_referencia; ?></td>
                                            <td><?php echo $registro->producto_notas; ?></td>
                                            <td class="text-center"><?php echo $registro->cantidad; ?></td>
                                            <td width="15%">
                                                <input type="text" class="form-control text-right" id="precio_<?php echo $registro->id; ?>" value="<?php echo $precio_item; ?>">
                                            </td>
                                            <td width="15%">
                                                <input type="text" class="form-control" id="observacion_<?php echo $registro->id; ?>" value="<?php echo $observacion_item; ?>">
                                            </td>
                                        </tr>

                                        <script>
                                            // Por defecto se formatea el campo
                                            $(`#precio_<?php echo $registro->id; ?>`).val(formatearNumero(<?php echo $precio_item; ?>))
                                        </script>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <button class="btn btn-success btn-lg w-100" onClick="javascript:guardarCotizacionProductos()">Enviar cotización</button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    guardarCotizacionProductos = async() => {
        let cotizacionProductos = await obtenerCotizacionProductos()

        let actualizar = ($('#cotizacion_detalle').val()) ? true : false

        var datos = {
            tipo: 'proveedores_cotizaciones_detalle',
            cotizacion_id: <?php echo $cotizacion_id; ?>,
            proveedor_nit: <?php echo $nit; ?>,
            registros: cotizacionProductos
        }

        let datosLog = {
            id: '<?php echo $cotizacion_id; ?>',
            nit: '<?php echo $nit; ?>',
        }

        if (actualizar) {
            agregarLog(64, JSON.stringify(datos))
            let resultado = await consulta('crear', datos)
        } else {
            agregarLog(63, JSON.stringify(datos))
            let resultado = await consulta('crear', datos)
            location.reload()
        }
    }

    obtenerCotizacionProductos = () => {
        var cotizacionProductos = []

        $("#listado_cotizacion_detalle tr").each(function () {
            var id = $(this).data("cotizacion-detalle-id")
            var solicitudDetalleId = $(this).data("solicitud-detalle-id")
            var observacion = $(`#observacion_${solicitudDetalleId}`).val()

            var datos = {
                cotizacion_id: $("#cotizacion_id").val(),
                proveedor_nit: $("#proveedor_nit").val(),
                precio: parseFloat($(`#precio_${solicitudDetalleId}`).val().replace(/\./g, '')),
                producto_id: $(this).data("producto-id"),
                observacion: observacion,
            }

            cotizacionProductos.push(datos)
        })

        return cotizacionProductos
    }

    $().ready(() => {
        // Si el precio cambia
        $(`input[id^='precio_']`).on('keyup', function() {
            // Se formatea el campo
            $(this).val(formatearNumero($(this).val()))
        })
    })
</script>