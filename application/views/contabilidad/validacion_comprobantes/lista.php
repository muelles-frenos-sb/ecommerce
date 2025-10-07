<table class="table-striped table-bordered" id="tabla_comprobantes_contables"></table>

<script>
    $().ready(() => {
        var tablaComprobantesContables = $("#tabla_comprobantes_contables").DataTable({
            ajax: {
                url: `${$("#site_url").val()}contabilidad/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'comprobantes_contables_validacion'
                },
            },
            columns: [
                {
                    title: `
                        Id
                    `,
                    data: 'id',
                    className: 'text-right',
                    width: '70px',
                },
                {
                    title: `
                        Documento
                    `,
                    data: 'directorio',
                },
                {
                    title: `
                        Documento validado
                    `,
                    data: 'validado',
                    width: '70px',
                },
                {
                    title: `
                        Soportes
                    `,
                    data: 'documentos_adicionales',
                    className: 'text-right',
                    width: '70px',
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
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaComprobantesContables.draw())
            },
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            ordering: true,
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