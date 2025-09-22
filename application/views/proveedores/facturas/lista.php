<style>
    #tabla_cuentas_por_pagar tbody td {
        font-size: 0.7em;
        padding: 5px;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_cuentas_por_pagar"></table>

<script>
    $().ready(async () => {
        let tablaCuentasPorPagar = $("#tabla_cuentas_por_pagar").DataTable({
            ajax: {
                url: `${$("#site_url").val()}proveedores/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'api_cuentas_por_pagar'
                    numero_documento = <?php echo $datos['numero_documento']; ?>
                },
            },
            columns: [
                {
                    title: `f353_rowid`,
                    data: 'f353_rowid',
                    className: 'text-right',
                },
                {
                    title: 'f353_consec_docto_cruce',
                    data: 'f353_consec_docto_cruce',
                    className: 'text-right',
                },
                { title: 'f353_fecha', data: 'f353_fecha' },
                { 
                    title: 'f353_total_cr',
                    data: 'f353_total_cr',
                    data: null,
                    className: 'text-right',
                    render: (cuenta, type, row) => {
                        return parseFloat(cuenta.f353_total_cr).toLocaleString('es-CO')
                    }
                },
                { 
                    title: 'f353_total_db',
                    data: null,
                    className: 'text-right',
                    render: (cuenta, type, row) => {
                        return parseFloat(cuenta.f353_total_db).toLocaleString('es-CO')
                    }
                },
                { title: 'f353_notas', data: 'f353_notas' },
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