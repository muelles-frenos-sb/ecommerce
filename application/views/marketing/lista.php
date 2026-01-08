<style>
    #tabla_campanias tbody td {
        font-size: 0.9em;
        padding: 8px;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_campanias"></table>

<script>
    $().ready(async () => {
        var tablaCampanias = $("#tabla_campanias").DataTable({
            ajax: {
                url: `${$("#site_url").val()}marketing/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'campanias'

                    // Filtros personalizados
                    datos.filtro_id = $('#filtro_id').val()
                    datos.filtro_fecha_inicio = $('#filtro_fecha_inicio').val()
                    datos.filtro_fecha_finalizacion = $('#filtro_fecha_finalizacion').val()
                    datos.filtro_cantidad_contactos = $('#filtro_cantidad_contactos').val()
                    datos.filtro_cantidad_envios = $('#filtro_cantidad_envios').val()
                },
            },
            columns: [
                { 
                    title: `
                        Id
                        <input type="number" id="filtro_id" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'id',
                    className: 'dt-body-right'
                },
                { 
                    title: `
                        Fecha de inicio
                        <input type="date" id="filtro_fecha_inicio" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'fecha_inicio'
                },
                { 
                    title: `
                        Fecha de finalización
                        <input type="date" id="filtro_fecha_finalizacion" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'fecha_finalizacion'
                },
                { 
                    title: `
                        Cantidad de contactos
                        <input type="number" id="filtro_cantidad_contactos" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'cantidad_contactos',
                    className: 'dt-body-right'
                },
                { 
                    title: `
                        Cantidad de envíos
                        <input type="number" id="filtro_cantidad_envios" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'cantidad_envios',
                    className: 'dt-body-right'
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
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaCampanias.draw())
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