<?php
$producto = $this->productos_model->obtener('productos', ['id' => $datos['id']]);
$item = buscar_item_carrito($producto->id);
?>

<?php if(!empty($item)) { ?>
    <div class="product__actions">
        <div class="input-number">
            <input class="input-number__input form-control form-control-lg" type="number" min="1" max="<?php echo $producto->disponible; ?>" value="<?php echo $item['qty']; ?>" disabled>
            <div class="input-number__add" onClick="javascript:modificarItem('agregar', '<?php echo $item['rowid']; ?>', <?php echo $producto->id; ?>)"></div>
            <div class="input-number__sub" onClick="javascript:modificarItem('remover', '<?php echo $item['rowid']; ?>', <?php echo $producto->id; ?>)"></div>
        </div>
    </div>
<?php } else { ?>
    <div class="product__actions">
        <div class="product__actions-item product__actions-item--addtocart">
            <button
                class="btn btn-primary btn-xl btn-block"
                onClick="javascript:agregarProducto({
                    id: <?php echo $producto->id; ?>,
                    precio: <?php echo $producto->precio; ?>,
                    referencia: '<?php echo $producto->referencia; ?>',
                    unidad_inventario: '<?php echo $producto->unidad_inventario; ?>',
                })">
                <i class="fa fa-plus"></i> Agregar al carrito
            </button>
        </div>
    </div>
<?php } ?>