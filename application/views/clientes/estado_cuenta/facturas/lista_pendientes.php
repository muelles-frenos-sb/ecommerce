<?php
// Obtenemos las facturas del cliente pendientes por pagar
$facturas = $this->clientes_model->obtener('clientes_facturas', [
    'numero_documento' => $datos['numero_documento'],
    'pendientes' => true,
    'mostrar_estado_cuenta'=> true,
]);

$facturas_invalidas = $this->clientes_model->obtener('clientes_facturas', [
    'numero_documento' => $datos['numero_documento'],
    'pendientes' => true,
    'mostrar_alerta'=> true,
]);

$recibos_pendientes_por_aplicar = $this->configuracion_model->obtener('recibos_detalle', ['documento_numero' => $datos['numero_documento'], 'recibo_estado_id' => 3]);
?>

<!-- Si tiene alguna factura no válida para pago en línea -->
<?php if(count($facturas_invalidas) > 0) { ?>
    <div class="alert alert-danger alert-lg alert-dismissible fade show">
        Te informamos que el número de documento consultado presenta facturas que no se pueden reflejar en este módulo. Por favor, comunícate al teléfono 604 444 7232 - Extensión 110.
    </div>
<!-- Si no tiene facturas pendientes por pagar -->
<?php } elseif(empty($facturas)) { ?>
    <script>
        $('#contenedor_mensaje_carga').html('')
        $('#contenedor_carrito_facturas').hide('')
    </script>

    <div class="alert alert-success alert-lg alert-dismissible fade show">
        <?php
        echo 'No tienes ninguna factura pendiente por pagar';
        if(isset($opciones['busqueda'])) echo " con la búsqueda <b>{$opciones['busqueda']}</b>";
        exit();
        ?>
    </div>
<?php } ?>

<div class="mt-2 mb-2">
    <button class="btn btn-success btn-md btn-block" onClick="javascript:generarReporte('excel/facturas', {numero_documento: '<?php echo $datos['numero_documento']; ?>'})">
        <i class="fa fa-file-excel"></i>
        Descargar listado de facturas o estado de cuenta
    </button>

    <!-- Si tiene recibos pendientes por aplicar -->
    <?php if(!empty($recibos_pendientes_por_aplicar)) { ?>
        <button class="btn btn-secondary btn-md btn-block" id="btn_recibos_pendientes" onClick="javascript:cargarRecibosPorProcesar({numero_documento: '<?php echo $datos['numero_documento']; ?>'})">
            <i class="fa fa-search"></i>
            Ver recibos pendientes por procesar en el ERP
        </button>
    <?php } ?>
</div>

<style>
    #tabla_facturas_pendientes tbody td {
        font-size: 0.7em;
        padding: 5px;
    }

    #tabla_facturas_pendientes thead th {
        background-color: #19287F;
        color: white;
    }
</style>

<div class="table-responsive">
    <table class="table-striped table-bordered" id="tabla_facturas_pendientes"></table>
</div>

<script>
    $().ready(() => {        
        var tablaFacturasPendientes = $("#tabla_facturas_pendientes").DataTable({
            ajax: {
                url: `${$("#site_url").val()}clientes/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'facturas_pendientes'
                    datos.numero_documento = '<?php echo $datos['numero_documento']; ?>'
                    datos.pendientes = true
                    datos.mostrar_estado_cuenta = true

                    // Filtros personalizados
                    datos.filtros_personalizados = {
                        sede: $('#filtro_sede').val(),
                        documento_cruce: $('#filtro_documento_cruce').val(),
                        cuota: $('#filtro_cuota').val(),
                        fecha: $('#filtro_fecha').val(),
                        fecha_vencimiento: $('#filtro_fecha_vencimiento').val(),
                        dias_vencido: $('#filtro_dias_vencido').val(),
                        valor_documento: $('#filtro_valor_valor_documento').val(),
                        valor_abonos: $('#filtro_valor_abonos').val(),
                        valor_saldo: $('#filtro_valor_saldo').val(),
                        sucursal: $('#filtro_sucursal').val(),
                        tipo_credito: $('#filtro_tipo_credito').val(),
                    }
                },
            },
            columns: [
                { 
                    title: `<i class="fa fa-plus fa-2x"></i>`,
                    data: null,
                    className: 'text-center',
                    render: (factura, type, row) => {
                        return `
                            <div class="form-check" style="height: 20px;">
                                <input class="form-check-input" type="radio" onClick="javascript:agregarFactura({
                                    contador: null,
                                    id: '${factura.id}',
                                    documento_cruce: '${factura.Nro_Doc_cruce}',
                                    numero_documento: '${factura.Cliente}',
                                    fecha_documento: '${factura.Fecha_doc_cruce}',
                                    dias_vencido: '${factura.dias_vencido}',
                                    fecha_vencimiento: '${factura.Fecha_venc}',
                                    numero_cuota: '${factura.Nro_cuota}',
                                    centro_operativo: '${factura.CentroOperaciones}',
                                    documento_cruce_tipo: '${factura.Tipo_Doc_cruce}',
                                    documento_cruce_fecha: '${factura.Fecha_doc_cruce}',
                                    valor: '${parseInt(factura.totalCop)}',
                                    sede: '${factura.centro_operativo}',
                                    tipo_credito: '${factura.nombre_homologado}',
                                    descuento_porcentaje: '${factura.descuento_porcentaje}',
                                    id_sucursal: '${factura.sucursal_id}',
                                    valor_aplicado: '${factura.ValorAplicado}', // Enviado para almacenar en el detalle del recibo,
                                    valor_documento: '${factura.valorDoc}', // Enviado para almacenar en el detalle del recibo,
                                    total_cop: '${factura.totalCop}' // Enviado para almacenar en el detalle del recibo,
                                })">
                            </div>
                        `
                    }
                },
                { 
                    title: `Recibo pendiente`,
                    data: null,
                    render: (factura, type, row) => {
                        // Si está pendiente por aplicar
                        return (factura.por_aplicar_archivo_pendiente)
                        ? `
                            <a
                                class="mb-2"
                                target="_blank"
                                onClick="window.open('${$('base_url').val()}/archivos/recibos/${factura.por_aplicar_archivo_pendiente}', this.target, 'width=800,height=600'); return false;"
                                title="Ver comprobante"
                                style="cursor: pointer;"
                            >
                                <i class="fa fa-search"></i>
                            </a>
                        `
                        : ``
                    }
                },
                {
                    title: `
                        Sede
                        <input type="text" id="filtro_sede" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'centro_operativo'
                },
                { 
                    title: `
                        Doc
                        <input type="text" id="filtro_documento_cruce" class="form-control form-control-sm border-secondary">
                    `,
                    data: null,
                    className: 'text-right',
                    render: (factura, type, row) => {
                        return `
                            <a href="javascript:;" onClick="javascript:cargarProductos({
                                documento_cruce: '${factura.Nro_Doc_cruce}',
                                numero_documento: '${factura.Cliente}',
                                id_sucursal: '${factura.sucursal_id}',
                            });">${factura.Nro_Doc_cruce}</a>
                        `
                    }
                },
                {
                    title: `
                        Cuota
                        <input type="number" id="filtro_cuota" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'Nro_cuota',
                    className: 'text-right',
                },
                {
                    title: `
                        Fecha fact
                        <input type="date" id="filtro_fecha" class="form-control form-control-sm border-secondary" style='width: 100px;'>
                    `,
                    data: 'Fecha_doc_cruce'
                },
                {
                    title: `
                        Fecha Vcto
                        <input type="date" id="filtro_fecha_vencimiento" class="form-control form-control-sm border-secondary" style='width: 100px;'>
                    `,
                    data: 'Fecha_venc'
                },
                { 
                    title: `
                        Dias venc
                        <input type="number" id="filtro_dias_vencido" class="form-control form-control-sm border-secondary">
                    `,
                    data: null,
                    className: 'text-right',
                    render: (factura, type, row) => {
                        // Si está pendiente por aplicar
                        return (parseInt(factura.dias_vencido) > 0) ? `
                            <div class='status-badge status-badge--style--failure status-badge--has-text'>
                                <div class='status-badge__body'>
                                    <div class='status-badge__text'>${factura.dias_vencido}</div>
                                </div>
                            </div>
                        ` : 0
                    }
                },
                {
                    title: `
                        Valor Doc
                        <input type="number" id="filtro_valor_valor_documento" class="form-control form-control-sm border-secondary">
                    `, 
                    data: null,
                    className: 'text-right',
                    render: (factura, type, row) => {
                        return `$ ${Math.round(parseFloat(factura.ValorAplicado)).toLocaleString('es-CO')}`
                    }
                },
                {
                    title: `
                        Abonos
                        <input type="number" id="filtro_valor_abonos" class="form-control form-control-sm border-secondary">
                    `, 
                    data: null,
                    className: 'text-right',
                    render: (factura, type, row) => {
                        let saldo = `$ ${Math.round(parseFloat(factura.valorDoc)).toLocaleString('es-CO')}`

                        // Si está pendiente por aplicar
                        return (parseFloat(factura.totalCop) < 0) ? `
                            <div class='status-badge status-badge--style--failure status-badge--has-text'>
                                <div class='status-badge__body'>
                                    <div class='status-badge__text'>${saldo}</div>
                                </div>
                            </div>
                        ` : 0
                    }
                },
                {
                    title: `
                        Saldo
                        <input type="number" id="filtro_valor_saldo" class="form-control form-control-sm border-secondary">
                    `, 
                    data: null,
                    className: 'text-right',
                    render: (factura, type, row) => {
                        return `$ ${Math.round(parseFloat(factura.totalCop)).toLocaleString('es-CO')}`
                    }
                },
                { 
                    title: `Retenciones`,
                    data: null,
                    render: (factura, type, row) => {
                        return `
                            <a href="javascript:;" onClick="javascript:cargarMovimientos({
                                documento_cruce: ${factura.Nro_Doc_cruce},
                                numero_documento: ${factura.Cliente},
                                id_sucursal: ${factura.sucursal_id},
                            });">Ver</a>
                        `
                    }
                },
                {
                    title: `
                        Sucursal
                        <input type="text" id="filtro_sucursal" class="form-control form-control-sm border-secondary">
                    `,
                    data: null,
                    render: (factura, type, row) => {
                        return factura.RazonSocial_Sucursal.split(' ')[0]
                    }
                },
                {
                    title: `
                        Tipo crédito
                        <input type="text" id="filtro_tipo_credito" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'nombre_homologado'
                },
            ],
            createdRow: function(row, data, dataIndex) {
                // Agregar id al tr
                $(row).attr('id', `factura_${data.id}`);
                $(row).css('height', '40px');
            },
            deferRender: true,
            fixedHeader: true,
            info: true,
            initComplete: function () {
                // Cuando un campo de filtro personalizado cambie, se redibuja la tabla
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaFacturasPendientes.draw())

                // Evita que al dar clic en los filtros personalizados, se aplique el ordenamiento
                // $(`input[id^='filtro_'], select[id^='filtro_']`).on('click', e => {
                //     e.stopPropagation()
                //     e.stopImmediatePropagation()
                //     return false
                // })

                // Aplicar estilos al encabezado
                $(this.api().table().header()).find('th').css({
                    'height': '60px',
                    'background-color': '#19287F',
                    'color': 'white',
                    'font-weight': 'bold',
                    'vertical-align': 'middle',
                    'text-align': 'center'
                })
            },
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            ordering: false,
            orderCellsTop: false,
            pageLength: 100,
            paging: true,
            processing: true,
            scrollCollapse: true,
            scroller: true,
            scrollX: false,
            scrollY: '320px',
            searching: true,
            serverSide: true,
            stateSave: false,
        })

        $('#contenedor_mensaje_carga').html('')
        if($('#pago_con_comprobante').val() != 1) $("#btn_recibos_pendientes").hide()
    })
</script>