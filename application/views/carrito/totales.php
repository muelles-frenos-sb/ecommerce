<div class="card-body card-body--padding--2">
    <h3 class="card-title">Resumen</h3>
    <table class="cart__totals-table">
        <thead>
            <tr>
                <th>Subtotal</th>
                <td><?php echo formato_precio($this->cart->total()); ?></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>Envío</th>
                <td>
                    $ 0
                    <!-- <div>
                        <a href="">Calculate shipping</a>
                    </div> -->
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <td><?php echo formato_precio($this->cart->total()); ?></td>
            </tr>
        </tfoot>
    </table>
    <a class="btn btn-primary btn-xl btn-block" href="<?php echo site_url("carrito/finalizar"); ?>">
        Finalizar pedido
    </a>
</div>