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
        Swal.fire({
            title: 'Cargando información de la factura...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        let datosConsulta = {
            tipo: 'movimientos_contables',
            documento_cruce: datos.documento_cruce,
            numero_documento: datos.numero_documento,
            id_sucursal: datos.id_sucursal,
        }

        // Se cargan los movimientos de la factura
        consulta('obtener', datosConsulta, false)
        .then(movimientosFactura => {
            // Se insertan en la base de datos todos los movimientos obtenidos de la factura
            consulta('crear', {tipo: 'clientes_facturas_movimientos', valores: movimientosFactura.detalle.Table}, false)
            .then(() => {
                // Se obtiene de los movimientos creados el valor bruto de ingreso ventas 19%
                consulta('obtener', {tipo: 'cliente_factura_movimiento', f350_consec_docto: datos.documento_cruce, f253_id: '41359310'}, false)
                .then(resultadoMovimiento => {
                    // Se obtiene el valor bruto de ese movimiento, para calcular descuentos posteriormente
                    valorBruto = (resultadoMovimiento) ? resultadoMovimiento.f351_valor_cr : 0

                    Swal.close()

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
                                <div class='row'>
                                    <div class='col-12'>
                                        <label>Valor a pagar</label>
                                        <input 
                                            type="text"
                                            id="${datos.id}"
                                            data-id="${datos.id}"
                                            data-documento_cruce_numero="${datos.documento_cruce}"
                                            data-documento_cruce_tipo="${datos.documento_cruce_tipo}"
                                            data-descuento_porcentaje="${datos.descuento_porcentaje}"
                                            data-valor_bruto="${valorBruto}"
                                            class="form-control valor_pago_factura"
                                            style="text-align: right"
                                            max="${datos.valor}"
                                            value="${datos.valor}"
                                        >
                                    </div>

                                    <div class='col-4 d-none'>
                                        <label>Descuento</label>
                                        <input type='text' id="descuento_${datos.id}" class="form-control" placeholder='Descuento' disabled>
                                    </div>
                                    
                                    <div class='col-4 d-none'>
                                        <label>Valor final</label>
                                        <input type='text' id="valor_completo_${datos.id}" class="form-control" disabled>
                                    </div>
                                </div>
                            </span>
                                
                            <button type="button" class="vehicles-list__item-remove" onClick="removerFactura(${datos.contador})">
                                <svg width="16" height="16">
                                    <path d="M2,4V2h3V1h6v1h3v2H2z M13,13c0,1.1-0.9,2-2,2H5c-1.1,0-2-0.9-2-2V5h10V13z" />
                                </svg>
                            </button>
                        </label>
                    `)
                    
                    calcularTotal()

                    // Por defecto se formatea el campo
                    $(`#${datos.id}`).val(formatearNumero(datos.valor))

                    // Si el número cambia
                    $(`input`).on('keyup', function() {
                        // Se formatea el campo
                        $(this).val(formatearNumero($(this).val()))

                        calcularTotal()
                    })
                })
            })
        })
    }

    calcularTotal = () => {
        var total = 0
        var detalleFactura = []

        $(`.valor_pago_factura`).each(function() {
            let valorAPagar = parseFloat($(this).val().replace(/\./g, ''))
            let valorBruto = parseFloat($(this).attr('data-valor_bruto'))
            let valorTotal = parseFloat($(this).attr('max'))
            // let porcentajeDescuento = parseFloat($(this).attr('data-descuento_porcentaje'))
            let porcentajeDescuento = 0
            
            let valorDescuento = (valorAPagar == valorTotal) ? valorBruto * (porcentajeDescuento / 100) : 0
            
            total += parseFloat($(this).val().replace(/\./g, ''))
            total -= valorDescuento.toFixed(0)

            $(`#descuento_${$(this).attr('data-id')}`).val(formatearNumero(valorDescuento.toFixed(0)))
            $(`#valor_completo_${$(this).attr('data-id')}`).val(formatearNumero((valorAPagar - valorDescuento).toFixed(0)))

            detalleFactura.push({
                documento_cruce_numero: $(this).attr('data-documento_cruce_numero'),
                documento_cruce_tipo: $(this).attr('data-documento_cruce_tipo'),
                subtotal: valorAPagar,
                descuento: valorDescuento
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