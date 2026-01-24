<?php $consecutivos = $this->contabilidad_model->obtener('comprobantes_contables_tareas_detalle', $datos); ?>

<div class="modal fade" id="modal_consecutivos" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo "----"; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="block">
                    <div class="container">
                        <div class="table-responsive">
                            <table class="table-striped table-bordered" id="tabla_facturas" width="100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Consecutivo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($consecutivos as $consecutivo) { ?>
                                        <tr>
                                            <td class="text-right"><?php echo $consecutivo->ruta; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
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
        $('#modal_consecutivos').modal({
            backdrop: 'static',
            keyboard: true
        })
    })
</script>