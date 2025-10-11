<style>
    #tabla_solicitudes_garantia tbody td {
        font-size: 0.7em;
        padding: 5px;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_solicitudes_garantia"></table>

<script>
    $().ready(async () => {
        let = tablaSolicitudesGarantia = $("#tabla_solicitudes_garantia").DataTable({
            ajax: {
                url: `${$("#site_url").val()}logistica/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'solicitudes_garantia'

                    // Filtros personalizados
                    datos.filtros_personalizados = {
                        id: $('#filtro_id').val(),
                        fecha_creacion: $('#filtro_fecha_creacion').val(),
                        fecha_cierre: $('#filtro_fecha_cierre').val(),
                        numero_documento: $('#filtro_numero_documento').val(),
                        nombre: $('#filtro_nombre').val(),
                        estado: $('#filtro_estado').val(),
                        vendedor: $('#filtro_vendedor').val(),
                        producto: $('#filtro_producto').val(),
                    }
                },
            },
            columns: [
                {
                    title: `
                        Id
                        <input type="text" id="filtro_id" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'id',
                    className: 'text-right',
                    width: '70px',
                },
                {
                    title: `
                        Creación
                        <input type="date" id="filtro_fecha_creacion" class="form-control form-control-sm border-secondary" style='width: 100px;'>
                    `,
                    data: 'fecha_creacion',
                    width: '70px',
                },
                {
                    title: `
                        Cierre
                        <input type="date" id="filtro_fecha_cierre" class="form-control form-control-sm border-secondary" style='width: 100px;'>
                    `,
                    data: 'fecha_cierre',
                    width: '70px',
                },
                { 
                    title: `
                        NIT
                        <input type="text" id="filtro_numero_documento" class="form-control form-control-sm border-secondary">
                    `,
                    data: null,
                    render: (solicitud, type, row) => {
                        return `
                            <a href="${$('#site_url').val()}/logistica/solicitudes_garantia/ver/${solicitud.id}" target="_blank">
                                ${solicitud.documento_numero}
                            </a>
                        `
                    },
                },
                { 
                    title: `
                        Nombre
                        <input type="text" id="filtro_nombre" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'razon_social' 
                },
                { 
                    title: `
                        Estado
                        <input type="text" id="filtro_estado" class="form-control form-control-sm border-secondary">
                    `, 
                    data: null,
                    render: (solicitud, type, row) => {
                        return `
                            <div class="status-badge status-badge--style--${solicitud.estado_clase} status-badge--has-text">
                                <div class="status-badge__body">
                                    <div class="status-badge__text">
                                        ${solicitud.estado}
                                    </div>
                                </div>
                            </div>
                        `
                    }
                },
                { 
                    title: `
                        Vendedor
                        <input type="text" id="filtro_vendedor" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'vendedor_nombre'
                },
                { 
                    title: `
                        Producto
                        <input type="text" id="filtro_producto" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'producto'
                },
            ],
            columnDefs: [
                { targets: '_all', className: 'dt-head-center p-1' } // Todo el encabezado alineado al centro
            ],
            deferRender: true,
            fixedHeader: true,
            info: true,
            initComplete: function () {
                // Cuando un campo de filtro personalizado cambie, se redibuja la tabla
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaSolicitudesGarantia.draw())
            },
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            ordering: false,
            orderCellsTop: true,
            pageLength: 25,
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
    })
</script>