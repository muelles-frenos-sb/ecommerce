<div class="pl-3 pr-3 mt-4">
    <div class="row">
        <!-- Sección 1: Filtros -->
        <div class="col-3">
            <div class="card">
                <h2 class="pr-4 pl-4 pt-4">Módulo de ventas</h2>
                <div class="card-divider"></div>

                <div class="card-body card-body--padding--1">
                    <div class="form-row">
                        <div class="col-12 p-2">
                            <label for="cliente_nit">Cliente *</label>
                            <select id="cliente_nit" class="form-control"></select>
                        </div>

                        <div class="col-12 p-2">
                            <label for="cliente_sucursal">Sucursal *</label>
                            <select id="cliente_sucursal" class="form-control"></select>
                        </div>

                        <div class="col-12 p-2">
                            <label for="cliente_bodega">Bodega *</label>
                            <select id="cliente_bodega" class="form-control"></select>
                        </div>

                        <div class="col-12 p-2">
                            <label for="cliente_lista_precio">Lista de precios *</label>
                            <select id="cliente_lista_precio" class="form-control"></select>
                        </div>
                    </div>
                </div>
                <div class="card-divider"></div>

                <div class="card-body card-body--padding--1" id="formulario_buscar_productos">
                    <form class="form-row mb-2">
                        <div class="form-group col-lg-12">
                            <label for="buscar_producto">Buscar por nombre, referencia, marca... *</label>
                            <input type="text" class="form-control" id="buscar_producto">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" id="btn_buscar_producto">Buscar</button>
                    </form>

                    <div class="mt-2" id="contenedor_mensaje_producto"></div>
                </div>
            </div>
        </div>

        <!-- Sección 2: Tablas -->
        <div class="col-9">
            <!-- Resumen del pedido -->
            <div class="card">
                <div class="card-body card-body--padding--1">
                    <div class="tag-badge tag-badge--new badge_formulario badge_formulario_azul">
                        RESUMEN DEL PEDIDO
                    </div>
                    <div id="contenedor_resultado_carrito" style="height: 20vh;"></div>
                </div>
            </div>
            <div class="card-divider"></div>

            <!-- Resultados de búsqueda -->
            <div class="card">
                <div class="card-body card-body--padding--1">
                    <div class="tag-badge tag-badge--new badge_formulario badge_formulario_azul">
                        Búsqueda
                    </div>
                    <div id="contenedor_resultado_productos" style="height: 40vh;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    $().ready(async function() {        
        var buscarProducto = $('#buscar_producto')

        $('#cliente_nit, #cliente_sucursal, #cliente_bodega, #cliente_lista_precio').select2({ width: '100%' })

        cargarInterfaz('clientes/ventas/gestion/carrito', 'contenedor_resultado_carrito')
        
        Swal.fire({
            title: 'Estamos cargando los datos...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        /*************************
         * Carga de los clientes *
         *************************/
        await listarDatos('cliente_nit', {
            tipo: 'terceros_local',
            f200_ind_cliente: true
        })

        Swal.close()

        /*************************
         ** Carga de sucursales **
         *************************/
        $('#cliente_nit').change(async () => {
            Swal.fire({
                title: `Estamos cargando las sucursales de ${$('#cliente_nit option:selected').text()}...`,
                text: 'Por favor, espera.',
                imageUrl: `${$('#base_url').val()}images/cargando.webp`,
                showConfirmButton: false,
                allowOutsideClick: false
            })
          
            // Obtenemos e insertamos las sucursales del tercero
            await gestionarSucursales($('#cliente_nit').val())

            // Cargamos las sucursales del cliente en la lista desplegable
            await listarDatos('cliente_sucursal', {
                tipo: 'clientes_sucursales_local',
                f200_nit: $('#cliente_nit').val()
            })

            Swal.close()
        })

        /**************************
         **** Carga de bodegas ****
         *************************/
        await listarDatos('cliente_bodega', {
            tipo: 'erp_bodegas',
        })

        /******************************
         * Carga de listas de precios *
         *****************************/
        await listarDatos('cliente_lista_precio', {
            tipo: 'erp_listas_precios',
        })

        $('#formulario_buscar_productos').submit(async evento => {
            evento.preventDefault()

            let datosObligatorios = [
                buscarProducto,
                // $('#cliente_nit'),
                // $('#cliente_sucursal'),
                // $('#cliente_bodega'),
                // $('#cliente_lista_precio'),
            ]

            // Validación de campos obligatorios
            if (!validarCamposObligatorios(datosObligatorios)) return false

            // Se activa el spinner
            $('#btn_buscar_producto').addClass('btn-loading').attr('disabled', true)

            // agregarLog(58, buscarProducto.val())

            // Mensaje mientras se consultan los datos
            $('#contenedor_mensaje_producto').html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Buscando coincidencias con ${buscarProducto.val()}...`)

            $('#btn_buscar_producto').removeClass('btn-loading').attr('disabled', false)

            let datos = {
                tipo: 'productos',
                busqueda: $('#buscar_producto').val(),
                filtro_bodega: $('#cliente_bodega option:selected').attr('data-codigo') || '00550',
                filtro_lista_precio: $('#cliente_lista_precio').val() || '003',
                mostrar_agotados: true,
            }

            // Se carga la lista de terceros encontrados
            cargarInterfaz('clientes/ventas/gestion/productos', 'contenedor_resultado_productos', datos)
        })
    })
</script>
