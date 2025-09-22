<style>
    #tabla_solicitudes_credito_bitacora tbody td {
        font-size: 0.7em;
        padding: 5px;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_solicitudes_credito_bitacora"></table>

<script>
    $().ready(async () => {
        let = tablaSolicitudesCredito = $("#tabla_solicitudes_credito_bitacora").DataTable({
            ajax: {
                url: `${$("#site_url").val()}clientes/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'solicitudes_credito_bitacora'
                    datos.solicitud_id = $('#id_solicitud_credito').val()
                },
            },
            columns: [
                {
                    title: `Id`,
                    data: 'id',
                    className: 'text-right',
                    render: (id, type, row) => {
                        return `
                            <a href="javascript:cargarBitacoraDetalle(${id})">
                                ${id}
                            </a>
                        `
                    }
                },
                {
                    title: `Fecha creación`,
                    data: 'fecha'
                },
                { title: 'Hora', data: 'hora' },
                { 
                    title: `Usuario`, 
                    data: 'nombre_usuario' 
                },
                { 
                    title: `Comentarios`, 
                    data: 'observaciones' 
                },
            ],
            columnDefs: [
                { targets: '_all', className: 'dt-head-center p-1' } // Todo el encabezado alineado al centro
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
    })
</script>