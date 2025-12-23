<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Módulo de ventas</h1>
        </div>
    </div>
</div>

<div class="pl-5 pr-5">
    <div class="card mb-lg-0">
        <div class="card-body card-body--padding--1">
            <div class="form-row">
                <div class="col-lg-3 col-sm-12">
                    <label for="cliente_nit">Cliente *</label>
                    <select id="cliente_nit" class="form-control"></select>
                </div>

                <div class="col-lg-3 col-sm-12">
                    <label for="cliente_sucursal">Sucursal *</label>
                    <select id="cliente_sucursal" class="form-control"></select>
                </div>

                <div class="col-lg-3 col-sm-12">
                    <label for="cliente_bodega">Bodega *</label>
                    <select id="cliente_bodega" class="form-control"></select>
                </div>

                <div class="col-lg-3 col-sm-12">
                    <label for="cliente_lista_precio">Lista de precios *</label>
                    <select id="cliente_lista_precio" class="form-control"></select>
                </div>
            </div>
        </div>
        <div class="card-divider"></div>

        <div class="card-body card-body--padding--1" id="formulario_buscar_productos">
            <form class="form-row mb-2">
                <div class="form-group col-lg-12">
                    <label for="buscar_producto">Producto *</label>
                    <input type="text" class="form-control" id="buscar_producto" placeholder="Buscar por nombre, referencia, marca...">
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="btn_buscar_producto">Buscar producto</button>
            </form>

            <div id="contenedor_resultado_productos">
                <div class="mt-2" id="contenedor_mensaje_producto"></div>
            </div>
        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    $().ready(async function() {        
        var buscarProducto = $('#buscar_producto')

        $('#cliente_nit, #cliente_sucursal').select2({
            width: '100%'
        })
        
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

            let datosBusqueda = {
                tipo: 'productos',
                busqueda: buscarProducto.val(),
                filtro_bodega: $('#cliente_bodega option:selected').attr('data-codigo').padStart(5, '0') || '00550',
                filtro_lista_precio: $('#cliente_lista_precio').val().padStart(3, '0') ?? '003',
                mostrar_agotados: true,
            }
            console.log(datosBusqueda)

            let productos = await consulta('obtener', datosBusqueda)

            $('#btn_buscar_producto').removeClass('btn-loading').attr('disabled', false)

            // Se carga la lista de terceros encontrados
            cargarInterfaz('clientes/ventas/gestion/productos', 'contenedor_resultado_productos', JSON.stringify(productos))
        })
    })
</script>
