<?php
$opciones = [
	'contador' => $datos['contador'],
];

$registros = $this->configuracion_model->obtener('recibos', $opciones);

if(count($registros) == 0) echo '<li class="list-group-item">No se encontraron registros.</li>';

foreach ($registros as $factura) {
    $mensajes_estado_wompi = ($factura->wompi_status) ? mostrar_mensajes_estados_wompi($factura->wompi_status) : null;
    ?>
    <tr class="wishlist__row wishlist__row--body">
        <td class="wishlist__column wishlist__column--body wishlist__column--product">
            <?php echo $factura->tipo; ?>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--product">
            <div class="wishlist__product-name">
                <a href="<?php echo site_url("configuracion/recibos/id/$factura->token"); ?>"><?php echo $factura->razon_social; ?></a>
            </div>
            <div class="wishlist__product-rating">
                <div class="wishlist__product-rating-title"><?php echo $factura->documento_numero; ?></div>
            </div>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--stock">
            <div class="wishlist__product-name">
                <a href="<?php echo site_url("configuracion/recibos/id/$factura->token"); ?>"><?php echo $factura->wompi_transaccion_id; ?></a>
            </div>
            <div class="wishlist__product-rating">
                <div class="wishlist__product-rating-title"><?php echo $factura->token; ?></div>
            </div>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--stock">
            <div class="status-badge status-badge--style--<?php echo ($factura->wompi_status == 'APPROVED') ? 'success' : 'failure' ; ?> status-badge--has-text">
                <div class="status-badge__body">
                    <div class="status-badge__text"><?php if($mensajes_estado_wompi) echo $mensajes_estado_wompi['asunto']; ?></div>
                </div>
            </div>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--price">
            <?php echo formato_precio($factura->valor); ?>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--button">
            <a type="button" class="btn btn-sm btn-primary" href="<?php echo site_url("configuracion/recibos/id/$factura->token"); ?>">Ver</a>
        </td>
    </tr>
<?php } ?>

<script>
	$().ready(() => {
		let totalRegistros = parseInt("<?php echo count($registros); ?>")

		// Si no hay más datos o son menos del total configurado, se oculta el botón
		if(totalRegistros == 0 || totalRegistros < parseInt($('#cantidad_datos').val())) $("#btn_mostrar_mas").hide()
	})
</script>