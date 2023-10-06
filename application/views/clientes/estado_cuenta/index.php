<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Pagar facturas</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--1">
                <form id="formulario_buscar_cliente">
                    <div class="form-group" id="contenedor_numero_documento">
                        <label for="estado_cuenta_numero_documento">Digita tu número de documento o NIT *</label>
                        <input type="number" class="form-control" id="estado_cuenta_numero_documento" placeholder="Sin espacios, guiones ni dígito de verificación" value="900886934" autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" id="btn_estado_cuenta_cliente">Consultar mis facturas</button>

                    <div class="mt-2" id="contenedor_mensaje_carga"></div>
                </form>

                <div id="contenedor_estado_cuenta"></div>
            </div>
        </div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    var numeroDocumento = $('#estado_cuenta_numero_documento')
    
    $().ready(() => {
        $('#formulario_buscar_cliente').submit(evento => {
            evento.preventDefault()

            // Validación de campos obligatorios
            if (!validarCamposObligatorios([numeroDocumento])) return false

            // Se activa el spinner
            $('#btn_estado_cuenta_cliente').addClass('btn-loading').attr('disabled', true)

            agregarLog(22, `Número de documento ${numeroDocumento.val()}`)

            // Mensaje mientras se consultan los datos
            $('#contenedor_mensaje_carga').html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Consultando los datos del cliente...`)
            
            let consultaTercero = consulta('obtener', {tipo: 'terceros', numero_documento: numeroDocumento.val()}, false)
            let consultaEstadoCuentaCliente = consulta('obtener', {tipo: 'estado_cuenta_cliente', numero_documento: numeroDocumento.val()}, false)
            
            Promise.all([consultaTercero, consultaEstadoCuentaCliente])
            .then(async(resultado) => {
                // Obtenemos e insertamos las sucursales por aparte, para recorrer la paginación
                await gestionarSucursales(numeroDocumento.val())

                $('#contenedor_mensaje_carga').html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Preparando la visualización de los datos...`)

                let tercero = resultado[0]
                let facturas = resultado[1]

                // Si no se encuentra el estado de cuenta
                if(facturas.codigo && facturas.codigo == 1) {
                    $('#btn_estado_cuenta_cliente').removeClass('btn-loading').attr('disabled', false)
                    $('#contenedor_mensaje_carga').html('')
                    mostrarAviso('alerta', 'No se encontraron resultados con el número de documento que nos indicas. Por favor, asegúrate de que el número sea correcto o no tenga dígito de verificación.', 30000)
                    agregarLog(23, `Número de documento ${numeroDocumento.val()}`)
                    return false
                }

                let creacionTercero = consulta('crear', {tipo: 'tercero', valores: tercero.detalle.Table}, false)
                let creacionFacturas = consulta('crear', {tipo: 'clientes_facturas', valores: facturas.detalle.Table}, false)
                
                Promise.all([creacionTercero, creacionFacturas])
                .then(async(resultado) => {                    
                    agregarLog(24, `Número de documento ${numeroDocumento.val()}`)
                    
                    // location.href = `${$('#site_url').val()}clientes/estado_cuenta/${numeroDocumento.val()}`
                    cargarInterfaz('clientes/estado_cuenta/facturas/index', 'contenedor_estado_cuenta', {numero_documento: numeroDocumento.val()})

                    $('#btn_estado_cuenta_cliente').hide()
                    numeroDocumento.attr('disabled', true)
                    $('#contenedor_numero_documento').hide()
                })
                .catch(error => {
                    agregarLog(26, `Número de documento ${numeroDocumento.val()}`)
                    mostrarAviso('error', 'Ocurrió un error consultando las facturas del cliente. Intenta de nuevo más tarde.', 30000)
                    $('#contenedor_mensaje_carga').html('')
                    return false
                })
            })
            .catch(error => {
                agregarLog(25, `Número de documento ${numeroDocumento.val()}`)
                mostrarAviso('error', 'Ocurrió un error consultando los datos del cliente. Por favor, intenta más tarde.', 30000)
                return false
            })
        })

        // Control del input para que registren solamente números
        numeroDocumento.keyup(function() {
            $(`#${$(this).attr('id')}`).val(limpiarCadena($(this).val()))
        })
    })
</script>