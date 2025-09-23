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
    var solicitud = JSON.parse('<?php echo json_encode($solicitud) ?>')

    var datosBitacora = {
        tipo: 'clientes_solicitudes_credito_bitacora',
        solicitud_id: solicitud.id,
        usuario_id: $('#sesion_usuario_id').val()
    }

    aprobarSolicitudCredito = async (id, confirmacion) => {
        if(!confirmacion) {
            cargarInterfaz('clientes/solicitud_credito/aprobacion', 'contenedor_modal_gestion_solicitud_Credito', {id: id})
            return false
        }

        if (!validarCamposObligatorios([$('#aprobacion_cupo'), $('#aprobacion_responsable_iva')])) return false

        let datos = {
            tipo: 'clientes_solicitudes_credito',
            id: id,
            solicitud_credito_estado_id: 2,
            fecha_cierre: true,
            cupo_asignado: $('#aprobacion_cupo').val(),
        }

        await consulta('actualizar', datos)

        // Creación del registro en bitácora
        datosBitacora.observaciones = `Solicitud aprobada`
        await consulta('crear', datosBitacora)

        Swal.fire({
            title: 'Creando el tercero en el ERP...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        // Se consulta en el ERP el tercero
        var consultaTercero = await consulta('obtener', {tipo: 'terceros', numero_documento: solicitud.documento_numero}, false)

        // Si el tercero ya existe en el ERP
        if(consultaTercero.codigo == 0) {
            // Se va a actualizar el tercero
            // ----------------------------------------
        }

        let responsableIVA = $('#aprobacion_responsable_iva option:selected').attr('data-responsable_iva')
        let causanteIVA = $('#aprobacion_responsable_iva option:selected').attr('data-causante_iva')
        
        let datosTerceroSiesa = {
            responsable_iva: responsableIVA,
            causante_iva: causanteIVA,
            tipo_tercero: solicitud.persona_tipo_id,
            documento_tipo: solicitud.tipo_identificacion_codigo,
            documento_numero: solicitud.documento_numero,
            nombres: solicitud.nombre,
            primer_apellido: solicitud.primer_apellido,
            segundo_apellido: solicitud.segundo_apellido,
            razon_social: solicitud.razon_social,
            id_departamento: solicitud.departamento_id,
            id_ciudad: solicitud.ciudad_id,
            direccion: solicitud.direccion,
            contacto: solicitud.telefono,
            email: solicitud.email,
            telefono: solicitud.razon_social,
            vendedor: solicitud.vendedor_codigo,
            lista_precio: '001',
            condiciono_pago: 'C30',
            tipo_cliente: 'C001',
            cupo: solicitud.cupo_asignado,
            dias_gracia: 26,
        }

        let creacionTerceroSiesa = crearTerceroCliente(datosTerceroSiesa)

        creacionTerceroSiesa.then(resultado => {
            if(resultado[0].codigo == 1) {
                agregarLog(80, JSON.stringify(resultado))
                mostrarAviso('error', `No se pudo crear el tercero en el ERP: <b>${resultado[0].detalle}</b>`, 20000)
                return false
            }

            agregarLog(81, JSON.stringify(resultado))
            mostrarAviso('exito', `La solicitud ha sido aprobada y el tercero ha sido creado correctamente`, 20000)

            // Creación del registro en bitácora
            datosBitacora.observaciones = `Tercero creado en Siesa`
            consulta('crear', datosBitacora)
        })

        Swal.close()
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

        // Creación del registro en bitácora
        datosBitacora.observaciones = `Solicitud denegada`
        consulta('crear', datosBitacora)

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

        // Creación del registro en bitácora
        datosBitacora.observaciones = `Enviado para firma`
        consulta('crear', datosBitacora)

        await consulta('actualizar', datos)

        mostrarAviso('exito', `Cambios guardados exitosamente`, 5000)
    }
</script>