<?php $facturas_pendientes_por_aplicar = $this->configuracion_model->obtener('recibos_detalle', ['documento_numero' => $datos['numero_documento'], 'recibo_estado_id' => 3]); ?>

<div class="modal fade" id="modal_por_procesar" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo "Facturas pendientes por procesar en el ERP"; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="block">
                    <div class="container container--max--xl">
                        <?php if(empty($facturas_pendientes_por_aplicar)) echo 'La factura no tiene retenciones'; ?>

                        <?php if(!empty($facturas_pendientes_por_aplicar)) { ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">#</th>
                                        <th scope="col" class="text-center">DOC</th>
                                        <th scope="col" class="text-center">Cuota</th>
                                        <th scope="col" class="text-center">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 1;

                                    foreach ($facturas_pendientes_por_aplicar as $factura) {
                                    ?>
                                        <tr>
                                            <th scope="row" class="text-right"><?php echo $contador++; ?></th>
                                            <td class="text-right"><?php echo $factura->documento_cruce_numero; ?></td>
                                            <td class="text-right"><?php echo $factura->cuota_numero; ?></td>
                                            <td class="text-right"><?php echo formato_precio($factura->subtotal - $factura->descuento); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
     $().ready(function() {
        $('#modal_por_procesar').modal({
            backdrop: 'static',
            keyboard: true
        })
    })
</script>