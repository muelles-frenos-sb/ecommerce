<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Módulo de ventas</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
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
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    $().ready(async function() {
        var buscarProducto = $('#buscar_producto')
        
        Swal.fire({
            title: 'Estamos cargando los datos...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        await listarDatos('cliente_nit', {
            tipo: 'terceros_local',
            f200_ind_cliente: true
        })

        Swal.close()


        $('#cliente_nit').select2({
            width: '100%'
        })

        $('#cliente_nit').change(async () => {
            Swal.fire({
                title: `Estamos cargando las sucursales de ${$('#cliente_nit option:selected').text()}...`,
                text: 'Por favor, espera.',
                imageUrl: `${$('#base_url').val()}images/cargando.webp`,
                showConfirmButton: false,
                allowOutsideClick: false
            })
          
            // await gestionarSucursales($('#cliente_nit').val())

            // await listarDatos('cliente_sucursal', {
            //     tipo: 'clientes_sucursales',
            //     f200_nit: $('#cliente_nit').val()
            // })
            // Swal.close()
        })

        $('#formulario_buscar_productos').submit(async evento => {
            evento.preventDefault()

            let datosObligatorios = [ buscarProducto ]

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
                filtro_bodega: $('#cliente_bodega').val() || '00550',
                lista_precio: $('#cliente_lista_precio').val() || '003',
                mostrar_agotados: true,
            }

            let productos = await consulta('obtener', datosBusqueda)
            console.log(productos)

            $('#btn_buscar_producto').removeClass('btn-loading').attr('disabled', false)

            // Se carga la lista de terceros encontrados
            cargarInterfaz('clientes/ventas/gestion/productos', 'contenedor_resultado_productos', productos)
        })
    })
</script>
