<style>
    #tabla_pedidos tbody td {
        font-size: 0.7em;
        padding: 5px;
        vertical-align: middle;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_pedidos"></table>

<!-- Modal vacío -->
<div class="modal fade" id="modal_pedido_detalle" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalle del pedido</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_pedido_body">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin"></i> Cargando...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $().ready(async () => {
        tablaPedidos = $("#tabla_pedidos").DataTable({
            ajax: {
                url: `${$("#site_url").val()}logistica/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'pedidos'

                    datos.filtros_personalizados = {
                        fecha: $('#filtro_fecha').val(),
                        numero: $('#filtro_numero').val(),
                        nit: $('#filtro_nit').val(),
                        razon_social: $('#filtro_razon_social').val(),
                        orden_compra: $('#filtro_orden_compra').val(),
                        creador: $('#filtro_creador').val(),
                        items: $('#filtro_items').val(),
                        valor: $('#filtro_valor').val()
                    }
                }
            },
            columns: [
                { 
                    title: `
                        Fecha
                        <input type="date" id="filtro_fecha" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'fecha',
                },
                { 
                    title: `
                        Numero
                        <input type="text" id="filtro_numero" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'numero'
                },
                { 
                    title: `
                        Nit
                        <input type="number" id="filtro_nit" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'nit'
                },
                { 
                    title: `
                        Razón Social
                        <input type="text" id="filtro_razon_social" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'razon_social'
                },
                { 
                    title: `
                        Orden de compra
                        <input type="text" id="filtro_orden_compra" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'orden_compra'
                },
                { 
                    title: `
                        Creador
                        <input type="text" id="filtro_creador" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'creador'
                },
                { 
                    title: `
                        Ítems
                        <input type="number" id="filtro_items" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'items',
                    className: 'dt-right'
                },
                {
                    title: `
                        Valor
                        <input type="number" id="filtro_valor" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'valor',
                    className: 'dt-right',
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            return parseFloat(data || 0).toLocaleString('es-CO');
                        }
                        return data; // para ordenar correctamente
                    }
                },
                {
                    title: 'Acciones',
                    data: null, 
                    orderable: false,
                    render: (data, type, row) => {
                        return `
                            <div class="dt-buttons-gap">
                                <button class="btn btn-sm btn-primary btn-ver-pedido" data-id="${data.f430_rowid}" title="Ver pedido">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        `
                    }
                }
            ],
            columnDefs: [
                { targets: '_all', className: 'dt-head-center p-1' }
            ],
            deferRender: true,
            fixedHeader: true,
            info: true,
            initComplete: function () {
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaPedidos.draw())
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

        // Abrir modal con detalle del pedido
        $(document).on('click', '.btn-ver-pedido', function() {
            const id = $(this).data('id')

            $('#modal_pedido_body').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando...</div>')
            $('#modal_pedido_detalle').modal('show')

            $.get(`${$("#site_url").val()}logistica/pedidos/detalle/${id}`, function(response) {
                $('#modal_pedido_body').html(response)
            })
        })
    })
</script>