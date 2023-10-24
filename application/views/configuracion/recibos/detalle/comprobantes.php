<div class="block">
    <div class="container container--max--xl">
        <div class="card">
            <div class="card-header">
                <h5 class="text-center">Facturas asociadas</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-table">
                <div class="table-responsive-sm">
                    <table>
                        <thead>
                            <tr>
                                <th>Sede</th>
                                <th>Documento cruce</th>
                                <th>Fecha factura</th>
                                <th>Valor documento</th>
                                <th>Valor pagado</th>
                                <th>Valor saldo</th>
                                <th>Sucursal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recibo_detalle as $item) {
                                $factura_cliente = $this->clientes_model->obtener('clientes_facturas', [
                                    'Tipo_Doc_cruce' => $item->documento_cruce_tipo,
                                    'Nro_Doc_cruce' => $item->documento_cruce_numero,
                                ]);
                                ?>
                                <tr>
                                    <td><?php echo $factura_cliente->centro_operativo; ?></td>
                                    <td class="text-right"><?php echo $factura_cliente->Nro_Doc_cruce; ?></td>
                                    <td><?php echo $factura_cliente->Fecha_doc_cruce; ?></td>
                                    <td class="text-right"><?php echo formato_precio($factura_cliente->ValorAplicado); ?></td>
                                    <td class="text-right"><?php echo formato_precio($item->subtotal); ?></td>
                                    <td class="text-right"><?php echo formato_precio($factura_cliente->ValorAplicado - $item->subtotal); ?></td>
                                    <td><?php echo $factura_cliente->nombre_homologado; ?></td>
                                </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-divider"></div>
        <div class="card">
            <div class="card-header">
                <h5 class="text-center">Comprobantes</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-table">
                <div class="table-responsive-sm">
                    <table>
                        <thead>
                            <tr>
                                <th>Nro.</th>
                                <th>Nombre</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($recibo->archivos) {
                                $contador = 1;
                                $archivos = glob("./archivos/recibos/$recibo->id/*");

                                foreach ($archivos as $archivo) {
                                ?>
                                    <tr>
                                        <td><?php echo $contador++; ?></a></td>
                                        <td><?php echo basename($archivo); ?></a></td>
                                        <td>
                                            <a class="mb-2" href="<?php echo base_url()."archivos/recibos/$recibo->id/".basename($archivo); ?>" download>Descargar</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                        <tfoot>

                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-divider"></div>
        </div>
        <div class="card-divider"></div>
        <div class="card">
            <div class="card-header">
                <h5 class="text-center">Distribución del pago</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-body">
                <div id="contenedor_cuentas"></div>
                <a class="btn btn-info btn-block mt-2" href="javascript:;" onClick="javascript:agregarCuenta(<?php echo $recibo->id; ?>);">
                    Agregar cuenta
                </a>
            </div>
            <div class="card-divider"></div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-4">
                        <button class="btn btn-info" href=":;" onclick="history.back()">Volver a recibos</button>
                    </div>
                    <div class="col-4">
                        <a class="btn btn-danger btn-block" href="javascript:;">Rechazar pago</a>
                    </div>
                    <div class="col-4">
                        <a class="btn btn-success btn-block" href="javascript:;" onClick="javascript:aprobarPago(<?php echo $recibo->id; ?>)">Aprobar pago</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    $(`.facturas_items`).addClass('account-nav__item--active')
</script>