<?php
$productos = $this->clientes_model->obtener('clientes_productos', [
    'f350_consec_docto' => $datos['documento_cruce'],
    'f200_nit_fact' => $datos['numero_documento'],
]);
?>

<div class="modal fade" id="modal_productos" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalle de los productos en el pedido</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="block">
                    <div class="container">
                        <div class="cart">
                            <div class="cart__table cart-table">
                                <table class="cart-table__table">
                                    <thead class="cart-table__head">
                                        <tr class="cart-table__row">
                                            <th class="cart-table__column cart-table__column--image">Foto</th>
                                            <th class="cart-table__column cart-table__column--product">Producto</th>
                                            <th class="cart-table__column cart-table__column--price">Precio</th>
                                            <th class="cart-table__column cart-table__column--quantity">Cantidad</th>
                                            <th class="cart-table__column cart-table__column--total">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="cart-table__body">
                                        <?php
                                        $total = 0;
                                        $subtotal = 0;
                                        $impuestos = 0;

                                        foreach ($productos as $detalle) {
                                            $producto = $this->productos_model->obtener('productos', ['id' => $detalle->f120_id]);

                                            $subtotal += $detalle->f470_vlr_bruto;
                                            $impuestos += $detalle->f470_vlr_imp;
                                            $total += $detalle->f470_vlr_neto;
                                            ?>
                                            <tr class="cart-table__row">
                                                <td class="cart-table__column cart-table__column--image">
                                                    <div class="image image--type--product">
                                                        <a href="<?php echo site_url("productos/ver/$producto->id"); ?>" class="image__body">
                                                            <img class="image__tag" src="<?php echo url_fotos($producto->marca, $producto->referencia); ?>" alt="Foto de producto">
                                                        </a>
                                                    </div>
                                                </td>
                                                <td class="cart-table__column cart-table__column--product">
                                                    <a href="<?php echo site_url("productos/ver/$producto->id"); ?>" class="cart-table__product-name"><?php echo $detalle->f120_descripcion; ?></a>
                                                    <ul class="cart-table__options">
                                                        <li>Marca: <?php echo $producto->marca; ?></li>
                                                        <li>Línea: <?php echo $producto->linea; ?></li>
                                                        <li>Grupo: <?php echo $producto->grupo; ?></li>
                                                    </ul>
                                                </td>
                                                <td class="cart-table__column cart-table__column--price" data-title="Price">
                                                    <?php echo formato_precio($detalle->f470_precio_uni); ?>
                                                </td>
                                                <td class="cart-table__column cart-table__column--quantity" data-title="Quantity">
                                                    <?php echo $detalle->f470_cant_base; ?>
                                                </td>
                                                <td class="cart-table__column cart-table__column--total" data-title="Total">
                                                    <?php echo formato_precio($detalle->f470_vlr_neto); ?>
                                                </td>
                                            </tr>
                                        <?php } ?>                                        
                                    </tbody>
                                </table>
                            </div>
                            <div class="cart__totals">
                                <div class="card">
                                    <div class="card-body card-body--padding--2">
                                        <h3 class="card-title">Resumen</h3>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block-space block-space--layout--before-footer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Pagar</button>
            </div>
        </div>
    </div>
</div>

<script>
     $().ready(function() {
        $('#modal_productos').modal({
            backdrop: 'static',
            keyboard: true
        })
    })
</script>