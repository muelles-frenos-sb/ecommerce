<?php
$opciones = [
    'numero_documento' => $datos['numero_documento'],
    'pendientes' => true,
];

// if($datos['busqueda'] != '') $opciones['busqueda'] = $datos['busqueda'];

// Obtenemos las facturas del cliente pendientes por pagar
$facturas = $this->clientes_model->obtener('clientes_facturas', $opciones);

if(empty($facturas)) {
    ?>
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

<!-- <div class="alert alert-success alert-lg alert-dismissible fade show"> -->
    <?php
    // echo "¡Bienvenido, <b>$tercero->f200_razon_social</b>! encontramos ".number_format(count($facturas), 0, ',', '.')." facturas pendientes por pagar";
    // if(isset($opciones['busqueda'])) echo " con la búsqueda <b>{$opciones['busqueda']}</b>";
    ?>
<!-- </div> -->

<div class="table-responsive">
    <table class="table-striped" id="tabla_facturas">
        <thead>
            <tr>
                <th class="text-center"></th>
                <th class="text-center">Sede</th>
                <th class="text-center">Doc</th>
                <th class="text-center">Fecha fact</th>
                <th class="text-center">Fecha vcto</th>
                <th class="text-center">Días venc</th>
                <th class="text-center">Valor Doc</th>
                <th class="text-center">Abonos</th>
                <th class="text-center">Saldo</th>
                <th class="text-center">Retenciones</th>
                <th class="text-center">Sucursal</th>
                <th class="text-center">Tipo crédito</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_facturas = 0;
            $total_pagado = 0;
            $total_saldo = 0;
            $contador = 1;

            foreach($facturas as $factura) {
                $sucursal = explode(' ', $factura->RazonSocial_Sucursal);

                $total_facturas += $factura->ValorAplicado;
                $total_pagado += $factura->valorDoc;
                $total_saldo += $factura->totalCop;
            ?>
                <tr id="factura_<?php echo $contador; ?>">
                    <td>
                        <button type="button" class="btn btn-success" onClick="javascript:agregarFactura({
                            contador: '<?php echo $contador; ?>',
                            id: '<?php echo $factura->id; ?>',
                            documento_cruce: '<?php echo $factura->Nro_Doc_cruce; ?>',
                            valor: `<?php echo $factura->totalCop; ?>`,
                            sede: `<?php echo $factura->centro_operativo; ?>`,
                            tipo_credito: `<?php echo $factura->nombre_homologado; ?>`,
                        })">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>
                    <td>
                        <?php echo $factura->centro_operativo; ?>
                    </td>
                    <td class="text-right">
                        <a href="javascript:;" onClick="javascrip:cargarProductos({
                            documento_cruce: '<?php echo $factura->Nro_Doc_cruce; ?>',
                            numero_documento: '<?php echo $factura->Cliente; ?>',
                            id_sucursal: '<?php echo $factura->sucursal_id; ?>',
                        });"><?php echo $factura->Nro_Doc_cruce; ?></a>
                    </td>
                    <td><?php echo $factura->Fecha_doc_cruce; ?></td>
                    <td><?php echo $factura->Fecha_venc; ?></td>
                    <td class="text-right">
                        <?php
                        if($factura->dias_vencido > 0) {
                            echo "
                            <div class='status-badge status-badge--style--failure status-badge--has-text'>
                                <div class='status-badge__body'>
                                    <div class='status-badge__text'>$factura->dias_vencido</div>
                                </div>
                            </div>
                            ";
                        } else {
                            echo 0;
                        }
                        ?>
                    </td>
                    <td class="text-right"><?php echo formato_precio($factura->ValorAplicado);?></td>
                    <td class="text-right"><?php echo formato_precio($factura->valorDoc);?></td>
                    <td class="text-right"><?php echo formato_precio($factura->totalCop); ?></td>
                    <td class="text-center">
                        <a href="javascript:;" onClick="javascrip:cargarMovimientos({
                            documento_cruce: '<?php echo $factura->Nro_Doc_cruce; ?>',
                            numero_documento: '<?php echo $factura->Cliente; ?>',
                            id_sucursal: '<?php echo $factura->sucursal_id; ?>',
                        });">Ver</a>
                    </td>
                    <td>
                        <?php echo substr($sucursal[0], 0, 10); ?>
                    </td>
                    <td><?php echo $factura->nombre_homologado; ?></td>
                </tr>
            <?php
                $contador++;
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th></th>
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
</div>

<script>
    $().ready(() => {
        new DataTable('#tabla_facturas', {
            info: true,
            ordering: true,
            paging: true,
            stateSave: true,
            // scrollY: '600px',
            searching: true,
            language: {
                decimal: ',',
                thousands: '.'
            },
            language: {
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            scrollX: false,
        })
       
        $('#contenedor_mensaje_carga').html('')
    })
</script>