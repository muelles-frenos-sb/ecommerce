<?php
$producto = $this->productos_model->obtener('productos', ['id' => $datos['id']]);
$item = buscar_item_carrito($producto->id);
?>

<div class="product__actions">
    <?php if(!empty($item)) { ?>
            <div class="input-number">
                <input class="input-number__input form-control form-control-lg" type="number" min="1" max="<?php echo $producto->disponible; ?>" value="<?php echo $item['qty']; ?>" disabled>
                <div class="input-number__add" onClick="javascript:modificarItem('agregar', '<?php echo $item['rowid']; ?>', <?php echo $producto->id; ?>)"></div>
                <div class="input-number__sub" onClick="javascript:modificarItem('remover', '<?php echo $item['rowid']; ?>', <?php echo $producto->id; ?>)"></div>
            </div>
    <?php } else { ?>
        <div class="product__actions-item product__actions-item--addtocart">
            <button
                class="btn btn-primary btn-lg btn-block"
                onClick="javascript:agregarProducto({
                    id: <?php echo $producto->id; ?>,
                    precio: <?php echo $producto->precio; ?>,
                    referencia: '<?php echo $producto->referencia; ?>',
                    unidad_inventario: '<?php echo $producto->unidad_inventario; ?>',
                })">
                <i class="fa fa-plus"></i> Agregar al carrito
            </button>

            <a class="btn btn-success btn-lg btn-block" href="<?php echo site_url("carrito/finalizar"); ?>">
                Ir a pagar
            </a>

            <a href="<?php echo site_url(); ?>">
                <img src="<?php echo base_url(); ?>images/continuar_compra.png" alt="Continuar comprando" class="mt-2 mb-2" width="100%">
            </a>
        </div>
        <div class="product__actions-divider"></div>
    <?php } ?>
</div>