<?php 
if (isset($datos['solicitud_garantia'])) {
    $solicitud = (object)$datos['solicitud_garantia'];
    print_r($solicitud);
    // $solicitud_detalle = $this->clientes_model->obtener("clientes_solicitudes_credito_detalle", ['cscd.solicitud_id' => $datos['id']]);
}
?>

<?php if(!isset($solicitud)) { ?>
    <div class="block-header block-header--has-breadcrumb block-header--has-title">
        <div class="container">
            <div class="block-header__body">
                <h1 class="block-header__title">Solicitud de garantía</h1>
            </div>
        </div>
    </div>
<?php } ?>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--1">
                <div class="tag-badge tag-badge--theme badge_formulario mb-3">
                    1 - DATOS DEL SOLICITANTE
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="solicitud_tipo_solicitante">Tipo de solicitante *</label>
                        <select id="solicitud_tipo_solicitante" class="form-control">
                            <option value="">Selecciona...</option>
                            <option value="2">Asesor comercial</option>
                            <option value="1">Cliente</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="solicitud_solicitante_razon_social">Nombre completo de quien solicita *</label>
                        <input type="text" class="form-control" id="solicitud_solicitante_razon_social" value="<?php if(isset($solicitud)) echo $solicitud->razon_social; ?>">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="solicitud_telefono">Teléfono *</label>
                        <input type="text" class="form-control" id="solicitud_telefono" value="<?php if(isset($solicitud)) echo $solicitud->telefono; ?>">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="solicitud_email">E-mail *</label>
                        <input type="email" class="form-control" id="solicitud_email" value="<?php if(isset($solicitud)) echo $solicitud->email; ?>">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="solicitud_numero_documento">Número de documento del cliente final *</label>
                        <input type="text" class="form-control" id="solicitud_numero_documento" value="<?php if(isset($solicitud)) echo $solicitud->documento_numero; ?>">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="solicitud_cliente_razon_social">Razón social del cliente *</label>
                        <input type="text" class="form-control" id="solicitud_cliente_razon_social" value="<?php if(isset($solicitud)) echo $solicitud->razon_social; ?>">
                    </div>
                </div>

                <div class="tag-badge tag-badge--theme badge_formulario mb-3 mt-2">
                    2 - INFORMACIÓN DE LA VENTA
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="solicitud_numero_factura">Número de factura *</label>
                        <input type="text" class="form-control" id="solicitud_numero_factura" value="<?php if(isset($solicitud)) echo $solicitud->representante_legal; ?>">
                    </div>

                </div>

                <div class="tag-badge tag-badge--theme badge_formulario mb-3 mt-2">
                    3 - IDENTIFICACIÓN DEL PRODUCTO
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="solicitud_cantidad_reclamada">Cantidad reclamada *</label>
                        <input type="number" class="form-control" id="solicitud_cantidad_reclamada" placeholder="$0" value="<?php if(isset($solicitud)) echo $solicitud->ingresos_mensuales; ?>">
                    </div>
                </div>

                <div class="tag-badge tag-badge--theme badge_formulario mb-3 mt-2">
                    4 - MOTIVO DE LA GARANTÍA
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="solicitud_motivo_id">Indicanos el motivo de la reclamación *</label>
                        <select id="solicitud_motivo_id" class="form-control">
                            <option value="">Sin asesor comercial asignado</option>
                            <?php foreach($this->configuracion_model->obtener('vendedores') as $vendedor) echo "<option value='$vendedor->id'>$vendedor->nombre</option>"; ?>
                        </select>
                    </div>
                </div>
                
                <?php if(!isset($solicitud)) { ?>
                    <button class="btn btn-primary btn-block" onClick="javascript:crearSolicituDGarantia()" id="btn_enviar_solicitud">ENVIAR SOLICITUD DE GARANTÍA</button>
                <?php } else { ?>
                    <button class="btn btn-primary btn-block mb-3" onClick="javascript:crearSolicituDGarantia()">ACTUALIZAR DATOS DE LA SOLICITUD</button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    crearSolicituDGarantia = async () => {
        // let camposObligatorios = [
        // ]
        // if (!validarCamposObligatorios(camposObligatorios)) return false

        // let archivos = validarArchivos()
        // if (!archivos) {
        //     mostrarAviso('alerta', `Por favor selecciona los archivos para poder finalizar la solicitud de crédito`, 20000)
        //     return false
        // }

        let datosSolicitud = {
            tipo: 'logistica_solicitudes_garantia',
        }

        $('#btn_enviar_solicitud').prop("disabled", true)

        // Si es un usuario logueado en el sistema, se agrega el id
        // if($('#sesion_usuario_id').val()) datosSolicitud.usuario_id = $('#sesion_usuario_id').val()

        Swal.fire({
            title: 'Estamos creando la solicitud de garantía en nuestro sistema...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

    }

    $().ready(async () => {

    })
</script>