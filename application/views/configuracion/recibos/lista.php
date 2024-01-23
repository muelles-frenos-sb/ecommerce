<input type="hidden" id="recibo_id_tipo" value="<?php echo $datos['id_tipo_recibo']; ?>">

<div class="wishlist">
    <table class="wishlist__table table-responsive">
        <thead class="wishlist__head">
            <tr class="wishlist__row wishlist__row--head">
                <th class="wishlist__column wishlist__column--head wishlist__column--product text-center">Fecha</th>
                <th class="wishlist__column wishlist__column--head wishlist__column--product text-center">Hora</th>
                <th class="wishlist__column wishlist__column--head wishlist__column--product text-center">Cliente</th>
                <th class="wishlist__column wishlist__column--head wishlist__column--product text-center">Referencia</th>
                <th class="wishlist__column wishlist__column--head wishlist__column--product text-center">Forma de pago</th>
                <th class="wishlist__column wishlist__column--head wishlist__column--stock text-center">Estado</th>
                <th class="wishlist__column wishlist__column--head wishlist__column--price text-center">Valor</th>
                <th class="wishlist__column wishlist__column--head wishlist__column--button text-center"></th>
            </tr>
        </thead>
        <tbody class="wishlist__body" id="datos">
            <?php $this->load->view("configuracion/recibos/datos"); ?>
        </tbody>
    </table>
</div>

<button class="btn btn-primary btn-block mt-3" onClick="javascript:cargarMasDatos('configuracion/recibos')" id="btn_mostrar_mas">Mostrar más datos</button>