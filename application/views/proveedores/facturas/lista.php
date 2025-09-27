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
                    datos.numero_documento = <?php echo $datos['numero_documento']; ?>
                },
            },
            columns: [
                {
                    title: `Id`,
                    data: 'row_id',
                    className: 'text-right',
                },
                {
                    title: 'Sede',
                    data: 'sede',
                },
                {
                    title: 'Documento',
                    data: 'documento_cruce',
                    className: 'text-right',
                },
                { title: 'Fecha', data: 'fecha' },
                { 
                    title: 'Valor',
                    data: 'valor_documento',
                    data: null,
                    className: 'text-right',
                    render: (cuenta, type, row) => {
                        return `$${parseFloat(cuenta.valor_documento).toLocaleString('es-CO')}`
                    }
                },
                { 
                    title: 'Abonos',
                    data: null,
                    className: 'text-right',
                    render: (cuenta, type, row) => {
                        return `$${parseFloat(cuenta.valor_abonos).toLocaleString('es-CO')}`
                    }
                },
                { 
                    title: 'Saldo',
                    data: null,
                    className: 'text-right',
                    render: (cuenta, type, row) => {
                        return `$${parseFloat(cuenta.valor_saldo).toLocaleString('es-CO')}`
                    }
                },
                { title: 'Notas', data: 'notas' },
                {
                    title: 'Opciones',
                    data: null, 
                    render: (cuenta, type, row) => {
                        return (cuenta.valor_saldo == 0)
                        ? `
                            <div class="p-1">
                                <a type="button" class="btn btn-sm btn-danger" href="${$("#site_url").val()}reportes/pdf/comprobante_egreso/${cuenta.id}" title="Descargar comprobante de egreso">
                                    <i class="fa fa-file-download"></i>
                                </a>
                            </div>
                        `
                        : ''
                    }
                }
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
            ordering: true,
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