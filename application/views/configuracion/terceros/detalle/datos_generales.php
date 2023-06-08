<?php
if($this->uri->segment(4)) {
    $tercero = $this->configuracion_model->obtener('usuarios', ['token' => $this->uri->segment(4)]);
    echo "<input type='hidden' id='tercero_id' value='$tercero->id' />";
}
?>

<div class="card">
    <div class="card-header">
        <h5>Datos generales</h5>
    </div>
    <div class="card-divider"></div>
    <div class="card-body card-body--padding--2">
        <div class="row no-gutters">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="tercero_razon_social">Razón Social *</label>
                        <input type="text" class="form-control" id="tercero_razon_social" value="<?php if(!empty($tercero)) echo $tercero->razon_social; ?>" autofocus>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="tercero_nombre_establecimiento">Nombre del establecimiento</label>
                        <input type="text" class="form-control" id="tercero_nombre_establecimiento" value="<?php if(!empty($tercero)) echo $tercero->nombre_establecimiento; ?>">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="tercero_nombres">Nombres</label>
                        <input type="text" class="form-control" id="tercero_nombres" value="<?php if(!empty($tercero)) echo $tercero->nombres; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_primer_apellido">Primer apellido</label>
                        <input type="text" class="form-control" id="tercero_primer_apellido" value="<?php if(!empty($tercero)) echo $tercero->primer_apellido; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_segundo_apellido">Segundo apellido</label>
                        <input type="text" class="form-control" id="tercero_segundo_apellido" value="<?php if(!empty($tercero)) echo $tercero->segundo_apellido; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_codigo">Código *</label>
                        <input type="text" class="form-control" id="tercero_codigo" value="<?php if(!empty($tercero)) echo $tercero->codigo; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_fecha_nacimiento">Fecha de nacimiento</label>
                        <input type="date" class="form-control" id="tercero_fecha_nacimiento" value="<?php if(!empty($tercero)) echo $tercero->fecha_nacimiento; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_telefono">Teléfono</label>
                        <input type="text" class="form-control" id="tercero_telefono" value="<?php if(!empty($tercero)) echo $tercero->telefono; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_celular">Celular</label>
                        <input type="text" class="form-control" id="tercero_celular" value="<?php if(!empty($tercero)) echo $tercero->celular; ?>">
                    </div>
                </div>
                <hr>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tercero_tipo_id">Tipo de tercero *</label>
                        <select id="tercero_tipo_id" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach($this->configuracion_model->obtener('usuarios_tipos') as $usuario_tipo) echo "<option value='$usuario_tipo->id'>$usuario_tipo->nombre</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_tipo_identificacion_id">Tipo de documento * </label>
                        <select id="tercero_tipo_identificacion_id" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach($this->configuracion_model->obtener('usuarios_identificacion_tipos') as $identificacion_tipo) echo "<option value='$identificacion_tipo->id'>$identificacion_tipo->nombre</option>"; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-8">
                        <label for="tercero_numero_documento">Número de documento *</label>
                        <input type="text" class="form-control" id="tercero_numero_documento" value="<?php if(!empty($tercero)) echo $tercero->documento_numero; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="tercero_numero_documento_digito_verificacion">Dígito de verificación</label>
                        <input type="text" class="form-control" id="tercero_numero_documento_digito_verificacion" value="<?php if(!empty($tercero)) echo $tercero->digito_verificacion; ?>">
                    </div>
                    <div class="form-group col-md-12 mb-0">
                        <label for="tercero_email">Correo electrónico *</label>
                        <input type="email" class="form-control" id="tercero_email" value="<?php if(!empty($tercero)) echo $tercero->email; ?>">
                    </div>
                    <div class="form-group col-md-12 mb-0">
                        <label for="tercero_nombre_contacto">Nombre de contacto</label>
                        <input type="text" class="form-control" id="tercero_nombre_contacto" value="<?php if(!empty($tercero)) echo $tercero->nombre_contacto; ?>">
                    </div>
                </div>
                <hr>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="tercero_es_proveedor">¿Es proveedor? *</label>
                        <select id="tercero_es_proveedor" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tercero_es_empleado">¿Es empleado? *</label>
                        <select id="tercero_es_empleado" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tercero_es_interno">¿Interno? *</label>
                        <select id="tercero_es_interno" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tercero_es_cliente">Es cliente? *</label>
                        <select id="tercero_es_cliente" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_estado">Estado *</label>
                        <select id="tercero_estado" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tercero_es_regimen_unificado">¿Régimen unificado? *</label>
                        <select id="tercero_es_regimen_unificado" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group col-md-12 mb-0">
                        <label for="tercero_ciiu">CIIU</label>
                        <input type="text" class="form-control" id="tercero_ciiu" value="<?php if(!empty($tercero)) echo $tercero->ciiu; ?>">
                    </div>
                </div>
                <div class="form-group mb-0 pt-3 mt-3">
                    <button class="btn btn-info" onClick="javascript:history.back()">Volver</button>
                    <button class="btn btn-success" onClick="javascript:guardarTercero()">Guardar datos</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(isset($tercero)) { ?>
    <script>
        $('#tercero_tipo_id').val(<?php echo $tercero->usuario_tipo_id; ?>)
        $('#tercero_tipo_identificacion_id').val(<?php echo $tercero->usuario_identificacion_tipo_id; ?>)
        $('#tercero_es_proveedor').val(<?php echo $tercero->es_proveedor; ?>)
        $('#tercero_es_empleado').val(<?php echo $tercero->es_empleado; ?>)
        $('#tercero_es_interno').val(<?php echo $tercero->es_interno; ?>)
        $('#tercero_es_cliente').val(<?php echo $tercero->es_cliente; ?>)
        $('#tercero_estado').val(<?php echo $tercero->estado; ?>)
        $('#tercero_es_regimen_unificado').val(<?php echo $tercero->es_regimen_unificado; ?>)
    </script>
<?php } ?>

<script>
    guardarTercero = async() => {
        let camposObligatorios = [
            $('#tercero_razon_social'),
            $('#tercero_codigo'),
            $('#tercero_tipo_id'),
            $('#tercero_tipo_identificacion_id'),
            $('#tercero_numero_documento'),
            $('#tercero_email'),
            $('#tercero_es_proveedor'),
            $('#tercero_es_empleado'),
            $('#tercero_es_interno'),
            $('#tercero_es_cliente'),
            $('#tercero_estado'),
            $('#tercero_es_regimen_unificado'),
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datosTercero = {
            tipo: 'terceros',
            razon_social: $('#tercero_razon_social').val(),
            nombre_establecimiento: $('#tercero_nombre_establecimiento').val(),
            nombres: $('#tercero_nombres').val(),
            primer_apellido: $('#tercero_primer_apellido').val(),
            segundo_apellido: $('#tercero_segundo_apellido').val(),
            codigo: $('#tercero_codigo').val(),
            fecha_nacimiento: $('#tercero_fecha_nacimiento').val(),
            email: $('#tercero_email').val(),
            telefono: $('#tercero_telefono').val(),
            celular: $('#tercero_celular').val(),
            usuario_tipo_id: $('#tercero_tipo_id').val(),
            usuario_identificacion_tipo_id: $('#tercero_tipo_identificacion_id').val(),
            documento_numero: $('#tercero_numero_documento').val(),
            digito_verificacion: $('#tercero_numero_documento_digito_verificacion').val(),
            nombre_contacto: $('#tercero_nombre_contacto').val(),
            es_proveedor: $('#tercero_es_proveedor').val(),
            es_empleado: $('#tercero_es_empleado').val(),
            es_interno: $('#tercero_es_interno').val(),
            es_cliente: $('#tercero_es_cliente').val(),
            estado: $('#tercero_estado').val(),
            es_regimen_unificado: $('#tercero_es_regimen_unificado').val(),
            ciiu: $('#tercero_ciiu').val(),
        }

        if(!$('#tercero_id').val()) {
            // Se consulta si existe un usuario con ese mismo login
            let terceroExistente = await consulta('obtener', {tipo: 'usuarios', documento_numero: $.trim($('#tercero_numero_documento').val())})
            
            if(terceroExistente) {
                mostrarNotificacion('alerta', `El tercero con número de documento <b>${$('#tercero_numero_documento').val()}</b> ya existe en la base de datos.`)
                return false
            }

            await consulta('crear', datosTercero)
        } else {
            datosTercero.id = $('#tercero_id').val()

            await consulta('actualizar', datosTercero)
        }
    }
</script>