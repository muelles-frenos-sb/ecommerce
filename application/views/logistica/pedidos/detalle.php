<?php
$total = 0;
$subtotal = 0;
$impuestos = 0;
?>

<div class="block">
    <div class="container container--max--xl">
        <div class="wishlist">
            <table class="wishlist__table">
                <thead class="wishlist__head">
                    <tr class="wishlist__row wishlist__row--head">
                        <th class="wishlist__column wishlist__column--head wishlist__column--image">Foto</th>
                        <th class="wishlist__column wishlist__column--head wishlist__column--product">Producto</th>
                        <th class="wishlist__column wishlist__column--head wishlist__column--stock">Precio</th>
                        <th class="wishlist__column wishlist__column--head wishlist__column--price">Cantidad</th>
                        <th class="wishlist__column wishlist__column--head wishlist__column--button">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="wishlist__body">
                    <?php foreach($pedido_detalle as $detalle) {
                        $item = $this->productos_model->obtener('productos', ['id' => $detalle->f120_id, 'omitir_bodega' => true, 'omitir_lista_precio' => true]);

                        $subtotal  += $detalle->f431_vlr_bruto;
                        $impuestos += $detalle->f431_vlr_imp;
                        $total     += $detalle->f431_vlr_neto;
                    ?>
                        <tr class="wishlist__row wishlist__row--body">
                            <td class="wishlist__column wishlist__column--body wishlist__column--image">
                                <div class="image image--type--product">
                                    <?php if($item) { ?>
                                        <a href="<?php echo site_url("productos/ver/$item->slug"); ?>" class="image__body" target="_blank">
                                            <img class="image__tag" src="<?php echo url_fotos($item->marca, $item->referencia); ?>" alt="Foto del producto">
                                        </a>
                                    <?php } else { ?>
                                        <img class="image__tag" src="<?php echo base_url('archivos/fotos/producto_generico.webp'); ?>" alt="Sin foto">
                                    <?php } ?>
                                </div>
                            </td>
                            <td class="wishlist__column wishlist__column--body wishlist__column--product">
                                <div class="wishlist__product-name">
                                    <?php if($item) { ?>
                                        <a href="<?php echo site_url("productos/ver/$item->slug"); ?>" target="_blank">
                                            <?php echo $detalle->f120_descripcion; ?>
                                        </a>
                                    <?php } else { ?>
                                        <?php echo $detalle->f120_descripcion; ?>
                                    <?php } ?>
                                </div>
                            </td>
                            <td class="wishlist__column wishlist__column--body wishlist__column--stock">
                                <?php echo formato_precio($detalle->f431_precio_unitario_base); ?>
                            </td>
                            <td class="wishlist__column wishlist__column--body wishlist__column--price">
                                <?php echo $detalle->f431_cant1_pedida; ?>
                            </td>
                            <td class="wishlist__column wishlist__column--body wishlist__column--price">
                                <?php echo formato_precio($detalle->f431_vlr_neto); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <table class="cart__totals-table">
                        <thead>
                            <tr>
                                <th>Subtotal</th>
                                <td><?php echo formato_precio($subtotal); ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Impuestos</th>
                                <td><?php echo formato_precio($impuestos); ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <td><?php echo formato_precio($total); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </tfoot>
            </table>
        </div>
    </div>
</div>