<?php $factura = $this->productos_model->obtener('factura', ['token' => $this->uri->segment(4)]);?>

<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container container--max--xl">
        <div class="row">
            <div class="col-12 col-lg-3 d-flex">
                <div class="account-nav flex-grow-1">
                    <h4 class="account-nav__title">Opciones</h4>
                    <ul class="account-nav__list">
                        <!-- Ítems -->
                        <li class="facturas_items account-nav__item">
                            <a onClick="cargarInterfaz('configuracion/facturas/detalle/items', 'contenedor_facturas_detalle', {token: '<?php echo $this->uri->segment(4); ?>'})">Ítems</a>
                        </li>

                        <!-- Pago -->
                        <?php if($factura->factura_tipo_id == 1) { ?>
                            <li class="facturas_wompi account-nav__item">
                                <a onClick="cargarInterfaz('configuracion/facturas/detalle/wompi', 'contenedor_facturas_detalle', {token: '<?php echo $this->uri->segment(4); ?>'})">Pago</a>
                            </li>
                        <?php } ?>

                        <!-- Comprobante -->
                        <?php if($factura->factura_tipo_id != 1) { ?>
                            <li class="facturas_comprobante account-nav__item">
                                <a onClick="cargarInterfaz('configuracion/facturas/detalle/comprobante', 'contenedor_facturas_detalle', {token: '<?php echo $this->uri->segment(4); ?>'})">Comprobante</a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-lg-9 mt-4 mt-lg-0">
                <div id="contenedor_facturas_detalle"></div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    aprobarPago = async(facturaId) => {
        await consulta('crear', {tipo: 'factura_documento_contable', 'id_factura': facturaId}, false)
        .then(pago => {
            console.log(pago)
        })
        .catch(error => {
            mostrarAviso('error', 'Ocurrió un error al crear el documento contable en Siesa')
            return false
        })
    }

    $().ready(() => {
        cargarInterfaz('configuracion/facturas/detalle/items', 'contenedor_facturas_detalle', {token: '<?php echo $this->uri->segment(4); ?>'})
    })
</script>