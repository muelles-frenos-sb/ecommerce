<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Consultar estado de cuenta</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--1">
                <form id="formulario_buscar_cliente">
                    <div class="form-group">
                        <label for="estado_cuenta_numero_documento">Digita tu número de documento o NIT *</label>
                        <input type="number" class="form-control" id="estado_cuenta_numero_documento" placeholder="Sin espacios, guiones ni dígito de verificación" value="15258814" autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" id="btn_estado_cuenta_cliente">Consultar mi estado de cuenta</button>
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

            let camposObligatorios = [
                numeroDocumento,
            ]
            
            if (!validarCamposObligatorios(camposObligatorios)) return false

            // Se activa el spinner
            $('#btn_estado_cuenta_cliente').addClass('btn-loading').attr('disabled', true)
            
            let datosCliente = {
                tipo: 'estado_cuenta_cliente',
                numero_documento: numeroDocumento.val(),
            }

            agregarLog(22, `Número de documento ${numeroDocumento.val()}`)

            // Se consulta en el API de Siesa el estado de cuenta del cliente
            consulta('obtener', datosCliente, false)
            .then(resultado => {
                $('#btn_estado_cuenta_cliente').removeClass('btn-loading').attr('disabled', false)
                
                if(resultado.codigo && resultado.codigo == 1) {
                    mostrarAviso('alerta', 'No se encontraron resultados con el número de documento que nos indicas. Por favor, asegúrate de que el número sea correcto o no tenga dígito de verificación.', 30000)

                    agregarLog(23, `Número de documento ${numeroDocumento.val()}`)

                    return false
                }

                let datosFacturas = {
                    tipo: 'clientes_facturas',
                    valores: resultado.detalle.Table,
                }
                
                // Se insertan en la base de datos todos los registros obtenidos del cliente
                consulta('crear', datosFacturas, false)
                .then(resultado => {
                    agregarLog(24, `Número de documento ${numeroDocumento.val()}`)

                    cargarInterfaz('clientes/estado_cuenta/detalle/index', 'contenedor_estado_cuenta', {numero_documento: numeroDocumento.val()})
                })
                .catch(error => {
                    agregarLog(26, `Número de documento ${numeroDocumento.val()}`)
                    mostrarAviso('error', 'Ocurrió un error consultando las facturas del cliente. Intenta de nuevo más tarde.', 30000)
                    return false
                })

                $('#btn_estado_cuenta_cliente').hide()
                numeroDocumento.attr('disabled', true)
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