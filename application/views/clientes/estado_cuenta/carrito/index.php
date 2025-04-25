<div class="address-card__row" id="mensaje_inicial">
    <div class="alert alert-primary">
        Selecciona una o varias facturas a pagar, haciendo clic en el ícono <i class="fa fa-plus"></i>
    </div>
</div>

<div class="vehicles-list__body mt-2" id="contenedor_lista_carrito"></div>

<div class="mt-2 mb-2 d-flex justify-content-end">
    <input type="hidden" id="total_pago">
    
    <!-- Total a pagar: $<span id="total_pago_formato">0</span> -->

    <div class="container mt-5">
        <h2 class="text-center mb-4">Resumen del pago</h2>
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <td>Subtotal</td>
                    <td>$<span id="subtotal_formato">0</span></td>
                </tr>
                <tr>
                    <td>Descuento</td>
                    <td>$<span id="total_descuento_formato">0</span></td>
                </tr>
                <tr class="table-success">
                    <td><strong>Total</strong></td>
                    <td>$<span id="total_pago_formato">0</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12">
        <!-- Si trae NIT de comprobante, es la sección para vendedores -->
        <?php if($datos['nit_comprobante']) { ?>
            <div class="mt-2 mb-2 d-flex flex-column">
                <!-- <input type="hidden" id="total_faltante_amortizacion" value=""> -->
                <h4 class="align-self-end">Valor de facturas seleccionadas: $<span id="valor_total_seleccionadas">0</span></h4>
                <h4 class="align-self-end">Valor faltante: $<span id="comprobante_valor_faltante">0</span></h4>
            </div>

            <button class="btn btn-primary btn-lg btn-block" onClick="javascript:guardarReciboEstadoCuenta()">Guardar pago con comprobante</button>
        <?php } else { ?>
            <button class="btn btn-primary btn-lg btn-block" id="btn_pago_en_linea">Realizar pago en línea</button>
        <?php } ?>

        <!-- <div class="col-6">
            <button class="btn btn-primary btn-sm btn-block" onClick="javascript:guardarReciboEstadoCuenta()">Subir comprobantes</button>
        </div> -->
        
        <center>
            <img src="<?php echo base_url(); ?>images/banners/opciones_pago.png" class="img-fluid"alt="Opciones de pago">
        </center>
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
                consulta('obtener', {tipo: 'cliente_factura_movimiento', f200_nit: datos.numero_documento, f350_consec_docto: datos.documento_cruce}, false)
                .then(resultadoMovimiento => {
                    // Se obtiene el valor bruto de ese movimiento, para calcular descuentos posteriormente
                    valorBruto = (resultadoMovimiento.f351_valor_cr) ? resultadoMovimiento.f351_valor_cr : 0

                    Swal.close()

                    // Se oculta la celda
                    $(`#factura_${datos.contador}`).hide()

                    // Se oculta el mensaje inicial
                    $('#mensaje_inicial').hide()

                    // Si el valor es negativo, no debe dejarse editar
                    let desactivado = (datos.valor < 0) ? 'disabled' : ''

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
                                    <div class='col-4'>
                                        <label>Valor a pagar</label>
                                        <input 
                                            type="text"
                                            id="${datos.id}"
                                            data-id="${datos.id}"
                                            data-documento_cruce_numero="${datos.documento_cruce}"
                                            data-numero_cuota="${datos.numero_cuota}"
                                            data-documento_cruce_tipo="${datos.documento_cruce_tipo}"
                                            data-descuento_porcentaje="${datos.descuento_porcentaje}"
                                            data-valor_bruto="${valorBruto}"
                                            class="form-control valor_pago_factura"
                                            style="text-align: right"
                                            max="${parseFloat(datos.valor)}"
                                            value="${parseFloat(datos.valor)}"
                                            ${desactivado}
                                        >
                                    </div>

                                    <div class='col-4'>
                                        <label>Descuento</label>
                                        <input type='text' id="descuento_${datos.id}" class="form-control" placeholder='Descuento' style="text-align: right" disabled>
                                    </div>
                                    
                                    <div class='col-4'>
                                        <label>Valor final</label>
                                        <input type='text' id="valor_completo_${datos.id}" class="form-control" style="text-align: right" disabled>
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
                    $(`.valor_pago_factura`).on('keyup', function() {
                        // Se formatea el campo
                        $(this).val(formatearNumero($(this).val()))

                        calcularTotal()
                    })

                    mostrarAviso('exito', '!Bien! En la parte inferior podrás ver tus facturas seleccionadas para pago', 10000)
                })
            })
        })
    }

    calcularTotal = () => {
        var subtotal = 0
        var totalDescuento = 0
        var total = 0
        var detalleFactura = []
        var montoComprobante = ($("#monto").val()) ? parseFloat($("#monto").val().replace(/\./g, '')) : 0 // Monto especificado cuando se sube un comprobante

        $(`.valor_pago_factura`).each(function() {
            let valorAPagar = parseFloat($(this).val().replace(/\./g, ''))
            let valorBruto = parseFloat($(this).attr('data-valor_bruto'))
            let valorTotal = parseFloat($(this).attr('max'))
            let porcentajeDescuento = parseFloat($(this).attr('data-descuento_porcentaje'))
            let valorDescuento = (valorAPagar == valorTotal) ? Math.floor(valorBruto * (porcentajeDescuento / 100)) : 0
            
            subtotal += valorAPagar
            totalDescuento += valorDescuento
            total += parseFloat($(this).val().replace(/\./g, ''))
            total -= valorDescuento

            $(`#descuento_${$(this).attr('data-id')}`).val(formatearNumero(valorDescuento))
            $(`#valor_completo_${$(this).attr('data-id')}`).val(formatearNumero((valorAPagar - valorDescuento)))

            detalleFactura.push({
                documento_cruce_numero: $(this).attr('data-documento_cruce_numero'),
                cuota_numero: $(this).attr('data-numero_cuota'),
                documento_cruce_tipo: $(this).attr('data-documento_cruce_tipo'),
                subtotal: valorAPagar,
                descuento: valorDescuento
            })
        })

        // Se formatea el campo
        $('#subtotal_formato').text(formatearNumero(subtotal))
        $('#total_descuento_formato').text(formatearNumero(totalDescuento))
        $('#total_pago_formato').text(formatearNumero(total))
        $('#total_pago').val(total)

        // Sección para subida de comprobante
        $('#valor_total_seleccionadas').text(formatearNumero(total))

        // Valor que falta para poder subir el comprobante
        let valorFaltanteComprobante = montoComprobante - total
        $('#comprobante_valor_faltante').text(formatearNumero(valorFaltanteComprobante))

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

        // Si es un pago en línea y el monto no supera lo indicado
        if(pagarEnLinea && total < 10000) {
            mostrarAviso('alerta', 'Lamentamos informarte que si deseas pagar por este medio, el valor debe ser superior o igual a $10.000', 20000)
            return false
        }

        let datosRecibo = {
            tipo: 'recibos',
            abreviatura: 'ec',
            recibo_tipo_id: (pagarEnLinea) ? 2 : 3,
            recibo_estado_id: (!pagarEnLinea) ? 3 : null,
            razon_social: $('#factura_tercero_razon_social').val(),
            documento_numero: $('#factura_tercero_documento_numero').val(),
            usuario_creacion_id: '<?php echo $this->session->userdata('usuario_id'); ?>',
            email: localStorage.simonBolivar_emailContacto,
            valor: total,
        }

        // Si no es un pago en línea, se validan campos obligatorios
        if(!pagarEnLinea) {
            let camposObligatorios = [
                $('#fecha_consignacion'),
                $('#monto'),
                $('#cuenta'),
            ]

            if (!validarCamposObligatorios(camposObligatorios)) return false

            // Si no es pago en línea y no tiene archivos
            if(archivos.length == 0) {
                mostrarAviso('alerta', 'Por favor selecciona los comprobantes de pago que vas a subir')
                return false
            }

            // Si el total es diferente al monto
            if(total !== parseFloat($('#monto').val().replace(/\./g, ''))) {
                mostrarAviso('alerta', 'El monto indicado no es igual al valor pagado de las facturas.')
                return false
            }

            let confirmacion = await confirmar('Guardar', `¿Validaste que toda la información está correcta?`)
            if (!confirmacion) return false

            // Se agregan los datos del comprobante al recibo
            datosRecibo.fecha_consignacion = $('#fecha_consignacion').val()
            datosRecibo.cuenta_bancaria_id = $('#cuenta').val()
            datosRecibo.archivo_soporte = `1.${archivos[0].name.split('.').pop()}`
        }

        let recibo = await consulta('crear', datosRecibo, false)
        
        // Una vez creado el recibo
        if (recibo.resultado) {
            // Se crean los ítems del recibo
            let reciboItems = await consulta('crear', {
                tipo: 'recibos_detalle_estado_cuenta',
                recibo_id: recibo.resultado,
                items: calcularTotal()
            }, false)

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

    $().ready(() => {
        $('#btn_pago_en_linea').on('click', e => {
            // Se activa el spinner
            $('#btn_pago_en_linea').addClass('btn-loading').attr('disabled', true)

            guardarReciboEstadoCuenta(true)
            
            // Se desactiva el spinner después de cierto tiempo
            setTimeout(() => $('#btn_pago_en_linea').removeClass('btn-loading').attr('disabled', false), 1000)
        })

        $(`#monto`).on('keyup', function() {
            // Se formatea el campo
            $(this).val(formatearNumero($(this).val()))

            calcularTotal()
        })
    })
</script>