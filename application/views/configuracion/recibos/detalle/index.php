<?php $recibo = $this->productos_model->obtener('recibo', ['token' => $this->uri->segment(4)]);?>

<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container container--max--xl">
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
        await consulta('crear', {tipo: 'factura_documento_contable', 'id_factura': reciboid}, false)
        .then(pago => {
            if(pago.resultado.error) {
                mostrarAviso('error', 'Ocurrió un error al crear el documento contable en Siesa')
                return false
            }
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