<?php
$factura = $this->productos_model->obtener('factura', ['token' => $datos['token']]);
$factura_detalle = $this->productos_model->obtener('factura_detalle', ['fd.factura_id' => $factura->id]);
$wompi = json_decode($factura->wompi_datos, true);
?>

<div class="block">
    <div class="container container--max--xl">
        <div class="wishlist">
            <table class="wishlist__table">
                <thead class="wishlist__head">
                    <tr class="wishlist__row wishlist__row--head">
                        <?php if($factura->factura_tipo_id == 1) { ?>
                            <th class="wishlist__column wishlist__column--head wishlist__column--image">Foto</th>
                        <?php } ?>
                        <th class="wishlist__column wishlist__column--head wishlist__column--product">
                            <?php echo ($factura->factura_tipo_id == 1) ? 'Producto' : 'Factura' ; ?>
                        </th>
                        <th class="wishlist__column wishlist__column--head wishlist__column--price">Cantidad</th>
                        <th class="wishlist__column wishlist__column--head wishlist__column--price">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="wishlist__body">
                    <?php
                    foreach($factura_detalle as $detalle) {
                        if($factura->factura_tipo_id == 1) {
                            $item = $this->productos_model->obtener('productos', ['id' => $detalle->producto_id]);
                        } else {
                            $item = $this->clientes_model->obtener('clientes_facturas', ['id' => $detalle->cliente_factura_id]);
                        }
                        ?>
                        <tr class="wishlist__row wishlist__row--body">
                            <?php if($factura->factura_tipo_id == 1) { ?>
                                <td class="wishlist__column wishlist__column--body wishlist__column--image">
                                    <div class="image image--type--product">
                                        <a href="<?php echo site_url("productos/ver/$item->id"); ?>" class="image__body">
                                            <img class="image__tag" src="<?php echo url_fotos($item->marca, $item->referencia); ?>">
                                        </a>
                                    </div>
                                </td>
                                <td class="wishlist__column wishlist__column--body wishlist__column--product">
                                    <div class="wishlist__product-name">
                                        <a href="<?php echo site_url("productos/ver/$item->id"); ?>"><?php echo $item->notas; ?></a>
                                    </div>
                                </td>
                            <?php } else { ?>
                                <td class="wishlist__column wishlist__column--body wishlist__column--image"><?php echo "$item->Tipo_Doc_cruce-$item->Nro_Doc_cruce"; ?></td>
                                <td class="wishlist__column wishlist__column--body wishlist__column--product"></td>
                            <?php } ?>

                            <td class="wishlist__column wishlist__column--body wishlist__column--price">
                                <?php echo $detalle->cantidad; ?>
                            </td>
                            <td class="wishlist__column wishlist__column--body wishlist__column--price">
                                <?php echo formato_precio($detalle->subtotal); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card-body card-body--padding--2">
        <a class="btn btn-info" href="<?php echo base_url()."archivos/facturas/$factura->nombre_archivo" ?>" download>Descargar comprobante</a>
        <a class="btn btn-danger" href="javascript:;">Rechazar pago</a>
        <a class="btn btn-success" href="javascript:;" onClick="javascript:aprobarPago(<?php echo $factura->id; ?>)">Aprobar pago</a>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    $(`.facturas_items`).addClass('account-nav__item--active')
</script>