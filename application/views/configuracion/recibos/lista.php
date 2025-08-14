<?php $permisos = $this->configuracion_model->obtener('permisos'); ?>

<!-- Inputs ocultos para pasar como filtros -->
<input type="hidden" id="recibo_id_tipo" value="<?php echo $datos['id_tipo_recibo']; ?>">
<input type="hidden" id="configuracion_comprobantes_eliminar" value="<?php echo (in_array(['configuracion' => 'configuracion_comprobantes_eliminar'], $permisos)); ?>">

<!-- Si es comprobante -->
<?php if($datos['id_tipo_recibo'] == 3) { ?>
    <a class="btn btn-success mb-2" href="<?php echo site_url('configuracion/comprobantes/crear'); ?>">Subir comprobante</a>
<?php } ?>

<style>
    #tabla_recibos tbody td {
        font-size: 0.7em;
        padding: 5px;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_recibos"></table>

<script>
    $().ready(async () => {
        var tablaRecibos = $("#tabla_recibos").DataTable({
            ajax: {
                url: `${$("#site_url").val()}configuracion/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'recibos'
                    datos.id_tipo_recibo = $('#recibo_id_tipo').val()
                },
            },
            columns: [
                {
                    title: 'Fecha ingreso',
                    data: null,
                    render: (recibo, type, row) => {
                        return `
                            <a href="${$('#site_url').val()}/configuracion/recibos/id/${recibo.token}" target="_blank">
                                ${recibo.fecha}
                            </a>
                        `
                    }
                },
                { title: 'Hora ingreso', data: 'hora' },
                { title: 'Fecha pago', data: 'fecha_consignacion' },
                { title: 'NIT', data: 'documento_numero' },
                { title: 'Nombre', data: 'razon_social' },
                {
                    title: 'Forma pago', 
                    data: null,
                    visible: ($('#recibo_id_tipo').val() != 3), // Visible si no es comprobantes
                    render: (recibo, type, row) => {
                        let wompi = JSON.parse(recibo.wompi_datos)

                        if(!wompi) return ``

                        // Si es tarjeta de crédito, se obtiene la información de la tarjeta
                        let metodoPago = (wompi.payment_method_type == 'CARD') ? wompi.payment_method.extra.name : ''

                        return `
                            <div class="wishlist__product-name">
                                ${wompi.payment_method_type}
                            </div>

                            <div class="wishlist__product-rating">
                                <div class="wishlist__product-rating-title">
                                    ${metodoPago}
                                </div>
                            </div>
                        `
                    }
                },
                { title: 'Recibo Siesa', data: 'numero_siesa' },
                {
                    title: 'Estado',
                    data: null,
                    render: (recibo, type, row) => {
                        return `
                            <div class="status-badge status-badge--style--${recibo.estado_clase} status-badge--has-text">
                                <div class="status-badge__body">
                                    <div class="status-badge__text">
                                        ${recibo.estado}
                                    </div>
                                </div>
                            </div>
                        `
                    }
                },
                {
                    title: 'Creador', 
                    data: null,
                    visible: ($('#recibo_id_tipo').val() == 3), // Visible si es comprobantes
                    render: (recibo, type, row) => {
                        return `${recibo.usuario_creacion}`
                    }
                },
                {
                    title: 'Comentarios', 
                    data: null,
                    visible: ($('#recibo_id_tipo').val() == 3), // Visible si es comprobantes
                    render: (recibo, type, row) => {
                        let comentarios = recibo.comentarios || ''
                        return `${comentarios}`
                    }
                },
                {
                    title: 'Valor', 
                    data: null,
                    className: 'text-right',
                    render: (recibo, type, row) => {
                        return parseFloat(recibo.valor).toLocaleString('es-CO')
                    }
                },
                {
                    title: 'Opciones', 
                    data: null,
                    render: (recibo, type, row) => {
                        let botonEliminarComprobante = ($('#recibo_id_tipo').val() == 3 && $('#configuracion_comprobantes_eliminar').val() == 1 && !recibo.fecha_actualizacion_bot)
                        ? `
                            <a type="button" class="btn btn-sm btn-danger" title="Eliminar" href="javascript:eliminarComprobante(${recibo.id})">
                                <i class="fas fa-trash"></i>
                            </a>
                        `
                        : ``

                        let botonReciboCaja = ($('#recibo_id_tipo').val() == 2 && recibo.wompi_status == 'APPROVED')
                        ? `
                            <a type="button" class="btn btn-sm btn-danger" href="${$('#site_url').val()}/reportes/pdf/recibo/${recibo.token}" target="_blank">
                                <i class="fas fa-search"></i>
                        </a>
                        ` 
                        : ``

                        return `
                            <td class="p-1">
                                ${botonReciboCaja}
                                
                                ${botonEliminarComprobante}
                            </td>
                        `
                    }
                },
            ],
            columnDefs: [
                { targets: '_all', className: 'dt-head-center' } // Todo el encabezado alineado al centro
            ],
            deferRender: true,
            fixedHeader: true,
            info: true,
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            ordering: false,
            orderCellsTop: true,
            pageLength: 100,
            paging: true,
            processing: true,
            scrollCollapse: true,
            scroller: true,
            scrollX: false,
            scrollY: false,
            searching: true,
            serverSide: true,
            stateSave: false,
        })

        // Cuando un campo de filtro personalizado cambie, se redibuja la tabla
        $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaRecibos.draw())

        // Se inactiva el evento clic dentro de los campos usados para filtros personalizados
        $('#tabla_recibos thead th').on('click', 'input, select', e => e.stopPropagation())
    })
</script>