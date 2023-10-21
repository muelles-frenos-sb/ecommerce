<?php $recibo = $this->productos_model->obtener('recibo', ['token' => $this->uri->segment(4)]); ?>

<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container container--max--xl">
        <input type="hidden" id="total_recibo" value="<?php echo $recibo->valor; ?>">

        <div class="row">
            <div class="col-12 col-lg-3 d-flex">
                <div class="account-nav flex-grow-1">
                    <h4 class="account-nav__title">Opciones</h4>
                    <ul class="account-nav__list">
                        <!-- Resumen -->
                        <li class="recibos_items account-nav__item">
                            <a onClick="cargarInterfaz('configuracion/recibos/detalle/resumen', 'contenedor_recibos_detalle', {token: '<?php echo $this->uri->segment(4); ?>'})">Resumen</a>
                        </li>

                        <!-- Pago -->
                        <?php if($recibo->wompi_datos) { ?>
                            <li class="recibos_wompi account-nav__item">
                                <a onClick="cargarInterfaz('configuracion/recibos/detalle/wompi', 'contenedor_recibos_detalle', {token: '<?php echo $this->uri->segment(4); ?>'})">Pago</a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-lg-9 mt-4 mt-lg-0">
                <div id="contenedor_recibos_detalle"></div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    agregarCuenta = async(reciboId) => {
        let id = Math.floor(Math.random() * 10000)

        $('#contenedor_cuentas').append(`<div id="${id}"></div>`)

        cargarInterfaz('configuracion/recibos/detalle/cuenta', id, {id: id, id_recibo: reciboId})
    }

    aprobarPago = async(reciboId) => {
        // Arreglo para enviar la imputación
        var cuentas = []
        var totalImputado = 0
        var totalRecibo = parseFloat($('#total_recibo').val())
        var totalCuentas = 0

        $('.valor_cuenta_recibo').each(function() {
            totalCuentas++
            
            let id = $(this).attr('data-id')

            let camposObligatorios = [
                $(`#cuenta_${id}`),
                $(`#fecha_pago_${id}`),
                $(`#valor_${id}`),
            ]

            if (!validarCamposObligatorios(camposObligatorios)) return false

            totalImputado += parseFloat($(this).val())
            let fechaPago = $(`#fecha_pago_${id}`).val().split('-')

            // Se agrega la cuenta al arreglo
            cuentas.push({
                F350_CONSEC_DOCTO: 1,
                F351_ID_AUXILIAR: $(`#cuenta_${id} option:selected`).attr('data-codigo'),
                F351_VALOR_DB: parseFloat($(this).val()),
                F351_NRO_DOCTO_BANCO: `${fechaPago[0]}${fechaPago[1]}${fechaPago[2]}`,
                F351_NOTAS: 'Pago mediante el Ecommerce, subiendo comprobante'
            })
        })

        // Si los valores imputados y del recibo no coinciden
        if(totalCuentas == 0) {
            mostrarAviso('alerta', 'Por favor elija al menos una cuenta.')
            return false
        }

        // Si los valores imputados y del recibo no coinciden
        if(totalRecibo !== totalImputado) {
            mostrarAviso('alerta', 'El valor imputado no es igual al valor total del recibo.')
            return false
        }

        Swal.fire({
            title: 'Creando documento contable en Siesa...',
            text: 'Por favor, espera. No cierres esta ventana ni refresques la página',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })
        
        await consulta('crear', {tipo: 'factura_documento_contable', 'id_factura': reciboId, cuentas: cuentas}, false)
        .then(pago => {
            Swal.close()

            if(pago.resultado.error) {
                mostrarAviso('error', `
                    Ocurrió un error al crear el documento contable en Siesa:
                    <pre>
                        ${pago.resultado.mensaje.documento_contable}
                    </pre>
                `, 100000)
                return false
            }

            mostrarAviso('exito', 'El documento contable se asentó correctamente en Siesa.')
            return false
        })
        .catch(error => {
            mostrarAviso('error', 'Ocurrió un error al crear el documento contable en Siesa')
            return false
        })
    }

    $().ready(() => {
        cargarInterfaz('configuracion/recibos/detalle/resumen', 'contenedor_recibos_detalle', {token: '<?php echo $this->uri->segment(4); ?>'})
    })
</script>