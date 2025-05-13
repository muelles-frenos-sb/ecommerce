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
?>

<?php if(count($facturas_invalidas) > 0) { ?>
    <div class="alert alert-danger alert-lg alert-dismissible fade show">
        Te informamos que el número de documento consultado presenta facturas que no se pueden reflejar en este módulo. Por favor, comunícate al teléfono 604 444 7232 - Extensión 110.
    </div>
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

<!-- <div class="alert alert-success alert-lg alert-dismissible fade show"> -->
    <?php
    // echo "¡Bienvenido, <b>$tercero->f200_razon_social</b>! encontramos ".number_format(count($facturas), 0, ',', '.')." facturas pendientes por pagar";
    // if(isset($opciones['busqueda'])) echo " con la búsqueda <b>{$opciones['busqueda']}</b>";
    ?>
<!-- </div> -->

<div class="mt-2 mb-2">
    <button class="btn btn-success btn-md btn-block" onClick="javascript:generarReporte('excel/facturas', {numero_documento: '<?php echo $datos['numero_documento']; ?>'})">
        <i class="fa fa-file-excel"></i>
        Decargar facturas
    </button>
</div>

<style>
    #tabla_facturas {
        font-size: 0.8em;
        font-family: Futura;
    }

    #tabla_facturas th {
        background-color: #19287F;
        color: white;
    }
</style>

<div class="table-responsive">
    <table class="table-striped table-bordered" id="tabla_facturas">
        <thead>
            <tr>
                <th class="text-center">
                    <b><i class="fa fa-plus fa-2x"></i></b>
                </th>
                <th class="text-center">Sede</th>
                <th class="text-center">Doc</th>
                <th class="text-center">Cuota</th>
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
                        <div class="form-check" style="height: 13px;">
                            <input class="form-check-input" type="radio" onClick="javascript:agregarFactura({
                                contador: '<?php echo $contador; ?>',
                                id: '<?php echo $factura->id; ?>',
                                documento_cruce: '<?php echo $factura->Nro_Doc_cruce; ?>',
                                numero_documento: '<?php echo $factura->Cliente; ?>',
                                numero_cuota: '<?php echo $factura->Nro_cuota; ?>',
                                centro_operativo: '<?php echo $factura->CentroOperaciones; ?>',
                                documento_cruce_tipo: '<?php echo $factura->Tipo_Doc_cruce; ?>',
                                valor: `<?php echo number_format($factura->totalCop, 0, '', ''); ?>`,
                                sede: `<?php echo $factura->centro_operativo; ?>`,
                                tipo_credito: `<?php echo $factura->nombre_homologado; ?>`,
                                descuento_porcentaje: `<?php echo $factura->descuento_porcentaje; ?>`,
                                id_sucursal: '<?php echo $factura->sucursal_id; ?>',
                                valor_aplicado: `<?php echo $factura->ValorAplicado; // Enviado para almacenar en el detalle del recibo ?>`,
                                valor_documento: `<?php echo $factura->valorDoc; // Enviado para almacenar en el detalle del recibo ?>`,
                                total_cop: `<?php echo $factura->totalCop; // Enviado para almacenar en el detalle del recibo ?>`,
                            })" style="padding: 2px 5px 2px 5px;">
                        </div>
                    </td>
                    <td>
                        <?php echo $factura->centro_operativo; ?>
                    </td>
                    <td class="text-right">
                        <a href="javascript:;" onClick="javascript:cargarProductos({
                            documento_cruce: '<?php echo $factura->Nro_Doc_cruce; ?>',
                            numero_documento: '<?php echo $factura->Cliente; ?>',
                            id_sucursal: '<?php echo $factura->sucursal_id; ?>',
                        });"><?php echo $factura->Nro_Doc_cruce; ?></a>
                    </td>
                    <td class="text-right"><?php echo $factura->Nro_cuota; ?></td>
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
                        <a href="javascript:;" onClick="javascript:cargarMovimientos({
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
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right"><?php echo formato_precio($total_facturas); ?></td>
                <td class="text-right"><?php echo formato_precio($total_pagado); ?></td>
                <td class="text-right"><?php echo formato_precio($total_saldo); ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    $().ready(() => {
        new DataTable('#tabla_facturas', {
            info: true,
            // ordering: true,
            // order: [[5, 'desc']],
            paging: true,
            // stateSave: true,
            scrollY: '320px',
            searching: true,
            language: {
                decimal: ',',
                thousands: '.'
            },
            language: {
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            scrollX: false,
            scrollCollapse: true,
        })
       
        $('#contenedor_mensaje_carga').html('')
    })
</script>