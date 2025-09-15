<style>
    #tabla_solicitudes_credito tbody td {
        font-size: 0.7em;
        padding: 5px;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_solicitudes_credito"></table>

<script>
    $().ready(async () => {
        let = tablaSolicitudesCredito = $("#tabla_solicitudes_credito").DataTable({
            ajax: {
                url: `${$("#site_url").val()}clientes/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'solicitudes_credito'

                    // Filtros personalizados
                    datos.filtro_fecha_creacion = $('#filtro_fecha_creacion').val()
                    datos.filtro_numero_documento = $('#filtro_numero_documento').val()
                    datos.filtro_nombre = $('#filtro_nombre').val()
                },
            },
            columns: [
                {
                    title: `
                        Fecha creación
                        <input type="date" id="filtro_fecha_creacion" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'fecha'
                },
                { title: 'Hora creación', data: 'hora' },
                { 
                    title: `
                        NIT
                        <input type="text" id="filtro_numero_documento" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'documento_numero' },
                { 
                    title: `
                        Nombre
                        <input type="text" id="filtro_nombre" class="form-control form-control-sm border-secondary">
                    `, 
                    data: 'nombre_solicitante' },
                {
                    title: 'Opciones', 
                    data: null,
                    render: (solicitud, type, row) => {
                        let botonVer = `
                            <a type="button" class="btn btn-sm btn-danger" href="${$('#site_url').val()}/clientes/credito/ver/${solicitud.id}" target="_blank">
                                <i class="fas fa-search"></i>
                            </a>
                        `

                        let botonRealizarEnvioFirmaBot = (!solicitud.fecha_envio_firma)
                        ? `
                            <a type="button" class="btn btn-sm btn-primary" href="javascript:realizarEnvioFirmaBot(${solicitud.id})" title="Envío de la firma">
                                <i class="fas fa-signature"></i>
                            </a>
                        ` 
                        : ``

                        return `
                            <td class="p-1">
                                ${botonVer}

                                ${botonRealizarEnvioFirmaBot}
                            </td>
                        `
                    }
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
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaSolicitudesCredito.draw())
            },
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
    })
</script>