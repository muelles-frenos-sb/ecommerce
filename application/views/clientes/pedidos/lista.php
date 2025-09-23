<style>
    #tabla_pedidos tbody td {
        font-size: 0.7em;
        padding: 5px;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_pedidos"></table>

<script>
    $().ready(async () => {
        var tablaPedidos = $("#tabla_pedidos").DataTable({
            ajax: {
                url: `${$("#site_url").val()}clientes/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'pedidos'
                },
            },
            columns: [
                { title: 'Pedido', data: 'numero_documento' },
                { title: 'Id Consecutivo', data: 'consecutivo_id' },
                { title: 'Nombre Consecutivo', data: 'consecutivo_nombre' },
                { title: 'Fecha', data: 'fecha_documento' },
                { title: 'NIT', data: 'nit' },
                { title: 'Nombre', data: 'rzon_social' },
                { title: 'Productos', data: 'cantidad_productos', className: 'text-right' },
                { title: 'Estado', data: 'ultimo_estado' },
                {
                    title: 'Opciones', 
                    data: null,
                    render: (pedido, type, row) => {
                        return `
                            <td class="p-1">
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
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaRecibos.draw())
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
            searching: false,
            serverSide: true,
            stateSave: false,
        })
    })
</script>