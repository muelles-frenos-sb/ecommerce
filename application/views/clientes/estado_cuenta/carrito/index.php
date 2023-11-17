<div class="address-card__row mt-2 mb-2" id="mensaje_inicial">
    <div class="alert alert-primary mb-3">
        Selecciona una o varias facturas a pagar, haciendo clic en el ícono <i class="fa fa-plus"></i>
    </div>
</div>

<div class="vehicles-list__body mt-2" id="contenedor_lista_carrito"></div>

<div class="input-group mt-2">
    <input type="file" class="form-control" aria-label="Subir" id="estado_cuenta_archivos" multiple>
</div>

<div class="mt-2 mb-2 d-flex justify-content-end">
    <input type="hidden" id="total_pago">
    Total a pagar: $<span id="total_pago_formato">0</span>
</div>

<div class="row mt-2">
    <div class="col-6">
        <button class="btn btn-primary btn-sm btn-block" onClick="javascript:guardarReciboEstadoCuenta(true)">Pagar en línea</button>
    </div>
    <div class="col-6">
        <button class="btn btn-primary btn-sm btn-block" onClick="javascript:guardarReciboEstadoCuenta()">Subir comprobantes</button>
    </div>
</div>

<script>
    var detalleFactura = [];

    agregarFactura = datos => {
        // Se oculta la celda
        $(`#factura_${datos.contador}`).hide()

        // Se oculta el mensaje inicial
        $('#mensaje_inicial').hide()

        // Se agrega el ítem
        $('#contenedor_lista_carrito').append(`
            <label id="id_registro_${datos.contador}" class="vehicles-list__item">
                <span class="vehicles-list__item-radio input-radio">
                    <span class="input-radio__body">
                        <input class="input-radio__input" name="check_factura_${datos.contador}" type="radio" checked>
                        <span class="input-radio__circle"></span>
                    </span>
                </span>
                <span class="vehicles-list__item-info">
                    <span class="vehicles-list__item-name">Factura ${datos.documento_cruce}</span>
                    <span class="vehicles-list__item-details">SEDE ${datos.sede} - ${datos.tipo_credito}</span>
                </span>
                <span class="vehicles-list__item-info">
                    <input 
                        type="text"
                        id="${datos.id}"
                        data-id="${datos.id}"
                        data-documento_cruce_numero="${datos.documento_cruce}"
                        data-documento_cruce_tipo="${datos.documento_cruce_tipo}"
                        class="form-control valor_pago_factura"
                        style="text-align: right"
                        max="${datos.valor}"
                        value="${datos.valor}"
                    >
                </span>

                <button type="button" class="vehicles-list__item-remove" onClick="removerFactura(${datos.contador})">
                    <svg width="16" height="16">
                        <path d="M2,4V2h3V1h6v1h3v2H2z M13,13c0,1.1-0.9,2-2,2H5c-1.1,0-2-0.9-2-2V5h10V13z" />
                    </svg>
                </button>
            </label>
        `)

        // Por defecto se formatea el campo
        $(`#${datos.id}`).val(formatearNumero(datos.valor))

        // Si el número cambia
        $(`input`).on('keyup', function() {
            // Se formatea el campo
            $(this).val(formatearNumero($(this).val()))

            calcularTotal()
        })

        calcularTotal()
    }

    calcularTotal = () => {
        var total = 0
        var detalleFactura = []

        $(`.valor_pago_factura`).each(function() {
            total += parseFloat($(this).val().replace(/\./g, ''))

            detalleFactura.push({
                documento_cruce_numero: $(this).attr('data-documento_cruce_numero'),
                documento_cruce_tipo: $(this).attr('data-documento_cruce_tipo'),
                subtotal: $(this).val().replace(/\./g, '')
            })
        })

        // Se formatea el campo
        $('#total_pago_formato').text(formatearNumero(total))
        $('#total_pago').val(total)

        return detalleFactura
    }

    guardarReciboEstadoCuenta = async(pagarEnLinea  = false) => {
        let total = parseFloat($('#total_pago').val())
        var archivos = $('#estado_cuenta_archivos').prop('files')

        if(total == 0 || isNaN(total)) {
            mostrarAviso('alerta', 'No hay ninguna factura seleccionada para pagar. Selecciona una o varias facturas para continuar el proceso.')
            return false
        }

        if(total < 0) {
            mostrarAviso('alerta', 'El valor del pago debe ser mayor a cero.')
            return false
        }

        // Si no es pago en línea y no tiene archivos
        if(!pagarEnLinea && archivos.length == 0) {
            mostrarAviso('alerta', 'Por favor selecciona los comprobantes de pago que vas a subir')
            return false
        }

        let datosRecibo = {
            tipo: 'recibos',
            abreviatura: 'ec',
            recibo_tipo_id: (pagarEnLinea) ? 2 : 3,
            recibo_estado_id: (!pagarEnLinea) ? 3 : null,
            razon_social: $('#factura_tercero_razon_social').val(),
            documento_numero: $('#factura_tercero_documento_numero').val(),
            // direccion: $('#checkout_direccion').val(),
            // email: $('#checkout_email').val(),
            // telefono: $('#checkout_telefono').val(),
            // comentarios: $('#checkout_comentarios').val(),
            valor: total,
        }

        let recibo = await consulta('crear', datosRecibo, false)
        
        // Una vez creado el recibo
        if (recibo.resultado) {
            // Se crean los ítems del recibo
            let reciboItems = await consulta('crear', {tipo: 'recibos_detalle_estado_cuenta', 'recibo_id': recibo.resultado, items: calcularTotal()}, false)

            if (reciboItems.resultado) {
                // Si es pago en línea, redirecciona a Wompi
                if(pagarEnLinea) cargarInterfaz('clientes/estado_cuenta/carrito/pago', 'contenedor_pago_estado_cuenta', {id: recibo.resultado})

                // Si es para subir comprobante
                if(!pagarEnLinea) {
                    var contadorArchivos = 0

                    // Se recorren los archivos
                    $.each(archivos, (key, archivo) => {
                        contadorArchivos++
                        
                        let nombre = archivo.name.split('.')[0]
                        let extension = archivo.name.split('.').pop()
                        let tamanio = archivo.size / 1000
                        let nombreArchivo = `${contadorArchivos}.${extension}`

                        let anexo = new FormData()
                        anexo.append('name', archivo, nombreArchivo)

                        let peticion = new XMLHttpRequest()
                        peticion.open('POST', `${$('#site_url').val()}/interfaces/subir_comprobante/${recibo.resultado}`)
                        peticion.send(anexo)
                        peticion.onload = evento => {
                            let respuesta = JSON.parse(evento.target.responseText)

                            consulta('actualizar', {
                                tipo: 'recibos',
                                id: recibo.resultado,
                                archivos: contadorArchivos
                            }, false)
                        }
                    })

                    mostrarAviso('exito', 'Comprobantes subidos exitosamente')

                    // Se muestra el mensaje inicial
                    $('#mensaje_inicial').show()

                    vaciarCarritoEstadoCuenta()
                }
            }
        }
    }

    removerFactura = async(id) => {
        // El registro en el mini carrito se quita
        $(`#id_registro_${id}`).remove()

        // Se vuelve a agregar en la tabla
        $(`#factura_${id}`).show()

        calcularTotal()
    }

    vaciarCarritoEstadoCuenta = () => {
        $('#contenedor_lista_carrito').html('')
        $('#estado_cuenta_archivos').val('')
        calcularTotal()
    }
</script>