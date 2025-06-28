<?php 
$datos = [
    'cotizacion_id' => $cotizacion_id,
    'nit' => $nit
];

$solicitud_detalle = $this->proveedores_model->obtener('proveedores_maestro_solicitudes_detalle', $datos);
$cotizacion_detalle = $this->proveedores_model->obtener('proveedores_cotizaciones_detalle', $datos);

echo "<input type='hidden' id='cotizacion_id' value='$cotizacion_id'>";
echo "<input type='hidden' id='proveedor_nit' value='$nit'>";
?>

<div class="block">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Productos solicitados</h1>

            
        </div>

        <div class="alert alert-primary alert-lg mb-3 alert-dismissible fade show">
            Indícanos el precio que ofreces al lado de cada producto que tengas disponible. <!-- <a href="">Esta es la lista de marcas</a> -->
        </div>

        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="row" id="contenedor_cotizacion_detalle">
                    <div class="table-responsive p-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Cantidad</th>
                                    <th scope="col">Precio</th>
                                </tr>
                            </thead>
                            <tbody id="listado_cotizacion_detalle">
                                <?php 
                                foreach ($solicitud_detalle as $registro) {
                                    if ($cotizacion_detalle) {
                                        $index = array_search($registro->producto_id, array_column($cotizacion_detalle, 'producto_id'));
                                        if (gettype($index) === "integer") $cotizacion_detalle_id = $cotizacion_detalle[$index]->id;
                                    }
                                ?>
                                    <tr id="listado_producto_<?php echo $registro->id; ?>"
                                        data-producto-id="<?php echo $registro->producto_id; ?>"
                                        data-solicitud-detalle-id="<?php echo $registro->id; ?>"
                                        <?php if (isset($cotizacion_detalle_id)) echo " data-cotizacion-detalle-id='$cotizacion_detalle_id' "?>
                                    />
                                        <td><?php echo $registro->producto; ?></td>
                                        <td><?php echo $registro->cantidad; ?></td>
                                        <td>
                                            <input type="number" class="form-control" id="precio_<?php echo $registro->id; ?>" value="<?php if (isset($cotizacion_detalle_id)) echo $cotizacion_detalle[$index]->precio; ?>">
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <button class="btn btn-success btn-lg w-100" onclick="javascript:guardarCotizacionProductos()">Enviar cotización</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    guardarCotizacionProductos = async() => {
        let cotizacionProductos = obtenerCotizacionProductos()

        let actualizar = cotizacionProductos.some(item => item.hasOwnProperty('id'))

        let datos = {
            tipo: 'proveedores_cotizaciones_detalle',
            id: null,
            registros: cotizacionProductos
        }

        let datosLog = {
            id: '<?php echo $cotizacion_id; ?>',
            nit: '<?php echo $nit; ?>',
        }

        if (actualizar) {
            agregarLog(64, JSON.stringify(datosLog))

            await consulta('actualizar', datos)
        } else {
            agregarLog(63, JSON.stringify(datosLog))

            await consulta('crear', datos)
            location.reload()
        }
    }

    obtenerCotizacionProductos = () => {
        let cotizacionProductos = []

        $("#listado_cotizacion_detalle tr").each(function () {
            let id = $(this).data("cotizacion-detalle-id")
            let solicitudDetalleId = $(this).data("solicitud-detalle-id")

            let datos = {
                cotizacion_id: $("#cotizacion_id").val(),
                proveedor_nit: $("#proveedor_nit").val(),
                precio: parseInt($(`#precio_${solicitudDetalleId}`).val()),
                producto_id: $(this).data("producto-id"),
            }

            if (id) datos.id = id

            cotizacionProductos.push(datos)
        })

        return cotizacionProductos
    }
</script>