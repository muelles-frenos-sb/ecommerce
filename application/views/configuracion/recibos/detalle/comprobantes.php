<?php $recibo = $this->productos_model->obtener('recibo', ['token' => $datos['token']]); ?>

<div class="block">
    <div class="container container--max--xl">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="text-center">Facturas asociadas de <?php echo $recibo->razon_social; ?></h5>
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
                                <th>Descuento</th>
                                <th>Valor pagado</th>
                                <th>Valor saldo</th>
                                <th>Sucursal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $subtotal_valor_documento = 0;
                            $subtotal_valor_descuento = 0;
                            $subtotal_valor_pagado = 0;
                            $subtotal_valor_saldo = 0;

                            foreach($recibo_detalle as $item) {
                                $factura_cliente = $this->clientes_model->obtener('clientes_facturas', [
                                    'Cliente' => $recibo->documento_numero,
                                    // 'Tipo_Doc_cruce' => $item->documento_cruce_tipo,
                                    'Nro_Doc_cruce' => $item->documento_cruce_numero,
                                ]);

                                $subtotal_valor_documento += $factura_cliente->ValorAplicado;
                                $subtotal_valor_descuento += $item->descuento;
                                $subtotal_valor_pagado += $item->subtotal - $item->descuento;
                                $subtotal_valor_saldo += $factura_cliente->ValorAplicado - $item->subtotal;
                                ?>
                                <tr>
                                    <!-- Sede -->
                                    <td><?php echo $factura_cliente->centro_operativo; ?></td>
                                    
                                    <!-- Documento cruce -->
                                    <td class="text-right"><?php echo $factura_cliente->Nro_Doc_cruce; ?></td>

                                    <!-- Fecha factura -->
                                    <td><?php echo $factura_cliente->Fecha_doc_cruce; ?></td>
                                    
                                    <!-- Valor documento -->
                                    <td class="text-right"><?php echo formato_precio($factura_cliente->ValorAplicado); ?></td>
                                    
                                    <!-- Valor descuento -->
                                    <td class="text-right"><?php echo formato_precio($item->descuento); ?></td>
                                    
                                    <!-- Valor pagado -->
                                    <td class="text-right"><?php echo formato_precio($item->subtotal - $item->descuento); ?></td>
                                    
                                    <!-- Valor saldo -->
                                    <td class="text-right"><?php echo formato_precio($factura_cliente->ValorAplicado - $item->subtotal); ?></td>
                                    
                                    <!-- Sucursal -->
                                    <td><?php echo $factura_cliente->nombre_homologado; ?></td>
                                </tr>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right"><b><?php echo formato_precio($subtotal_valor_documento); ?></b></td>
                            <td class="text-right"><b><?php echo formato_precio($subtotal_valor_descuento); ?></b></td>
                            <td class="text-right"><b><?php echo formato_precio($subtotal_valor_pagado); ?></b></td>
                            <td class="text-right"><b><?php echo formato_precio($subtotal_valor_saldo); ?></b></td>
                            <td></td>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-4">
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
                                    $archivo_completo = base_url()."archivos/recibos/$recibo->id/".basename($archivo);
                                ?>
                                    <tr>
                                        <td><?php echo $contador++; ?></a></td>
                                        <td><?php echo basename($archivo); ?></a></td>
                                        <td>
							                <a class="mb-2" target="_blank" onClick="window.open('<?php echo $archivo_completo; ?>', this.target, 'width=800,height=600'); return false;" title="Ver comprobante" style="cursor: pointer;">Previsualizar</a> |
                                            
                                            <a class="mb-2" href="<?php echo $archivo_completo; ?>" download>Descargar</a>
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

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="text-center">Distribución del pago</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-body">
                <div id="contenedor_cuentas"></div>

                <div class="mt-2 mb-2 d-flex flex-column">
                    <input type="hidden" id="total_faltante_amortizacion" value="<?php echo number_format($subtotal_valor_pagado, 0, '', ''); ?>">
                    <h4 class="align-self-end">Total amortizado: $<span id="total_pago_amortizacion">0</span></h4>
                    <h4 class="align-self-end">Diferencia: $<span id="total_faltante_amortizacion_formato">0</span></h4>
                </div>
                
                <!-- Si está por validar el comprobante -->
                <?php if($recibo->recibo_estado_id == 3) { ?>
                    <a class="btn btn-info btn-block mt-2" href="javascript:;" onClick="javascript:agregarCuenta(<?php echo $recibo->id; ?>);">
                        Agregar cuenta
                    </a>
                <?php } ?>
            </div>
            <div class="card-divider"></div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-4">
                        <button class="btn btn-info" href=":;" onclick="history.back()">Volver a recibos</button>
                    </div>

                    <!-- Si está por validar el comprobante -->
                    <?php if($recibo->recibo_estado_id == 3) { ?>
                        <div class="col-4">
                            <a class="btn btn-danger btn-block" href="javascript:;" onClick="javascript:rechazarPago(<?php echo $recibo->id; ?>)">Rechazar pago</a>
                        </div>
                        <div class="col-4">
                            <a class="btn btn-success btn-block" href="javascript:;" onClick="javascript:aprobarPago(<?php echo $recibo->id; ?>)">Aprobar pago</a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    $(`.facturas_items`).addClass('account-nav__item--active')

    $().ready(() => calcularTotalAmortizacion())
</script>