<div class="block">
    <div class="container container--max--xl">
        <div class="wishlist">
            <table class="wishlist__table">
                <thead class="wishlist__head">
                    <tr class="wishlist__row wishlist__row--head">
                        <th class="wishlist__column wishlist__column--head wishlist__column--product text-center">
                            Documento
                        </th>
                        <th class="wishlist__column wishlist__column--head wishlist__column--price text-center">Valor</th>
                    </tr>
                </thead>
                <tbody class="wishlist__body">
                    <?php foreach($recibo_detalle as $detalle) { ?>
                        <tr class="wishlist__row wishlist__row--body">
                            <td class="wishlist__column wishlist__column--body wishlist__column--image"><?php echo "$detalle->documento_cruce_tipo-$detalle->documento_cruce_numero"; ?></td>
                            <td class="wishlist__column wishlist__column--body wishlist__column--price">
                                <?php echo formato_precio($detalle->subtotal); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="card-body card-body--padding--2">
                <?php
                if($recibo->archivos) {
                    $archivos = glob("./archivos/recibos/$recibo->id/*");

                    foreach ($archivos as $archivo) {
                    ?>
                        <a class="btn btn-info mb-2" href="<?php echo base_url()."archivos/recibos/$recibo->id/".basename($archivo); ?>" download>Descargar comprobante</a>
                    <?php } ?>
                <?php } ?>
            </div>

            <div class="vehicles-list__body mt-2 ml-2 mr-2">
                <h3>Distribución del pago</h3>
                
                <div id="contenedor_cuentas"></div>

                <a class="btn btn-info btn-block mb-2" href="javascript:;" onClick="javascript:agregarCuenta(<?php echo $recibo->id; ?>);">Agregar cuenta</a>

                <?php if($recibo->recibo_estado_id == 3) { ?>
                    <p>
                        <a class="btn btn-danger" href="javascript:;">Rechazar pago</a>
                        <a class="btn btn-success" href="javascript:;" onClick="javascript:aprobarPago(<?php echo $recibo->id; ?>)">Aprobar pago</a>
                    </p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    $(`.facturas_items`).addClass('account-nav__item--active')
</script>