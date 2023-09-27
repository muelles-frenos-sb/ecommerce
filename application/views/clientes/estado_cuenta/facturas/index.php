<!-- <div class="card-divider"></div> -->
<div class="card-table">
    <form class="form-group" id="formulario_buscar_factura">
        <div class="row">
            <div class="col-lg-10 col-sm-12">
                <input type="text" class="form-control" id="estado_cuenta_buscar" placeholder="Buscar una factura por número, placa, fecha, valor, etc.">
            </div>
            <div class="col-lg-2 col-sm-12">
                <button type="submit" class="btn btn-primary btn-block">Buscar</button>
            </div>
        </div>
    </form>
    
    <div id="contenedor_lista_facturas"></div>
</div>

<div class="card-divider"></div>

<div id="contenedor_modal"></div>

<script>
    cargarProductos = async(datos) => {
        // Se consulta en el API de Siesa el detalle de la factura (detalle de productos)
        datos.tipo = 'facturas_desde_pedido'
        let productosFactura = await consulta('obtener', datos, false)

        Promise.all([productosFactura])
        .then(async() => {
            if(productosFactura.codigo && productosFactura.codigo == 1) {
                mostrarAviso('alerta', 'No se encontraron resultados con el número de pedido. Intenta de nuevo más tarde.', 30000)
                agregarLog(27, JSON.stringify(datos))
                return false
            }

            // Se insertan en la base de datos todos los registros obtenidos del cliente
            await consulta('crear', {tipo: 'clientes_facturas_detalle', valores: productosFactura.detalle.Table}, false)

            agregarLog(28, JSON.stringify(datos))

            cargarInterfaz('clientes/estado_cuenta/productos/index', 'contenedor_modal', datos)
        })
        .catch(error => {
            agregarLog(29, JSON.stringify(datos))
            mostrarAviso('error', 'Ocurrió un error consultando los productos. Intenta de nuevo más tarde.', 30000)
            return false
        })
    }

    cargarMovimientos = async(datos) => {
        datos.tipo = 'movimientos_contables'
        let movimientosFactura = await consulta('obtener', datos, false)

        Promise.all([movimientosFactura])
        .then(async() => {
            if(movimientosFactura.codigo && movimientosFactura.codigo == 1) {
                mostrarAviso('alerta', 'No se encontraron movimientos con el número de pedido. Intenta de nuevo más tarde.', 30000)
                agregarLog(32, JSON.stringify(datos))
                return false
            }
        })
        .catch(error => {
            agregarLog(31, JSON.stringify(datos))
            mostrarAviso('error', 'Ocurrió un error consultando los movimientos de la factura. Intenta de nuevo más tarde.', 30000)
            return false
        })

        // Se insertan en la base de datos todos los movimientos obtenidos de la factura
        await consulta('crear', {tipo: 'clientes_facturas_movimientos', valores: movimientosFactura.detalle.Table}, false)

        agregarLog(30, JSON.stringify(datos))

        cargarInterfaz('clientes/estado_cuenta/movimientos/index', 'contenedor_modal', datos)
    }

    listarFacturas = async() => {
        if($('#estado_cuenta_buscar').val() == '' && localStorage.simonBolivar_buscarFacturaEstadoCuenta) $('#estado_cuenta_buscar').val(localStorage.simonBolivar_buscarFacturaEstadoCuenta)
        
        if(localStorage.simonBolivar_buscarFacturaEstadoCuenta) $('#estado_cuenta_buscar').val(localStorage.simonBolivar_buscarFacturaEstadoCuenta)

        let datos = {
            numero_documento: '<?php echo $datos['numero_documento']; ?>',
            busqueda: $("#estado_cuenta_buscar").val(),
        }

        cargarInterfaz('clientes/estado_cuenta/facturas/lista', 'contenedor_lista_facturas', datos)
    }

    $().ready(() => {
        listarFacturas()

        $('#formulario_buscar_factura').submit(evento => {
            evento.preventDefault()

            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_buscarFacturaEstadoCuenta = $('#estado_cuenta_buscar').val()

            listarFacturas()
        })
    })
</script>