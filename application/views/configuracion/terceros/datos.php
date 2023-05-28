<?php
$opciones = [
	"contador" => $datos['contador'],
];
if($datos['busqueda']) $opciones['busqueda'] = $datos['busqueda'];

$registros = $this->configuracion_model->obtener('usuarios', $opciones);

if(count($registros) == 0) echo '<li class="list-group-item">No se encontraron registros.</li>';

foreach ($registros as $tercero) { ?>
    <tr class="wishlist__row wishlist__row--body">
        <td class="wishlist__column wishlist__column--body wishlist__column--product">
            <div class="wishlist__product-name">
                <a href=""><?php echo $tercero->razon_social; ?></a>
            </div>
            <div class="wishlist__product-rating">
                <div class="wishlist__product-rating-title"><?php echo $tercero->documento_numero; ?></div>
            </div>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--stock">
            <div class="status-badge status-badge--style--<?php echo ($tercero->estado == 1) ? 'success' : 'failure' ; ?> status-badge--has-text">
                <div class="status-badge__body">
                    <div class="status-badge__text"><?php echo $tercero->estado_nombre; ?></div>
                </div>
            </div>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--price">
            ----
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--button">
            <a type="button" class="btn btn-sm btn-primary" href="<?php echo site_url("configuracion/terceros/id/$tercero->token"); ?>">Editar</a>
        </td>
        <td class="wishlist__column wishlist__column--body wishlist__column--remove">
            <button type="button" class="wishlist__remove btn btn-sm btn-muted btn-icon">
                <svg width="12" height="12">
                    <path d="M10.8,10.8L10.8,10.8c-0.4,0.4-1,0.4-1.4,0L6,7.4l-3.4,3.4c-0.4,0.4-1,0.4-1.4,0l0,0c-0.4-0.4-0.4-1,0-1.4L4.6,6L1.2,2.6 c-0.4-0.4-0.4-1,0-1.4l0,0c0.4-0.4,1-0.4,1.4,0L6,4.6l3.4-3.4c0.4-0.4,1-0.4,1.4,0l0,0c0.4,0.4,0.4,1,0,1.4L7.4,6l3.4,3.4 C11.2,9.8,11.2,10.4,10.8,10.8z" />
                </svg>
            </button>
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