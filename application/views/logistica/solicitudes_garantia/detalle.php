<?php  if (isset($datos['solicitud_garantia'])) $solicitud = (object)$datos['solicitud_garantia']; ?>

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

                    <div class="form-group col-md-8">
                        <label for="solicitud_solicitante_nombres">Nombre completo de quien solicita *</label>
                        <input type="text" class="form-control" id="solicitud_solicitante_nombres" value="<?php if(isset($solicitud)) echo $solicitud->solicitante_nombres; ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_solicitante_telefono">Teléfono *</label>
                        <input type="text" class="form-control" id="solicitud_solicitante_telefono" value="<?php if(isset($solicitud)) echo $solicitud->solicitante_telefono; ?>">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_solicitante_email">E-mail *</label>
                        <input type="email" class="form-control" id="solicitud_solicitante_email" value="<?php if(isset($solicitud)) echo $solicitud->solicitante_email; ?>">
                    </div>
                </div>

                <div class="tag-badge tag-badge--theme badge_formulario mb-3 mt-2">
                    2 - INFORMACIÓN DE LA VENTA
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label for="solicitud_cliente_nit">Número de documento del cliente final *</label>
                        <input type="text" class="form-control" id="solicitud_cliente_nit" value="900649430">
                    </div>

                    <div class="form-group col-md-5">
                        <label for="solicitud_numero_factura">Número de factura *</label>
                        <input type="text" id="solicitud_numero_factura" class="form-control" placeholder="Ej: 100-CPV-135339" value="100-CPV-135339">
                    </div>

                    <div class="form-group col-md-2">
                        <label>&nbsp;</label>
                        <button class="btn btn-primary btn-block" href="javascript:;" onClick="javascript:buscarPedido()">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Contenedor para mostrar los datos del pedido -->
                <div id="contenedor_detalle_pedido">
                    <div class="tag-badge tag-badge--theme badge_formulario mb-3 mt-2">
                        3 - IDENTIFICACIÓN DEL PRODUCTO
                    </div>

                    <div class="alert alert-primary mb-3">No se ha seleccionado un pedido todavía. Por favor escribe el número de la factura y haz clic en el ícono <strong><i class="fa fa-search"></i></strong></div>
                </div>

                <div class="tag-badge tag-badge--theme badge_formulario mb-3 mt-2">
                    4 - MOTIVO DE LA GARANTÍA
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-4">
                        <label for="solicitud_motivo_id">Indicanos el motivo de la reclamación *</label>
                        <select id="solicitud_motivo_id" class="form-control">
                            <option value="">Selecciona...</option>
                            <?php foreach($this->configuracion_model->obtener('productos_solicitudes_garantia_motivos_reclamacion') as $motivo_reclamacion) echo "<option value='$motivo_reclamacion->id'>$motivo_reclamacion->nombre</option>"; ?>
                        </select>
                    </div>

                    <div class="form-group col-lg-4">
                        <label for="solicitud_motivo_otro">Si es otro motivo, indícanos cuál</label>
                        <input type="text" class="form-control" id="solicitud_motivo_otro" value="<?php // if(isset($solicitud)) echo $solicitud->razon_social; ?>">
                    </div>

                    <div class="form-group col-lg-4">
                        <label for="solicitud_producto_estado">Estado del producto</label>
                        <select id="solicitud_producto_estado" class="form-control">
                            <option value="">Selecciona...</option>
                            <option value="1">Nuevo en empaque</option>
                            <option value="2">Instalado</option>
                            <option value="3">Usado</option>
                            <option value="4">Incompleto</option>
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="solicitud_desripcion">Danos detalle de la razón por la cual estás solicitando garantía *</label>
                        <textarea id="solicitud_desripcion" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="tag-badge tag-badge--theme badge_formulario mb-3 mt-2">
                    5 - LOGÍSTICA DE DEVOLUCIÓN
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-6">
                        <label for="solicitud_producto_ubicacion_actual">Ubicación actual del producto *</label>
                        <select id="solicitud_producto_ubicacion_actual" class="form-control">
                            <option value="">Selecciona...</option>
                            <option value="1">Cliente</option>
                            <option value="2">Sede de Repuestos Simón Bolívar</option>
                            <option value="3">En tránsito</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-6">
                        <label for="solicitud_metodo_devolucion">Método de devolución *</label>
                        <select id="solicitud_metodo_devolucion" class="form-control">
                            <option value="">Selecciona...</option>
                            <option value="1">El cliente realizó entrega</option>
                            <option value="2">La transportadora lo recoge</option>
                            <option value="3">Ya está en sede</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-6">
                        <label for="solicitud_tipo_solucion">Tipo de solución preferida (opcional)</label>
                        <select id="solicitud_tipo_solucion" class="form-control">
                            <option value="">Selecciona...</option>
                            <option value="1">Cambio inmediato</option>
                            <option value="2">Nota crédito</option>
                            <option value="3">Reparación</option>
                            <option value="4">Otro</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-6">
                        <label for="solicitud_tipo_solucion_otro">Si es otro, indícanos cuál</label>
                        <input type="text" class="form-control" id="solicitud_tipo_solucion_otro" value="<?php // if(isset($solicitud)) echo $solicitud->razon_social; ?>">
                    </div>
                </div>
                
                <?php if(!isset($solicitud)) { ?>
                    <button class="btn btn-primary btn-block" onClick="javascript:crearSolicitudGarantia()" id="btn_enviar_solicitud">ENVIAR SOLICITUD DE GARANTÍA</button>
                <?php } else { ?>
                    <button class="btn btn-primary btn-block mb-3" onClick="javascript:crearSolicitudGarantia()">ACTUALIZAR DATOS DE LA SOLICITUD</button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    buscarPedido = async () => {
        Swal.fire({
            title: 'Estamos Buscando el pedido en nuestro sistema...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        let estructuraNumeroPedido = $('#solicitud_numero_factura').val().split('-')
        
        let datosPedido = {
            tipo: 'pedidos',
            centro_operativo: estructuraNumeroPedido[0],
            tipo_documento: estructuraNumeroPedido[1],
            documento_cruce: estructuraNumeroPedido[2],
            numero_documento: $('#solicitud_cliente_nit').val(),
        }

        // Se consulta en el ERP el pedido
        var consultaPedido = await consulta('obtener', datosPedido)

        // Si no se encontró el pedido
        if(consultaPedido.codigo == 1) {
            mostrarAviso('alerta', `El pedido ${$('#solicitud_numero_factura').val()} no se encontró en nuestro sistema. Por favor, verifica nuevamente o ponte en contacto con nosotros.`, false)
            return false
        }

        await cargarInterfaz('logistica/solicitudes_garantia/detalle_pedido', 'contenedor_detalle_pedido', {pedido: consultaPedido.detalle.Table})

        Swal.close()
    }

    crearSolicitudGarantia = async () => {
        // Primero, nos aseguramos que se identifique el pedido
        let camposObligatoriosPedido = [
            $('#solicitud_cliente_nit'),
            $('#solicitud_numero_factura'),
        ]
        if (!validarCamposObligatorios(camposObligatoriosPedido, 'Por favor selecciona primero el pedido donde está el producto sobre el cual vas a pedir la garantía.')) return false

        
        // Si aún no se ha traido la respuesta del pedido
        if(!$('#solicitud_vendedor_nit').val()) {
            mostrarAviso('alerta', `Por favor selecciona primero el pedido donde está el producto sobre el cual vas a pedir la garantía.`, 20000)
            return false
        }

        // El resto de campos obligatorios se validan
        let camposObligatorios = [
            $('#solicitud_tipo_solicitante'),
            $('#solicitud_solicitante_nombres'),
            $('#solicitud_solicitante_telefono'),
            $('#solicitud_solicitante_email'),
            $('#solicitud_motivo_id'),
            $('#solicitud_producto_estado'),
            $('#solicitud_desripcion'),
            $('#solicitud_producto_ubicacion_actual'),
            $('#solicitud_metodo_devolucion'),
            $('#solicitud_producto_id'),
            $('#solicitud_cantidad_reclamada'),
        ]
        if (!validarCamposObligatorios(camposObligatorios)) return false

        // let archivos = validarArchivos()
        // if (!archivos) {
        //     mostrarAviso('alerta', `Por favor selecciona los archivos para poder finalizar la solicitud de crédito`, 20000)
        //     return false
        // }
        
        // Se captura la cantidad vendida del producto seleccionado
        let cantidadProducto = parseInt($('#solicitud_producto_id option:selected').attr('data-cantidad'))

        // Si la cantidad reclamada supera la cantidad vendida del producto
        if(parseInt($('#solicitud_cantidad_reclamada').val()) > cantidadProducto) {
            mostrarAviso('alerta', `El producto que seleccionaste solamente tiene ${cantidadProducto} unidades. La cantidad reclamada no puede ser mayor.`, 20000)
            return false
        }

        let datosSolicitud = {
            tipo: 'productos_solicitudes_garantia',
            solicitante_tipo: $('#solicitud_tipo_solicitante').val(),
            solicitante_nombres: $('#solicitud_solicitante_nombres').val().toUpperCase(),
            solicitante_telefono: $('#solicitud_solicitante_telefono').val(),
            solicitante_email: $('#solicitud_solicitante_email').val(),
            cliente_nit: $('#solicitud_cliente_nit').val(),
            numero_factura: $('#solicitud_numero_factura').val(),
            cliente_razon_social: $('#solicitud_cliente_razon_social').val().toUpperCase(),
            vendedor_nit: $('#solicitud_vendedor_nit').val(),
            producto_id: $('#solicitud_producto_id').val(),
            producto_cantidad_reclamada: $('#solicitud_cantidad_reclamada').val(),
            producto_numero_serie: $('#solicitud_producto_serial').val(),
            producto_fecha_instalacion: $('#solicitud_fecha_instalacion').val(),
            producto_uso: $('#solicitud_uso').val(),
            producto_solicitud_garantia_motivo_otro: $('#solicitud_motivo_otro').val(),
            producto_solicitud_garantia_estado_id: $('#solicitud_producto_estado').val(),
            descripcion: $('#solicitud_desripcion').val(),
            producto_ubicacion_actual: $('#solicitud_producto_ubicacion_actual').val(),
            producto_metodo_devolucion: $('#solicitud_metodo_devolucion').val(),
            tipo_solucion: $('#solicitud_tipo_solucion').val(),
            tipo_solucion_otro: $('#solicitud_tipo_solucion_otro').val(),
        }
        console.table(datosSolicitud)

        // $('#btn_enviar_solicitud').prop("disabled", true)

        // Si es un usuario logueado en el sistema, se agrega el id
        if($('#sesion_usuario_id').val()) datosSolicitud.usuario_id = $('#sesion_usuario_id').val()

        Swal.fire({
            title: 'Estamos creando la solicitud de garantía en nuestro sistema...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        // Se crea la solicitud
        let resultado = await consulta('crear', datosSolicitud, false)

        // Creación del registro en bitácora
        var datosBitacora = {
            tipo: 'productos_solicitudes_garantia_bitacora',
            solicitud_id: resultado.id,
            usuario_id: $('#sesion_usuario_id').val(),
            observaciones: `Solicitud recibida`,
        }
        consulta('crear', datosBitacora, false)

        Swal.close()
        mostrarAviso("exito", `¡Tu solicitud de garantía ha sido creada exitosamente con el radicado <b>${resultado.radicado}</b>! Te enviaremos un correo electrónico de confirmación y nos comunicaremos contigo lo más pronto posible.`, 30000)
    }

    $().ready(async () => {

    })
</script>