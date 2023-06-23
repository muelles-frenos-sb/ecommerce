<ul class="dropcart__list">
    <?php
    foreach ($this->cart->contents() as $item) {
        $datos = ['id' => $item['id']];
        $producto = $this->productos_model->obtener('productos', $datos);
    ?>
        <li class="dropcart__item">
            <div class="dropcart__item-image image image--type--product">
                <a class="image__body" href="product-full.html">
                    <img class="image__tag" src="<?php echo $this->config->item('url_fotos').trim($producto->marca).'/'.$producto->referencia.'.jpg'; ?>" alt="">
                </a>
            </div>
            <div class="dropcart__item-info">
                <div class="dropcart__item-name">
                    <a href="product-full.html"><?php echo $producto->notas; ?></a>
                </div>
                <ul class="dropcart__item-features">
                    <li>Color: Yellow</li>
                    <li>Material: Aluminium</li>
                </ul>
                <div class="dropcart__item-meta">
                    <div class="dropcart__item-quantity"><?php echo $item['qty']; ?></div>
                    <div class="dropcart__item-price">$ <?php echo $this->cart->format_number($item['subtotal']); ?></div>
                </div>
            </div>
            <button type="button" class="dropcart__item-remove">
                <svg width="10" height="10">
                    <path d="M8.8,8.8L8.8,8.8c-0.4,0.4-1,0.4-1.4,0L5,6.4L2.6,8.8c-0.4,0.4-1,0.4-1.4,0l0,0c-0.4-0.4-0.4-1,0-1.4L3.6,5L1.2,2.6 c-0.4-0.4-0.4-1,0-1.4l0,0c0.4-0.4 1-0.4,1.4,0L5,3.6l2.4-2.4c0.4-0.4,1-0.4,1.4,0l0,0c0.4,0.4,0.4,1,0,1.4L6.4,5l2.4,2.4 C9.2,7.8,9.2,8.4,8.8,8.8z" />
                </svg>
            </button>
        </li>
        <li class="dropcart__divider" role="presentation"></li>
    <?php } ?>
</ul>
<div class="dropcart__totals">
    <table>
        <tr>
            <th>Total</th>
            <td>$ <?php echo $this->cart->format_number($this->cart->total()); ?></td>
        </tr>
    </table>
</div>
<div class="dropcart__actions">
    <a href="cart.html" class="btn btn-secondary">View Cart</a>
    <a href="checkout.html" class="btn btn-primary">Checkout</a>
</div>