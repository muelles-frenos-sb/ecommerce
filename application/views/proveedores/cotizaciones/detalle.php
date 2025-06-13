<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <div class="form-group col-6 col-sm-8">
                        <label for="cotizacion_producto">Producto *</label>
                        <select id="cotizacion_producto" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach ($this->proveedores_model->obtener("productos") as $producto) echo "<option value='$producto->id'>$producto->valor</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-6 col-md-4">
                        <label for="cotizacion_cantidad">Cantidad *</label>
                        <input type="number" class="form-control" id="cotizacion_cantidad">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" onclick="javascript:agregarCotizacionProducto()">Agregar</button>
                    </div>
                </div>

                <div class="row d-none" id="contenedor_cotizacion_detalle">
                    <div class="table-responsive mt-3 p-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Cantidad</th>
                                    <th scope="col">Opciones</th>
                                </tr>
                            </thead>
                            <tbody id="listado_cotizacion_detalle"></tbody>
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
    agregarCotizacionProducto = () => {
        let camposObligatorios = [
            $('#cotizacion_producto'),
            $('#cotizacion_cantidad'),
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let $contenedor = $("#contenedor_cotizacion_detalle")
        let $listado = $("#listado_cotizacion_detalle")
        let productoId = $("#cotizacion_producto").val()
        let producto = $("#cotizacion_producto option:selected").text()
        let cantidad = $("#cotizacion_cantidad").val()

        $contenedor.removeClass("d-none")

        let productosAgregados = obtenerCotizacionProductos()
        let productoAgregado = productosAgregados.filter((registro) => registro.producto_id == productoId)

        if (productoAgregado.length > 0) {
            mostrarAviso('alerta', `¡El producto seleccionado ya fue agregado!`, 20000)
            return
        }

        $listado.append(`
            <tr id="listado_producto_${productoId}"
                data-producto-id="${productoId}"
                data-cantidad="${cantidad}"
            />
                <td>${producto}</td>
                <td>${cantidad}</td>
                <td>
                    <a type="button" class="btn btn-sm btn-danger" href="javascript:quitarCotizacionProducto(${productoId})">
                        Quitar
                    </a>
                </td>
            </tr>
        `)
    }

    guardarCotizacionProductos = async() => {
        let cotizacionProductos = obtenerCotizacionProductos()

        let datos = {
            tipo: 'proveedores_cotizaciones_solicitudes',
            cotizacion_detalle: cotizacionProductos
        }

        await consulta('crear', datos)
    }

    obtenerCotizacionProductos = () => {
        let cotizacionProductos = []

        $("#listado_cotizacion_detalle tr").each(function () {
            let datos = {
                producto_id: $(this).data("producto-id"),
                cantidad: $(this).data("cantidad"),
                cotizacion_id: null
            }

            cotizacionProductos.push(datos)
        })

        return cotizacionProductos
    }

    quitarCotizacionProducto = (productoId) => {
        let $contenedor = $("#contenedor_cotizacion_detalle")
        let $registro = $(`#listado_producto_${productoId}`)
        let numero = $("#listado_cotizacion_detalle tr").length

        if (numero === 0) $contenedor.addClass("d-none")

        $registro.remove()
    }

    $().ready(() => {
        $('#cotizacion_producto').select2()
    })
</script>