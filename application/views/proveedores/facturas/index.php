<div class="block-header mt-5" id="contenedor_cabecera_titulo">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Cuentas por pagar</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0" id="formulario_buscar_proveedor">
            <div class="card-body card-body">
                <form class="row">
                    <div class="form-group col-sm-12 col-lg-6">
                        <label for="numero_documento">Digita tu número de documento o NIT *</label>
                        <input type="number" class="form-control" id="numero_documento" placeholder="Sin espacios, guiones ni dígito de verificación" value="" autofocus>
                    </div>

                    <div class="form-group col-sm-12 col-lg-6">
                        <label for="telefono">Digita el número de celular *</label>
                        <input type="number" class="form-control" id="telefono" value="">
                    </div>

                    <div class="form-group col-sm-12 col-lg-12">
                        <button type="submit" class="btn btn-primary btn-block" id="btn_cuentas_por_pagar">Consultar cuentas por pagar</button>

                        <div class="mt-2" id="contenedor_mensaje_carga"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="w-100 p-5">
    <div id="contenedor_cuentas_por_pagar_lista"></div>
</div>

<script>
    let numeroDocumento = $('#numero_documento')
    let numeroTelefono = $('#telefono')

    procesarCuentasPorPagar = (numero) => {
        const opcionesPeticion = {
            method: "GET",
            redirect: "follow"
        }

        $('#contenedor_mensaje_carga').html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Preparando la visualización de los datos...`)

        fetch(`${$("#site_url").val()}webhooks/importar_proveedores_cuentas_por_pagar/${numero}`, opcionesPeticion)
            .then(respuesta => {
                if (!respuesta.ok) {
                    throw new Error("Error en la petición: " + respuesta.status);
                }

                return respuesta.json()
            })
            .then(datos => {
                agregarLog(76, `Número de documento ${numeroDocumento.val()}`)

                cargarInterfaz('proveedores/facturas/lista', 'contenedor_cuentas_por_pagar_lista', {numero_documento: numero})

                $('#btn_cuentas_por_pagar').hide()
                numeroDocumento.attr('disabled', true)
                $('#formulario_buscar_proveedor').remove() 
            })
            .catch(error => {
                agregarLog(77, `Número de documento ${numeroDocumento.val()}`)
                mostrarAviso('error', 'Ocurrió un error consultando las cuentas por pagar. Intenta de nuevo más tarde.', 30000)
                $('#contenedor_mensaje_carga').html('')
                return false
            })
    }

    validarProveedor = async (nit = null) => {
        let datosObligatorios = [
            numeroDocumento,
            numeroTelefono
        ]

        // Validación de campos obligatorios
        if (!validarCamposObligatorios(datosObligatorios)) return false

        let datosTercero = {
            tipo: 'terceros_local',
            nit: numeroDocumento.val(),
            numero: numeroTelefono.val(),
        }

        // Se verifica que el número de teléfono exista en la base de datos
        let tercero = await consulta('obtener', datosTercero)

        // Si no se encontró el tercero
        if (!tercero.resultado) {
            mostrarAviso('alerta', 'El número de teléfono que nos indicas no coincide con el número de documento. Por favor, verifica nuevamente o ponte en contacto con nosotros.', 30000)
            agregarLog(79, JSON.stringify(datosTercero))
            return false
        }

        $('#btn_cuentas_por_pagar').addClass('btn-loading').attr('disabled', true)

        agregarLog(78, `Número de documento ${numeroDocumento.val()}`)

        $('#contenedor_mensaje_carga').html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Consultando los datos del proveedor...`)

        procesarCuentasPorPagar(numeroDocumento.val())
    }

    $().ready(() => {
        $('#formulario_buscar_proveedor').submit(async (evento) => {
            evento.preventDefault()

            validarProveedor()
        })
    })
</script>