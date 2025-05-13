<?php
$opciones = [
    // 'contador' => $datos['contador'],
    'id_tipo_recibo' => $datos['id_tipo_recibo'],
];

$registros = $this->configuracion_model->obtener('recibos', $opciones);
$permisos = $this->configuracion_model->obtener('permisos');

if(count($registros) == 0) echo '<li class="list-group-item">No se encontraron registros.</li>';

foreach ($registros as $recibo) {
    $mensajes_estado_wompi = ($recibo->wompi_status) ? mostrar_mensajes_estados_wompi($recibo->wompi_status) : null;
    if($recibo->wompi_datos) $wompi = json_decode($recibo->wompi_datos, true);
    ?>
    <tr style="font-size: 0.7em;">
        <td>
            <a href="<?php echo site_url("configuracion/recibos/id/$recibo->token"); ?>">
                <?php echo $recibo->fecha; ?>
            </a>
        </td>
        <td><?php echo $recibo->hora; ?></td>
        <td><?php echo $recibo->fecha_consignacion; ?></td>
        <td><?php echo $recibo->documento_numero; ?></td>
        <td><?php echo $recibo->razon_social; ?></td>

        <!-- Referencia -->
        <?php if(false) { ?>
            <td>
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
        <?php } ?>

        <!-- Forma de pago -->
        <?php if($datos['id_tipo_recibo'] != 3) { ?>
            <td>
                <div class="wishlist__product-name">
                    <?php if(isset($wompi)) echo $wompi['payment_method_type']; ?>
                </div>
                <div class="wishlist__product-rating">
                    <div class="wishlist__product-rating-title">
                        <?php if(isset($wompi) && $wompi['payment_method_type'] == 'CARD') echo $wompi['payment_method']['extra']['name']; ?>
                    </div>
                </div>
            </td>
        <?php } ?>

        <td><?php echo $recibo->numero_siesa; ?></td>

        <td>
            <div class="status-badge status-badge--style--<?php echo $recibo->estado_clase; ?> status-badge--has-text">
                <div class="status-badge__body">
                    <div class="status-badge__text">
                        <?php echo $recibo->estado; ?>
                    </div>
                </div>
            </div>
        </td>

        <td>
            <div class="status-badge status-badge--style--<?php echo $recibo->estado_clase; ?> status-badge--has-text">
                <?php echo $recibo->usuario_creacion; ?>
            </div>
        </td>

        <?php if($datos['id_tipo_recibo'] != 3) { ?>
            <td>
                <?php echo $recibo->usuario_gestion; ?>
            </td>
        <?php } ?>

        <td class="text-right"><?php echo formato_precio($recibo->valor); ?></td>

        <!-- Opciones -->
        <td class="p-1">
            <a type="button" class="btn btn-sm btn-danger" href="<?php echo site_url("reportes/pdf/recibo/$recibo->token"); ?>" target="_blank">
                <i class="fas fa-search"></i>
            </a>

            <?php if (in_array(['configuracion' => 'configuracion_comprobantes_eliminar'], $permisos) && $datos['id_tipo_recibo'] == 3 && !$recibo->fecha_actualizacion_bot) { ?>
                <a type="button" class="btn btn-sm btn-danger" title="Eliminar" href="javascript:eliminarComprobante(<?php echo $recibo->id; ?>)">
                    <i class="fas fa-trash"></i>
                </a>
            <?php } ?>
        </td>
    </tr>
<?php } ?>

<script>
	$().ready(() => {
		let totalRegistros = parseInt("<?php echo count($registros); ?>")

		// Si no hay más datos o son menos del total configurado, se oculta el botón
		if(totalRegistros == 0 || totalRegistros < parseInt($('#cantidad_datos').val())) $("#btn_mostrar_mas").hide()

        new DataTable('#tabla_recibos', {
            info: true,
            // ordering: true,
            // order: [[5, 'desc']],
            paging: true,
            stateSave: true,
            scrollY: '640px',
            searching: true,
            language: {
                decimal: ',',
                thousands: '.'
            },
            language: {
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            scrollX: false,
            scrollCollapse: true,
        })
	})
</script>