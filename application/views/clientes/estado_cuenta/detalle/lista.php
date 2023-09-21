<?php
$opciones = [
    'numero_documento' => $datos['numero_documento'],
    'pendientes' => true,
];

$resultado_tercero = json_decode(obtener_terceros_api($datos));
$codigo_resultado_tercero = $resultado_tercero->codigo;
$mensaje_resultado_tercero = $resultado_tercero->mensaje;
$tercero = $resultado_tercero->detalle->Table[0];

if($datos['busqueda'] != '') $opciones['busqueda'] = $datos['busqueda'];

// Obtenemos las facturas del cliente pendientes por pagar
$facturas = $this->clientes_model->obtener('clientes_facturas', $opciones);

if(empty($facturas)) {
    ?>
    <div class="alert alert-success alert-lg alert-dismissible fade show">
        <?php
        echo 'No tienes ninguna factura pendiente por pagar';
        if(isset($opciones['busqueda'])) echo " con la búsqueda <b>{$opciones['busqueda']}</b>";
        exit();
        ?>
    </div>
<?php } ?>

<div class="alert alert-success alert-lg alert-dismissible fade show">
    <?php
    echo "¡Bienvenido, <b>$tercero->f200_razon_social</b>! encontramos ".number_format(count($facturas), 0, ',', '.')." facturas pendientes por pagar";
    if(isset($opciones['busqueda'])) echo " con la búsqueda <b>{$opciones['busqueda']}</b>";
    ?>
</div>

<table class="table-striped" id="tabla_facturas">
    <thead>
        <tr>
            <th class="text-center">Sede</th>
            <th class="text-center">Doc</th>
            <th class="text-center">Fecha fact</th>
            <th class="text-center">Fecha vcto</th>
            <th class="text-center">Días venc</th>
            <th class="text-center">Valor Doc</th>
            <th class="text-center">Abonos</th>
            <th class="text-center">Saldo</th>
            <th class="text-center">Sucursal</th>
            <th class="text-center">Tipo crédito</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_facturas = 0;
        $total_pagado = 0;
        $total_saldo = 0;

        foreach($facturas as $factura) {
            $sucursal = explode(' ', $factura->RazonSocial_Sucursal);

            $total_facturas += $factura->ValorAplicado;
            $total_pagado += $factura->totalCop;
            $total_saldo += $factura->valorDoc;
        ?>
            <tr>
                <td><?php echo $factura->centro_operativo; ?></td>
                <td class="text-right">
                    <button type="button" class="btn btn-sm btn-primary" style="text-decoration:none;" onClick="javascript:cargarProductos({
                        documento_cruce: '<?php echo $factura->Nro_Doc_cruce; ?>',
                        numero_documento: '<?php echo $datos['numero_documento']; ?>'
                    });">Ver (<?php echo $factura->Nro_Doc_cruce; ?>)</button>
                    <!-- <a onClick="javascript:cargarProductos({
                        documento_cruce: '<?php // echo $factura->Nro_Doc_cruce; ?>',
                        numero_documento: '<?php // echo $datos['numero_documento']; ?>'
                    });">
                        <?php echo $factura->Nro_Doc_cruce; ?>
                    </a> -->
                </td>
                <td><?php echo $factura->Fecha_doc_cruce; ?></td>
                <td><?php echo $factura->Fecha_venc; ?></td>
                <td class="text-right">
                    <?php
                    if($factura->diasvencidos > 0) {
                        echo "
                        <div class='status-badge status-badge--style--failure status-badge--has-text'>
                            <div class='status-badge__body'>
                                <div class='status-badge__text'>$factura->diasvencidos</div>
                            </div>
                        </div>
                        ";
                    }
                    ?>
                </td>
                <td class="text-right"><?php echo formato_precio($factura->ValorAplicado);?></td>
                <td class="text-right"><?php echo formato_precio($factura->totalCop); ?></td>
                <td class="text-right"><?php echo formato_precio($factura->valorDoc);?></td>
                <td>
                    <?php echo substr($sucursal[0], 0, 10); ?>
                </td>
                <td><?php echo $factura->nombre_homologado; ?></td>
                <!-- <td>
                    <a type="button" class="btn btn-sm btn-primary" style="text-decoration:none;" href="#">Pagar</a>
                </td> -->
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th class="text-right"><?php echo formato_precio($total_facturas); ?></th>
            <th class="text-right"><?php echo formato_precio($total_pagado); ?></th>
            <th class="text-right"><?php echo formato_precio($total_saldo); ?></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>

<script>
    $().ready(() => {
        new DataTable('#tabla_facturas', {
            info: true,
            ordering: true,
            paging: true,
            stateSave: true,
            // scrollY: '600px',
            searching: false,
            language: {
                decimal: ',',
                thousands: '.'
            },
            language: {
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            scrollX: false,
        });
    });
</script>