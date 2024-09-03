<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Registro de usuario</h1>
        </div>
        <div class="alert alert-primary mb-3">
            Regístrate para obtener grandes descuentos en toda nuestra tienda
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-header">
                <h5>Datos principales</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <div class="form-group">
                            <label for="usuario_responsable_iva">¿Eres responsable de IVA? *</label>
                            <select id="usuario_responsable_iva" class="form-control">
                                <option value="">Selecciona...</option>
                                <option value="0" data-responsable_iva="49" data-causante_iva="ZY">No</option>
                                <option value="1" data-responsable_iva="48" data-causante_iva="01">Sí</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <div class="form-group">
                            <label for="usuario_tipo_tercero">¿Eres persona natural o jurídica? *</label>
                            <select id="usuario_tipo_tercero" class="form-control">
                                <option value="">Selecciona...</option>
                                <option value="1">Persona natural</option>
                                <option value="2">Persona jurídica</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row col-md-9" id="datos_persona_natural">
                        <div class="form-group col-md-4">
                            <label for="usuario_nombres">Nombres *</label>
                            <input type="text" class="form-control" id="usuario_nombres">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="usuario_primer_apellido">Primer apellido *</label>
                            <input type="text" class="form-control" id="usuario_primer_apellido">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="usuario_segundo_apellido">Segundo apellido <span class="text-muted">(Opcional)</span></label>
                            <input type="text" class="form-control" id="usuario_segundo_apellido">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="usuario_razon_social">Razón social *</label>
                    <input type="text" class="form-control" id="usuario_razon_social">
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-4 col-sm-12">
                        <div class="form-group">
                            <label for="usuario_tipo_documento">Tipo de documento *</label>
                            <select id="usuario_tipo_documento" class="form-control">
                                <option value="">Selecciona...</option>
                                <option value="C" data-tipo_tercero="1">Cédula de ciudadanía</option>
                                <option value="N" data-tipo_tercero="2">NIT</option>
                                <option value="E" data-tipo_tercero="1">Cédula de extranjería</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-4 col-sm-12">
                        <label for="usuario_numero_documento1">Número de documento (sin dígito de verificación) *</label>
                        <input type="text" class="form-control" id="usuario_numero_documento1" placeholder="sin dígito de verificación">
                    </div>

                    <div class="form-group col-lg-4 col-sm-12">
                        <label for="usuario_numero_documento2">Repite el número de documento *</label>
                        <input type="text" class="form-control" id="usuario_numero_documento2" onpaste="return false;">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-4 col-sm-12">
                        <label for="usuario_login">Nombre de usuario *</label>
                        <input type="text" class="form-control" id="usuario_login">
                    </div>

                    <div class="form-group col-lg-4 col-sm-12">
                        <label for="usuario_clave1">Contraseña *</label>
                        <input type="password" class="form-control" id="usuario_clave1">
                    </div>

                    <div class="form-group col-lg-4 col-sm-12">
                        <label for="usuario_clave2">Repite la contraseña *</label>
                        <input type="password" class="form-control" id="usuario_clave2">
                    </div>
                </div>
            </div>

            <div class="card-header">
                <h5>Datos que necesitamos para enviarte tus productos</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1">
                <div class="form-row">
                    <div class="form-group col-lg-3 col-sm-12">
                        <label for="usuario_departamento_id">Departamento *</label>
                        <select id="usuario_departamento_id" class="form-control"></select>
                    </div>

                    <div class="form-group col-lg-3 col-sm-12">
                        <label for="usuario_municipio_id">Municipio *</label>
                        <select id="usuario_municipio_id" class="form-control"></select>
                    </div>

                    <div class="form-group col-lg-3 col-sm-12">
                        <label for="usuario_direccion">Dirección completa *</label>
                        <input type="text" class="form-control" id="usuario_direccion">
                    </div>
                    <div class="form-group col-lg-3 col-sm-12">
                        <label for="usuario_telefono">Número de teléfono *</label>
                        <input type="text" class="form-control" id="usuario_telefono">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-lg-6 col-sm-12">
                        <label for="usuario_contacto">Nombre de un contacto *</label>
                        <input type="text" class="form-control" id="usuario_contacto">
                    </div>
                    <div class="form-group col-lg-6 col-sm-12">
                        <label for="usuario_email">Correo electrónico *</label>
                        <input type="email" class="form-control" id="usuario_email">
                    </div>
                </div>

                <div class="form-group mb-2 mt-2">
                    <button class="btn btn-primary btn-block" onClick="javascript:crearUsuario()">Finalizar mi registro</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    crearUsuario = async() => {
        let camposObligatorios = [
            $('#usuario_tipo_tercero'),
            $('#usuario_numero_documento1'),
            $('#usuario_tipo_documento'),
            $('#usuario_razon_social'),
            $('#usuario_direccion'),
            $('#usuario_telefono'),
            $('#usuario_email'),
            $('#usuario_login'),
            $('#usuario_clave1'),
            $('#usuario_clave2'),
            $('#usuario_municipio_id'),
            $('#usuario_contacto'),
            $('#usuario_responsable_iva'),
        ]

        // Si es persona natural
        if ($('#usuario_tipo_tercero').val() == 1) {
            camposObligatorios.push($('#usuario_nombres'))
            camposObligatorios.push($('#usuario_primer_apellido'))
        }

        if (!validarCamposObligatorios(camposObligatorios)) return false

        // Si no coinciden los números de documento
        if ($("#usuario_numero_documento1").val() !== $("#usuario_numero_documento2").val()) {
            mostrarAviso('alerta', `Los números de documento no coinciden. Por favor, verifica nuevamente.`, 10000)
            return false
        }

        // Si no coinciden las claves
        if ($("#usuario_clave1").val() !== $("#usuario_clave2").val()) {
            mostrarAviso('alerta', `Las contraseñas no coinciden. Por favor, verifica nuevamente.`, 10000)
            return false
        }

        // Se consulta si existe un usuario con ese mismo login
        let usuarioExistenteDocumento = await consulta('obtener', {tipo: 'usuarios', documento_numero: $.trim($('#usuario_numero_documento').val())})
        let usuarioExistenteLogin = await consulta('obtener', {tipo: 'usuarios', login: $.trim($('#usuario_login').val())})
        let usuarioExistenteEmail = await consulta('obtener', {tipo: 'usuarios', email: $.trim($('#usuario_email').val())})

        if(usuarioExistenteDocumento || usuarioExistenteLogin || usuarioExistenteEmail) {
            mostrarAviso('alerta', `Ya estás registrado en nuestro sistema, por favor verifica nuevamente el correo electrónico, usuario y el número de documento. Podrás iniciar sesión <a href='${$('#site_url').val()}/sesion'>iniciar sesión haciendo clic aquí</a> o recuperar tu contraseña`, 10000)
            return false
        }

        let datosTerceroSiesa = {
            responsable_iva: $('#usuario_responsable_iva option:selected').attr('data-responsable_iva'), // Sí, No
            causante_iva: $('#usuario_responsable_iva option:selected').attr('data-causante_iva'), // Sí, No
            tipo_tercero: $('#usuario_tipo_tercero').val(), // Natural, jurídica
            documento_tipo: $('#usuario_tipo_documento').val(),
            documento_numero: $('#usuario_numero_documento1').val(),
            nombres: $('#usuario_nombres').val(),
            primer_apellido: $('#usuario_primer_apellido').val(),
            segundo_apellido: $('#usuario_segundo_apellido').val(),
            razon_social: $('#usuario_razon_social').val(),
            id_departamento: $('#usuario_departamento_id').val(),
            id_ciudad: $('#usuario_municipio_id').val(),
            direccion: $('#usuario_direccion').val(),
            contacto: $('#usuario_contacto').val(),
            email: $('#usuario_email').val(),
            telefono: $('#usuario_telefono').val(),
        }

        // Si es cédula de extranjería, se envía una entidad dinámica adicional
        // para la creación del tercero en Siesa
        if($('#usuario_tipo_documento').val() == 'E') {
            datosTerceroSiesa.entidad_dinamica_extranjero = {
                f200_id: $('#usuario_numero_documento1').val(),
                f753_id_entidad: 'EUNOECO036',
                f753_id_atributo: 'co036_id_procedencia_org',
                f753_id_maestro: 'MUNOECO043',
                f753_id_maestro_detalle: 11,
            }
        }

        let datosUsuario = {
            tipo: 'usuarios',
            nombres: $('#usuario_nombres').val(),
            primer_apellido: $('#usuario_primer_apellido').val(),
            segundo_apellido: $('#usuario_segundo_apellido').val(),
            razon_social: $('#usuario_razon_social').val(),
            celular: $('#usuario_telefono').val(),
            usuario_tipo_id: $('#usuario_tipo_tercero').val(),
            documento_numero: $('#usuario_numero_documento1').val(),
            usuario_identificacion_tipo_id: $('#usuario_tipo_documento option:selected').attr('data-tipo_tercero'),
            email: $('#usuario_email').val(),
            nombre_contacto: $('#usuario_contacto').val(),
            ciudad_id: $('#usuario_municipio_id').val(),
            departamento_id: $('#usuario_departamento_id').val(),
            direccion1: $('#usuario_direccion').val(),
            clave: $('#usuario_clave1').val(),
            login: $('#usuario_login').val(),
            perfil_id: 3,
            responsable_iva: $('#usuario_responsable_iva').val(),
        }

        Swal.fire({
            title: 'Estamos creando tu usuario en nuestros sistemas...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        // Se consulta en Siesa el tercero
        let consultaTercero = await consulta('obtener', {tipo: 'terceros', numero_documento: $('#usuario_numero_documento1').val()}, false)

        // Se crea el usuario
        let usuarioId = await consulta('crear', datosUsuario, false)

        Swal.close()

        mostrarAviso('exito', `
            ¡Tu usuario ha sido creado correctamente!<br><br>
            Ahora puedes <a href='${$('#site_url').val()}/sesion'>iniciar sesión haciendo clic aquí</a>
        `, 20000)

        // Envío de email de confirmación
        obtenerPromesa(`${$('#site_url').val()}interfaces/enviar_email`, {tipo: 'usuario_nuevo', id: usuarioId.resultado})
        
        // Si el tercero no existe en Siesa
        if(!consultaTercero.codigo == 0) {
            let creacionTerceroSiesa = crearTerceroCliente(datosTerceroSiesa)
            creacionTerceroSiesa.then(resultado => console.log(resultado))
        }
    }

    $().ready(() => {
        // Cuando se seleccione el tipo de tercero
        $('#usuario_tipo_tercero').change(() => {
            // Persona natural
            if ($('#usuario_tipo_tercero').val() == 1) {
                $('#datos_persona_natural').show()
                $('#usuario_razon_social').attr('disabled', true)
            }

            // Persona jurídica
            if ($('#usuario_tipo_tercero').val() == 2) {
                $('#datos_persona_natural').hide()
                $('#usuario_razon_social').attr('disabled', false)
                $('#usuario_nombres, #usuario_primer_apellido, #usuario_segundo_apellido, #usuario_razon_social').val('')
            }
        })

        // Cuando se escribe nombres o apellidos
        $('#datos_persona_natural').keyup(function(e) {
            // Si es persona natural
            if($('#usuario_tipo_tercero').val() == '1') {
                // Se completa la razón social
                $('#usuario_razon_social').val(`${$('#usuario_nombres').val()} ${$('#usuario_primer_apellido').val()} ${$('#usuario_segundo_apellido').val()}`)
            }
        })

        listarDatos('usuario_departamento_id', {tipo: 'departamentos', pais_id: 169})
        
        // Cuando se seleccione un departamento
        $('#usuario_departamento_id').change(() => {
            listarDatos('usuario_municipio_id', {tipo: 'municipios', departamento_id: $('#usuario_departamento_id').val()})
        })

        // Control del input para que registren solamente números
        $(`#usuario_numero_documento1, #usuario_numero_documento2`).keyup(function() {
            $(this).val(limpiarCadena($(this).val()))
        })
    })
</script>