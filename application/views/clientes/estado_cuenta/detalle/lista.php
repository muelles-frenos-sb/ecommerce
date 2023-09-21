<?php
$opciones = [
    'numero_documento' => $datos['numero_documento'],
    'pendientes' => true,
];

if($datos['busqueda'] != '') $opciones['busqueda'] = $datos['busqueda'];

// Obtenemos las facturas del cliente pendientes por pagar
$facturas = $this->clientes_model->obtener('clientes_facturas', $opciones);

if(empty($facturas)) {
    echo '<div class="alert alert-success alert-lg alert-dismissible fade show">No tienes ninguna factura pendiente por pagar. ¡Gracias por consultar!</div>';
    exit;
}
?>

<div class="alert alert-success alert-lg alert-dismissible fade show">
    <?php
    echo "Encontramos ".number_format(count($facturas), 0, ',', '.')." facturas pendientes por pagar";
    if(isset($opciones['busqueda'])) echo "con la búsqueda <b>{$opciones['busqueda']}</b>";
    ?>
</div>

<table class="table-striped">
    <thead>
        <tr>
            <th class="text-center">Número</th>
            <th class="text-center">Sucursal</th>
            <th class="text-center">Centro Operativo</th>
            <th class="text-center">Auxiliar</th>
            <th class="text-center">Creación</th>
            <th class="text-center">Vencimiento</th>
            <th class="text-center">Vencido</th>
            <th class="text-center">Factura</th>
            <th class="text-center">Pagado</th>
            <th class="text-center">Saldo</th>
            <th class="text-center">Opciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($facturas as $factura) { ?>
            <tr>
                <td class="text-right">
                    <a href="account-order-details.html"><?php echo $factura->Nro_Doc_cruce; ?></a>
                </td>
                <td><?php echo substr($factura->RazonSocial_Sucursal, 0, 10); ?></td>
                <td><?php echo $factura->centro_operativo; ?></td>
                <td><?php echo $factura->Desc_auxiliar; ?></td>
                <td><?php echo $factura->Fecha_doc_cruce; ?></td>
                <td><?php echo $factura->Fecha_venc; ?></td>
                <td>
                    <?php
                    if($factura->diasvencidos > 0) {
                        echo "
                        <div class='status-badge status-badge--style--failure status-badge--has-text'>
                            <div class='status-badge__body'>
                                <div class='status-badge__text'>$factura->diasvencidos días</div>
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
                    <a type="button" class="btn btn-sm btn-primary" style="text-decoration:none;" href="#">Pagar</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>