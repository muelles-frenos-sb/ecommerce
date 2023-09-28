<div class="vehicles-list">
    <div class="address-card__row mt-2 mb-2" id="mensaje_inicial">
        <div class="alert alert-primary mb-3">
            Aquí van a aparecer las facturas que selecciones para pagar. Haz clic en el ícono <i class="fa fa-plus"></i> para seleccionarla
        </div>
    </div>

    <div class="vehicles-list__body mt2" id="contenedor_lista_carrito"></div>
    Total a pagar: <span id="total_pago">0</span>

    <button type="submit" class="btn btn-success btn-sm btn-block mt-2" onClick="javascript:guardarFacturaEstadoCuenta()">Pagar facturas</button>
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
                    <input type="number" class="form-control valor_pago_factura" style="text-align: right" max="${datos.valor}" value="${datos.valor}">
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
        $(`.valor_pago_factura`).each(function() {
            total += parseFloat($(this).val())
        })

        $('#total_pago').text(total)
    }

    guardarFacturaEstadoCuenta = async() => {
        let total = parseFloat($('#total_pago').text())

        if(total == 0) {
            mostrarAviso('alerta', 'No hay ninguna factura seleccionada para pagar')
            return false
        }

        let datosFactura = {
            tipo: 'facturas',
            abreviatura: 'ec',
            tipo_id: 2,
            razon_social: $('#factura_tercero_razon_social').val(),
            documento_numero: $('#factura_tercero_documento_numero').val(),
            // direccion: $('#checkout_direccion').val(),
            // email: $('#checkout_email').val(),
            // telefono: $('#checkout_telefono').val(),
            // comentarios: $('#checkout_comentarios').val(),
            valor: total,
        }

        let factura = await consulta('crear', datosFactura, false)
        
        // Una vez creada la factura
        if (factura.resultado) {
            // Se crean los ítems de la factura
            // let facturaItems = await consulta('crear', {tipo: 'facturas_detalle_estado_cuenta', 'factura_id': factura.resultado}, false)

            // if (facturaItems.resultado) 
            cargarInterfaz('clientes/estado_cuenta/carrito/pago', 'contenedor_pago_estado_cuenta', {id: factura.resultado})
        }
    }

    $().ready(() => {
        $(`input[type='numer']`).keyup(() => {
            calcularTotal()
        })
    })
</script>