<?php
$opciones = [
    'contador' => $datos['contador'],
    'id_tipo_recibo' => $datos['id_tipo_recibo'],
];
if($datos['busqueda']) $opciones['busqueda'] = $datos['busqueda'];

$registros = $this->configuracion_model->obtener('recibos', $opciones);

if(count($registros) == 0) echo '<li class="list-group-item">No se encontraron registros.</li>';

foreach ($registros as $recibo) {
    $mensajes_estado_wompi = ($recibo->wompi_status) ? mostrar_mensajes_estados_wompi($recibo->wompi_status) : null;
    if($recibo->wompi_datos) $wompi = json_decode($recibo->wompi_datos, true);
    ?>
    <tr class="wishlist__row wishlist__row--body" style="font-size: 0.8em;">
        <!-- Fecha -->
        <td class="wishlist__column wishlist__column--body wishlist__column--product">
            <?php echo $recibo->fecha; ?>
        </td>

        <!-- Hora -->
        <td class="wishlist__column wishlist__column--body wishlist__column--product">
            <?php echo $recibo->hora; ?>
        </td>

        <!-- Cliente -->
        <td class="wishlist__column wishlist__column--body wishlist__column--product">
            <div class="wishlist__product-name">
                <a href="<?php echo site_url("configuracion/recibos/id/$recibo->token"); ?>">
                    <?php echo $recibo->razon_social; ?>
                </a>
            </div>

            <div class="wishlist__product-rating">
                <div class="wishlist__product-rating-title">
                    <?php echo $recibo->documento_numero; ?>
                </div>
            </div>
        </td>

        <!-- Referencia -->
        <td class="wishlist__column wishlist__column--body wishlist__column--stock">
            <div class="wishlist__product-name">
                <a href="<?php echo site_url("configuracion/recibos/id/$recibo->token"); ?>">
                    <?php echo $recibo->token; ?>
                </a>
            </div>
            <div class="wishlist__product-rating">
                <div class="wishlist__product-rating-title">
                    <?php echo $recibo->wompi_transaccion_id; ?>
                </div>
            </div>
        </td>

        <!-- Forma de pago -->
        <td class="wishlist__column wishlist__column--body wishlist__column--stock">
            <div class="wishlist__product-name">
                <?php if(isset($wompi)) echo $wompi['payment_method_type']; ?>
            </div>
            <div class="wishlist__product-rating">
                <div class="wishlist__product-rating-title">
                    <?php if(isset($wompi) && $wompi['payment_method_type'] == 'CARD') echo $wompi['payment_method']['extra']['name']; ?>
                </div>
            </div>
        </td>

        <!-- Estado -->
        <td class="wishlist__column wishlist__column--body wishlist__column--stock">
            <div class="status-badge status-badge--style--<?php echo $recibo->estado_clase; ?> status-badge--has-text">
                <div class="status-badge__body">
                    <div class="status-badge__text">
                        <?php echo $recibo->estado; ?>
                    </div>
                </div>
            </div>
        </td>

        <!-- Usuario que creó -->
        <td class="wishlist__column wishlist__column--body wishlist__column--stock">
            <div class="status-badge status-badge--style--<?php echo $recibo->estado_clase; ?> status-badge--has-text">
                <?php echo $recibo->usuario_creacion; ?>
            </div>
        </td>

        <!-- Usuario que aprobó o rechazó -->
        <td class="wishlist__column wishlist__column--body wishlist__column--stock">
            <?php echo $recibo->usuario_gestion; ?>
        </td>

        <!-- Valor -->
        <td class="wishlist__column wishlist__column--body wishlist__column--price">
            <?php echo formato_precio($recibo->valor); ?>
        </td>

        <!-- Opciones -->
        <td class="wishlist__column wishlist__column--body wishlist__column--button">
            <a type="button" class="btn btn-sm btn-primary" href="<?php echo site_url("configuracion/recibos/id/$recibo->token"); ?>">Ver</a>
            <a type="button" class="btn btn-sm btn-danger" href="<?php echo site_url("reportes/pdf/recibo/$recibo->token"); ?>" target="_blank">
                <i class="fa fa-file-pdf"></i>
            </a>
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