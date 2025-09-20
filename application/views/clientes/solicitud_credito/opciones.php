<?php $solicitud = $this->clientes_model->obtener("clientes_solicitudes_credito", ["id" => $datos['id']]); ?>

<!-- Contenedor donde se cargará el modal si es aprobada o rechazada la solicitud -->
<div id="contenedor_modal_gestion_solicitud_Credito"></div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <!-- Si la solicitd todavía está en trámite y está sin fecha de envío a firma -->
                    <?php if($solicitud->solicitud_credito_estado_id == 1 && !$solicitud->fecha_envio_firma) { ?>
                        <div class="form-group col-12 col-sm-12">
                            <button class="btn btn-primary btn-block" onClick="javascript:enviarSolicitudAFirma(<?php echo $solicitud->id; ?>)"><i class="fas fa-signature"></i> Enviar para firma</button>
                        </div>
                    <?php } else { ?>
                        <div class="form-group mb-2 alert alert-primary col-lg-12" role="alert">
                            <?php echo "<b>Fecha de envío para firma:</b> $solicitud->fecha_envio_firma"; ?>
                        </div>
                    <?php } ?>

                    <!-- Si la solicitd está pendiente -->
                    <?php if($solicitud->solicitud_credito_estado_id == 1) { ?>
                        <div class="form-group col-lg-6 col-sm-12">
                            <button class="btn btn-danger btn-block" onClick="javascript:rechazarSolicitudCredito(<?php echo $solicitud->id; ?>)"><i class="fas fa-times"></i> Rechazar solicitud</button>
                        </div>

                        <div class="form-group col-lg-6 col-sm-12">
                            <button class="btn btn-success btn-block" onClick="javascript:aprobarSolicitudCredito(<?php echo $solicitud->id; ?>)"><i class="fas fa-check"></i> Aprobar solicitud</button>
                        </div>
                    <?php } else { ?>
                        <div class="form-group mb-2 alert alert-info col-lg-12" role="alert">
                            <?php echo "<b>Estado de la solicitud:</b> $solicitud->estado ($solicitud->motivo_rechazo)"; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    aprobarSolicitudCredito = async (id, confirmacion) => {
        if(!confirmacion) {
            cargarInterfaz('clientes/solicitud_credito/aprobacion', 'contenedor_modal_gestion_solicitud_Credito', {id: id})
            return false
        }
        if(!confirmacion) return false

        if (!validarCamposObligatorios([$('#aprobacion_cupo')])) return false

        let datos = {
            tipo: 'clientes_solicitudes_credito',
            id: id,
            solicitud_credito_estado_id: 2,
            fecha_cierre: true,
            cupo_asignado: parseFloat($('#aprobacion_cupo').val().replace(/\./g, '')),
        }

        await consulta('actualizar', datos)

        mostrarAviso('exito', `Solicitud aprobada exitosamente`, 5000)
    }

    rechazarSolicitudCredito = async (id, confirmacion = null) => {
        if(!confirmacion) {
            cargarInterfaz('clientes/solicitud_credito/rechazo', 'contenedor_modal_gestion_solicitud_Credito', {id: id})
            return false
        }
        
        if (!validarCamposObligatorios([$('#motivo_rechazo_id')])) return false

        let datos = {
            tipo: 'clientes_solicitudes_credito',
            id: id,
            solicitud_credito_estado_id: 3,
            fecha_cierre: true,
            motivo_rechazo_id: $('#motivo_rechazo_id').val(),
        }

        await consulta('actualizar', datos)

        mostrarAviso('exito', `Solicitud rechazada`, 5000)
    }
    
    enviarSolicitudAFirma = async id => {
        confirmacion = await confirmar('enviar', `¿Estás seguro de enviar la solicitud para firma?`)
        if(!confirmacion) return false

        let datos = {
            tipo: 'clientes_solicitudes_credito',
            fecha_envio_firma: true,
            id: id
        }

        await consulta('actualizar', datos)

        mostrarAviso('exito', `Cambios guardados exitosamente`, 5000)
    }
</script>