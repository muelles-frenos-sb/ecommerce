<?php
// Obtenemos las facturas del cliente pendientes por pagar
$facturas = $this->clientes_model->obtener('clientes_facturas', [
    'numero_documento' => $datos['numero_documento'],
    'pendientes' => true,
]);

if(empty($facturas)) {
    echo '<div class="alert alert-success alert-lg alert-dismissible fade show">No tienes ninguna factura pendiente por pagar. ¡Gracias por consultar!</div>';
    exit;
}
?>

<!-- <div class="card-divider"></div> -->
<div class="card-table">
    <div class="alert alert-success alert-lg alert-dismissible fade show">
        <?php echo "Encontramos ".number_format(count($facturas), 0, ',', '.')." facturas pendientes por pagar:" ?>
    </div>
    <div class="table-responsive-sm">
        <table class="table-striped">
            <thead>
                <tr>
                    <th class="text-center">Número</th>
                    <th class="text-center">Sucursal</th>
                    <th class="text-center">Fechas</th>
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
                        <td>
                            <?php echo $factura->RazonSocial_Sucursal; ?>
                            <div class="vehicles-list__item-details">
                                <?php echo "<b>C. Operativo:</b> $factura->CentroOperaciones"; ?><br>
                                <?php echo "<b>Auxiliar:</b> $factura->Desc_auxiliar"; ?>
                            </div>
                        </td>
                        <td>
                            <div class="vehicles-list__item-details">
                                <?php echo "<b>Creación:</b> $factura->Fecha_doc_cruce"; ?><br>
                                <?php echo "<b>Vencimiento:</b> $factura->Fecha_venc"; ?><br>
                                <?php
                                if($factura->diasvencidos > 0) {
                                    echo "
                                    <div class='status-badge status-badge--style--failure status-badge--has-text'>
                                        <div class='status-badge__body'>
                                            <div class='status-badge__text'>$factura->diasvencidos días vencida</div>
                                        </div>
                                    </div>
                                    ";
                                }
                                ?>
                            </div>
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
    </div>
</div>
<div class="card-divider"></div>
<div class="card-footer">
    <!-- <ul class="pagination">
        <li class="page-item disabled">
            <a class="page-link page-link--with-arrow" href="" aria-label="Previous">
                <span class="page-link__arrow page-link__arrow--left" aria-hidden="true"><svg width="7" height="11">
                        <path d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z" />
                    </svg>
                </span>
            </a>
        </li>
        <li class="page-item"><a class="page-link" href="#">1</a></li>
        <li class="page-item active" aria-current="page">
            <span class="page-link">
                2
                <span class="sr-only">(current)</span>
            </span>
        </li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item"><a class="page-link" href="#">4</a></li>
        <li class="page-item page-item--dots">
            <div class="pagination__dots"></div>
        </li>
        <li class="page-item"><a class="page-link" href="#">9</a></li>
        <li class="page-item">
            <a class="page-link page-link--with-arrow" href="" aria-label="Next">
                <span class="page-link__arrow page-link__arrow--right" aria-hidden="true"><svg width="7" height="11">
                        <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9 C-0.1,9.8-0.1,10.4,0.3,10.7z" />
                    </svg>
                </span>
            </a>
        </li>
    </ul> -->
</div>