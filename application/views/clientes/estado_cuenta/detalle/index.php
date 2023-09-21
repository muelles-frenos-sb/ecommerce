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
    cargarProductos = (datos) => {
        datos.tipo = 'facturas_desde_pedido'

        // Se consulta en el API de Siesa el estado de cuenta del cliente
        consulta('obtener', datos, false)
        .then(resultado => {
            if(resultado.codigo && resultado.codigo == 1) {
                mostrarAviso('alerta', 'No se encontraron resultados con el número de pedido. Intenta de nuevo más tarde.', 30000)

                agregarLog(27, JSON.stringify(datos))

                return false
            }

            let datosPedido = {
                tipo: 'clientes_productos',
                valores: resultado.detalle.Table,
            }

            // Se insertan en la base de datos todos los registros obtenidos del cliente
            consulta('crear', datosPedido, false)
            .then(resultado => {
                agregarLog(28, JSON.stringify(datos))

                cargarInterfaz('clientes/estado_cuenta/pedidos/index', 'contenedor_modal', datos)
            })
            .catch(error => {
                agregarLog(29, JSON.stringify(datos))
                mostrarAviso('error', 'Ocurrió un error consultando los productos. Intenta de nuevo más tarde.', 30000)
                return false
            })
        })
    }

    listarFacturas = async() => {
        if($('#estado_cuenta_buscar').val() == '' && localStorage.simonBolivar_buscarFacturaEstadoCuenta) $('#estado_cuenta_buscar').val(localStorage.simonBolivar_buscarFacturaEstadoCuenta)
        
        if(localStorage.simonBolivar_buscarFacturaEstadoCuenta) $('#estado_cuenta_buscar').val(localStorage.simonBolivar_buscarFacturaEstadoCuenta)

        let datos = {
            numero_documento: '<?php echo $datos['numero_documento']; ?>',
            busqueda: $("#estado_cuenta_buscar").val(),
        }

        cargarInterfaz('clientes/estado_cuenta/detalle/lista', 'contenedor_lista_facturas', datos)
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