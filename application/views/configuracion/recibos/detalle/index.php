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
    aprobarPago = async(reciboid) => {
        // Arreglo para enviar la imputación
        var cuentas = []
        var totalImputado = 0
        var totalRecibo = parseFloat($('#total_recibo').val())

        $('.valor_cuenta_recibo').each(function() {
            // Si la cuenta tiene valor registrado
            if($(this).val() != '0') {
                totalImputado += parseFloat($(this).val())

                // Se agrega la cuenta al arreglo
                cuentas.push({
                    F350_CONSEC_DOCTO: 1,
                    F351_ID_AUXILIAR: $(this).attr('data-codigo'),
                    F351_VALOR_DB: parseFloat($(this).val()),
                    F351_NRO_DOCTO_BANCO: '<?php echo "{$recibo->anio}{$recibo->mes}{$recibo->dia}"; ?>',
                    F351_NOTAS: 'Pago mediante el Ecommerce, subiendo comprobante'
                })
            }
        })

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
        
        await consulta('crear', {tipo: 'factura_documento_contable', 'id_factura': reciboid, cuentas: cuentas}, false)
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