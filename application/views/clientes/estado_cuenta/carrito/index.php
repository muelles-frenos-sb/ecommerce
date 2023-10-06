<div class="form-group">
    <label for="estado_cuenta_tipo_pago">¿Vas a pagar en línea o vas a subir el comprobante?</label>
    <select id="estado_cuenta_tipo_pago" class="form-control form-control-select2">
        <option value="1" selected>Pagar en línea</option>
        <option value="2">Subir comprobante</option>
    </select>
</div>

<div class="address-card__row mt-2 mb-2" id="mensaje_inicial">
    <div class="alert alert-primary mb-3">
        Selecciona una o varias facturas a pagar, haciendo clic en el ícono <i class="fa fa-plus"></i>
    </div>
</div>

<div class="vehicles-list__body mt-2" id="contenedor_lista_carrito"></div>

<div class="mt-2">
    Total a pagar: <span id="total_pago">0</span>
</div>

<div class="input-group mt-2 d-none" id="contenedor_tipo_pago_comprobante">
    <input type="file" class="form-control" aria-label="Subir" id="estado_cuenta_archivo">
    <button class="btn btn-success"  onClick="javascript:guardarReciboEstadoCuenta()">Subir comprobante</button>
</div>

<div class="row mt-2 d-none" id="contenedor_tipo_pago_wompi">
    <div class="col-12">
        <button type="submit" class="btn btn-success btn-sm btn-block mt-2" onClick="javascript:guardarReciboEstadoCuenta(true)">Pagar en línea</button>
    </div>
</div>

<script>
    var detalleFactura = []

    agregarFactura = datos => {
        // Se oculta la celda
        $(`#factura_${datos.contador}`).hide()

        // Se oculta el mensaje inicial
        $('#mensaje_inicial').hide()

        // Se agrega el ítem
        $('#contenedor_lista_carrito').append(`
            <label class="vehicles-list__item">
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
                        type="number"
                        data-id="${datos.id}"
                        data-documento_cruce_numero="${datos.documento_cruce}"
                        data-documento_cruce_tipo="${datos.documento_cruce_tipo}"
                        class="form-control valor_pago_factura"
                        style="text-align: right"
                        max="${datos.valor}"
                        value="${datos.valor}"
                        onChange="javascript:calcularTotal()"
                    >
                </span>

                <button type="button" class="vehicles-list__item-remove">
                    <svg width="16" height="16">
                        <path d="M2,4V2h3V1h6v1h3v2H2z M13,13c0,1.1-0.9,2-2,2H5c-1.1,0-2-0.9-2-2V5h10V13z" />
                    </svg>
                </button>
            </label>
        `)

        calcularTotal()
    }

    calcularTotal = () => {
        var total = 0
        var detalleFactura = []

        $(`.valor_pago_factura`).each(function() {
            total += parseFloat($(this).val())

            detalleFactura.push({
                documento_cruce_numero: $(this).attr('data-documento_cruce_numero'),
                documento_cruce_tipo: $(this).attr('data-documento_cruce_tipo'),
                subtotal: $(this).val()
            })
        })

        $('#total_pago').text(total)

        return detalleFactura
    }

    guardarReciboEstadoCuenta = async(pagarEnLinea  = false) => {
        let total = parseFloat($('#total_pago').text())
        var archivo = $('#estado_cuenta_archivo').prop('files')[0]

        if(total == 0) {
            mostrarAviso('alerta', 'No hay ninguna factura seleccionada para pagar. Selecciona una o varias facturas para continuar el proceso.')
            return false
        }

        // Si no es pago en línea y no tiene archivo
        if(!pagarEnLinea && !archivo) {
            mostrarAviso('alerta', 'Por favor selecciona el comprobante de pago que vas a adjuntar al pago')
            return false
        }

        let datosrecibo = {
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

        let recibo = await consulta('crear', datosrecibo, false)
        
        // Una vez creado el recibo
        if (recibo.resultado) {
            // Se crean los ítems del recibo
            let reciboItems = await consulta('crear', {tipo: 'recibos_detalle_estado_cuenta', 'recibo_id': recibo.resultado, items: calcularTotal()}, false)

            // if (reciboItems.resultado) {
                // Si es pago en línea, redirecciona a Wompi
                if(pagarEnLinea) cargarInterfaz('clientes/estado_cuenta/carrito/pago', 'contenedor_pago_estado_cuenta', {id: recibo.resultado})

                // Si es para subir comprobante
                if(!pagarEnLinea) {
                    let nombre = archivo.name.split('.')[0]
                    let extension = archivo.name.split('.').pop()
                    let tamanio = archivo.size / 1000
                    let nombreArchivo = `${recibo.resultado}.${extension}`
                    
                    let anexo = new FormData()
                    anexo.append('name', archivo, nombreArchivo)
                    
                    let peticion = new XMLHttpRequest()
                    peticion.open('POST', $('#site_url').val() + '/interfaces/subir_comprobante')
                    peticion.send(anexo)
                    peticion.onload = evento => {
                        let respuesta = JSON.parse(evento.target.responseText)
                        consulta('actualizar', {
                            tipo: 'recibos',
                            id: recibo.resultado,
                            nombre_archivo: nombreArchivo
                        })
                    }
                }
            // }
        }
    }

    $().ready(() => {
        $(`input[type='number']`).keyup(() => {
            calcularTotal()
        })

        $(`#contenedor_tipo_pago_wompi`).removeClass('d-none')

        $('#estado_cuenta_tipo_pago').change(function() {
            $(`#contenedor_tipo_pago_wompi, #contenedor_tipo_pago_comprobante`).addClass('d-none')
            
            if($(this).val() == '1') $(`#contenedor_tipo_pago_wompi`).removeClass('d-none')
            if($(this).val() == '2') $(`#contenedor_tipo_pago_comprobante`).removeClass('d-none')
        })
    })
</script>