<?php $tercero = $this->clientes_model->obtener('tercero', ['f200_nit' => $datos['numero_documento']]); ?>

<input type="hidden" id="factura_tercero_razon_social" value="<?php echo $tercero->f200_razon_social; ?>">
<input type="hidden" id="factura_tercero_documento_numero" value="<?php echo $tercero->f200_nit; ?>">
<input type="hidden" id="total_pago">

<!-- Campo oculto para saber el id del registro pasado a la lista de facturas seleccionados -->
<input type="hidden" id="id_registro">

<!-- Modal que se usa para abrir la interfaz de pago de Wompi -->
<div id="contenedor_pago_estado_cuenta"></div>
<div id="contenedor_modal"></div>

<!-- Facturas pendientes -->
<div class="card flex-grow-1">
    <div class="card-body card-body--padding--2">
        <!-- Si es pago con comprobante, se muestran los datos del comprobante -->
        <?php if($datos['nit_comprobante']) { ?>
            <div id="contenedor_cabecera_cliente"></div>

            <div class="form-row mb-4 border border-saecondary p-3">
                <div class="form-group col-md-2">
                    <label for="fecha_consignacion">Fecha de consignación *</label>
                    <input type="date" class="form-control" id="fecha_consignacion" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group col-md-2">
                    <label for="monto">Monto *</label>
                    <input type='text' id="monto" class="form-control" placeholder='Valor pagado' style="text-align: right">
                </div>

                <div class="form-group col-md-3">
                    <label for="cuenta">Cuenta</label>
                    <select id="cuenta" class="form-control">
                        <option value="">Seleccione...</option>
                        <?php foreach($this->configuracion_model->obtener('cuentas_bancarias') as $cuenta) echo "<option value='$cuenta->id' data-codigo='$cuenta->codigo'>$cuenta->numero - $cuenta->nombre</option>"; ?>
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <label for="referencia">Referencia (Opcional)</label>
                    <input type='text' id="referencia" class="form-control" placeholder='Número de referencia'>
                </div>

                <div class="form-group col-md-3">
                    <label for="estado_cuenta_archivos">Comprobante digital</label>
                    <input type="file" class="form-control" aria-label="Subir" id="estado_cuenta_archivos" multiple>
                </div>
            </div>
        <?php } ?>

        <div class="tag-badge tag-badge--theme badge_formulario mb-1 mt-1">Facturas pendientes de pago</div>
        <div class="card-table">
            <div id="contenedor_lista_facturas_pendientes"></div>
        </div>
    </div>
</div>

<!-- Facturas a pagar -->
<div class="card flex-grow-1 mt-3">
    <div class="card-body card-body--padding--2">
        <div class="tag-badge tag-badge--new badge_formulario mb-1 mt-1">Facturas seleccionadas para pago</div>
        <div class="card-table">
            <div id="contenedor_lista_facturas_seleccionadas"></div>
        </div>
    </div>
</div>

<div class="row p-4">
    <div class="col-12">
        <!-- Si trae NIT de comprobante, es la sección para vendedores -->
        <?php if($datos['nit_comprobante']) { ?>
            <div class="d-flex flex-column">
                <!-- <input type="hidden" id="total_faltante_amortizacion" value=""> -->
                <h4 class="align-self-end">Valor de facturas seleccionadas: $<span id="valor_total_seleccionadas">0</span></h4>
                <h4 class="align-self-end">Diferencia: $<span id="comprobante_valor_faltante">0</span></h4>
            </div>

            <button class="btn btn-primary btn-lg btn-block" onClick="javascript:guardarReciboEstadoCuenta()">Guardar pago con comprobante</button>
        <?php } else { ?>
            <button class="btn btn-primary btn-lg btn-block" id="btn_pago_en_linea">Realizar pago en línea</button>
        <?php } ?>

        <button type="button" class="btn btn-secondary btn-sm btn-block" onClick="javascript:history.back()">Consultar otro cliente</button>

        <center>
            <img src="<?php echo base_url(); ?>images/banners/opciones_pago.png" class="img-fluid"alt="Opciones de pago">
        </center>
    </div>
</div>

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

            cargarInterfaz('clientes/estado_cuenta/facturas/productos', 'contenedor_modal', datos)
        })
        .catch(error => {
            agregarLog(29, JSON.stringify(datos))
            mostrarAviso('error', 'Ocurrió un error consultando los productos. Intenta de nuevo más tarde.', 30000)
            return false
        })
    }

    cargarMovimientos = async(datos, abrirModal = true) => {
        datos.tipo = 'movimientos_contables'
        let movimientosFactura = await consulta('obtener', datos, false)

        Promise.all([movimientosFactura])
        .then(async() => {
            if(movimientosFactura.codigo && movimientosFactura.codigo == 1) {
                mostrarAviso('alerta', 'No se encontraron movimientos con el número de pedido.', 30000)
                agregarLog(32, JSON.stringify(datos))
                return false
            }

            // Se insertan en la base de datos todos los movimientos obtenidos de la factura
            await consulta('crear', {tipo: 'clientes_facturas_movimientos', valores: movimientosFactura.detalle.Table}, false)

            agregarLog(30, JSON.stringify(datos))

            // Si se necesita cargar la interfaz
            if(abrirModal) cargarInterfaz('clientes/estado_cuenta/facturas/movimientos', 'contenedor_modal', datos)
        })
        .catch(error => {
            agregarLog(31, JSON.stringify(datos))
            mostrarAviso('error', 'Ocurrió un error consultando los movimientos de la factura. Intenta de nuevo más tarde.', 30000)
            return false
        })
    }

    cargarFacturasPedientes = async() => {
        cargarInterfaz('clientes/estado_cuenta/facturas/lista_pendientes', 'contenedor_lista_facturas_pendientes', {
            numero_documento: '<?php echo $datos['numero_documento']; ?>',
        })
    }

    cargarFacturasSeleccionadas = async() => {
        cargarInterfaz('clientes/estado_cuenta/facturas/lista_seleccionadas', 'contenedor_lista_facturas_seleccionadas', {
            numero_documento: '<?php echo $datos['numero_documento']; ?>',
        })
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

            let diferencia = total - parseFloat($('#monto').val().replace(/\./g, ''))
            let diferenciaPositiva = (diferencia < 0) ? diferencia * -1 : diferencia    // La diferencia siempre positiva

            // Si el total es diferente al monto descrito
            if(diferencia !== 0) {
                // Texto para el tipo de diferencia
                let mensajeDiferencia = (diferencia > 0) ? 'es menor que' : 'supera'

                var tipoDiferencia
                if(diferenciaPositiva > 10000) tipoDiferencia = 'saldo a favor'     // Más de 10.000 pesos
                if(diferenciaPositiva <= 10000) tipoDiferencia = 'aprovechamientos' // Hasta 10.000 pesos
                if(diferenciaPositiva <= 1000) tipoDiferencia = 'ajuste al peso'    // Hasta 1.000 pesos

                // Mensaje de advertencia
                let confirmacionDiferencia = await confirmar('De acuerdo', `⚠️ El valor consignado <b>${mensajeDiferencia}</b> el total de facturas en <b>$${formatearNumero(diferenciaPositiva)}</b>. Este valor se registrará como <b>${tipoDiferencia}</b>.`)
                if (!confirmacionDiferencia) return false

                // Se agrega el valor de diferencia en un campo aparte
                datosRecibo.valor_pagado_mayor = diferencia
            }

            // Se agregan los datos del comprobante al recibo
            datosRecibo.fecha_consignacion = $('#fecha_consignacion').val()
            datosRecibo.cuenta_bancaria_id = $('#cuenta').val()
            datosRecibo.referencia = $('#referencia').val()
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

    $().ready(() => {
        cargarFacturasPedientes()
        cargarFacturasSeleccionadas()

        // Datos del cliente para mostrar al inicio de la interfaz
        let datosCliente = JSON.parse('<?php echo json_encode($tercero) ?>')
        cargarInterfaz('clientes/estado_cuenta/facturas/detalle_cliente', 'contenedor_cabecera_titulo', datosCliente)
    })
</script>