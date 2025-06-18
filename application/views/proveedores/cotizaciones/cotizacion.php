<?php 
$datos = [
    'cotizacion_id' => $cotizacion_id,
    'nit' => $nit
];

$detalle = $this->proveedores_model->obtener('proveedores_cotizaciones_solicitudes_detalle', $datos);
?>

<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="row" id="contenedor_cotizacion_detalle">
                    <div class="table-responsive mt-3 p-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Cantidad</th>
                                    <th scope="col">Precio</th>
                                </tr>
                            </thead>
                            <tbody id="listado_cotizacion_detalle">
                                <?php foreach ($detalle as $registro) { ?>
                                    <tr id="listado_producto_<?php echo $registro->id; ?>"
                                        data-cotizacion-detalle-id="<?php echo $registro->id; ?>"
                                    />
                                        <td><?php echo $registro->producto; ?></td>
                                        <td><?php echo $registro->cantidad; ?></td>
                                        <td>
                                            <input type="number" class="form-control" id="precio_<?php echo $registro->id; ?>">
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <button class="btn btn-success w-100" onclick="javascript:guardarCotizacionProductos()">Guardar</button>
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

        let datos = {
            tipo: 'proveedores_cotizaciones_solicitudes_detalle',
            id: null,
            cotizacion_detalle: cotizacionProductos
        }

        await consulta('actualizar', datos)
    }

    obtenerCotizacionProductos = () => {
        let cotizacionProductos = []

        $("#listado_cotizacion_detalle tr").each(function () {
            let id = $(this).data("cotizacion-detalle-id")

            let datos = {
                id: id,
                precio: $(`#precio_${id}`).val()
            }

            cotizacionProductos.push(datos)
        })

        return cotizacionProductos
    }
</script>