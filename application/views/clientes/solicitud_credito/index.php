<?php $this->load->view("core/subida_archivos"); ?>

<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Solicitud de crédito</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--1">
                <div class="form-row mb-2">
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="solicitud_nueva" id="solicitud_nueva1" value="1">
                            <label class="form-check-label" for="solicitud_nueva1">
                                Quiero crear una solicitud nueva *
                            </label>
                        </div>
                    </div>
                
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="solicitud_nueva" id="solicitud_nueva0" value="0">
                            <label class="form-check-label" for="solicitud_nueva0">
                                Quiero actualizar mi solicitud *
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-divider"></div>
                <div class="tag-badge tag-badge--theme badge_formulario mb-2 mt-2">
                    DATOS BÁSICOS
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="solicitud_persona_tipo">¿Eres persona natural o jurídica? *</label>
                        <select id="solicitud_persona_tipo" class="form-control">
                            <option value="">Selecciona...</option>
                            <option value="1">Persona natural</option>
                            <option value="2">Persona jurídica</option>
                        </select>
                    </div>

                    <div class="form-group col-md-8">
                        <label for="solicitud_nombre">Nombre o razón social *</label>
                        <input type="text" class="form-control" id="solicitud_nombre">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-3">
                        <br>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="solicitud_tipo_documento" id="solicitud_tipo_documento1" value="1">
                            <label class="form-check-label" for="solicitud_tipo_documento1">
                                Cédula de ciudadanía *
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <br>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="solicitud_tipo_documento" id="solicitud_tipo_documento2" value="2">
                            <label class="form-check-label" for="solicitud_tipo_documento2">
                                Nit *
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_numero_documento">Número de documento *</label>
                        <input type="text" class="form-control" id="solicitud_numero_documento">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <div class="form-group">
                            <label for="solicitud_direccion">Dirección *</label>
                            <input type="text" class="form-control" id="solicitud_direccion">
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_telefono">Telefono *</label>
                        <input type="text" class="form-control" id="solicitud_telefono">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="solicitud_email">E-mail *</label>
                        <input type="text" class="form-control" id="solicitud_email">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_celular">Celular del titular o Representante legal</label>
                        <input type="text" class="form-control" id="solicitud_celular">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="solicitud_representante_legal">Representante legal (para personas jurídicas)</label>
                        <input type="text" class="form-control" id="solicitud_representante_legal" placeholder="Solo para personas jurídicas">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_representante_legal_documento">Número de documento del representante legal</label>
                        <input type="text" class="form-control" id="solicitud_representante_legal_documento" placeholder="Solo para personas jurídicas">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="solicitud_correo_facturacion_electronica">Correo para facturación electrónica *</label>
                        <input type="text" class="form-control" id="solicitud_correo_facturacion_electronica">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_cantidad_vehiculos">Si posees vehículos propios, indica cuántos</label>
                        <input type="number" class="form-control" id="solicitud_cantidad_vehiculos">
                    </div>
                </div>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1">
                <div class="tag-badge tag-badge--theme badge_formulario mb-2">
                    PERSONAS DE CONTACTO
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>Área</label>
                        <input type="text" class="form-control" value="Tesorería y pagos" readonly>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tesoreria_nombre">Nombre</label>
                        <input type="text" class="form-control" id="tesoreria_nombre">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="tesoreria_email">Email</label>
                        <input type="text" class="form-control" id="tesoreria_email">
                    </div>

                    <div class="form-group col-md-2">
                        <label for="tesoreria_telefono">Teléfono directo</label>
                        <input type="text" class="form-control" id="tesoreria_telefono">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="tesoreria_celular">Celular</label>
                        <input type="text" class="form-control" id="tesoreria_celular">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" value="Comercial" readonly>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="comercial_nombre">
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="comercial_email">
                    </div>

                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="comercial_telefono">
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="comercial_celular">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" value="Contabilidad" readonly>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="contabilidad_nombre">
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" id="contabilidad_email">
                    </div>

                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="contabilidad_telefono">
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" class="form-control" id="contabilidad_celular">
                    </div>
                </div>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1">
                <div class="tag-badge tag-badge--theme badge_formulario mb-2">
                    REFERENCIAS COMERCIALES
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="referencia_comercial_entidad1">Entidad</label>
                        <input type="text" class="form-control" id="referencia_comercial_entidad1">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="referencia_comercial_celular1">Celular</label>
                        <input type="text" class="form-control" id="referencia_comercial_celular1">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="referencia_comercial_direccion1">Dirección</label>
                        <input type="text" class="form-control" id="referencia_comercial_direccion1">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" id="referencia_comercial_entidad2">
                    </div>

                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" id="referencia_comercial_celular2">
                    </div>

                    <div class="form-group col-md-4">
                        <input type="text" class="form-control" id="referencia_comercial_direccion2">
                    </div>
                </div>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1">
                <div class="tag-badge tag-badge--theme badge_formulario mb-2">
                    REFERENCIA BANCARIA
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="referencia_bancaria_entidad">Entidad</label>
                        <input type="text" class="form-control" id="referencia_bancaria_entidad">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="referencia_bancaria_tipo">Tipo</label>
                        <input type="text" class="form-control" id="referencia_bancaria_tipo">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="referencia_bancaria_numero">Dirección</label>
                        <input type="text" class="form-control" id="referencia_bancaria_numero">
                    </div>
                </div>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1" id="personas_autorizadas">
                <div class="tag-badge tag-badge--theme badge_formulario mb-2">
                    PERSONAS AUTORIZADAS
                </div>
                <small>(personas a quiénes autorizas para brindar información de tu cuenta)</small>
                <div class="form-row" id="personas_autorizadas_0">
                    <div class="form-group col-md-4">
                        <label>Nombre</label>
                        <input type="text" class="form-control" id="personas_autorizadas_nombre_0">
                    </div>

                    <div class="form-group col-md-4">
                        <label>Identificación</label>
                        <input type="text" class="form-control" id="personas_autorizadas_identificacion_0">
                    </div>

                    <div class="form-group col-md-4">
                        <label>Celular</label>
                        <input type="text" class="form-control" id="personas_autorizadas_celular_0">
                    </div>
                </div>
            </div>

            <div class="form-group mx-3">
                <button class="btn btn-success" onClick="javascript:agregarCamposPersonaAutorizada('personas_autorizadas')">Agregar</button>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1" id="clientes">
                <div class="tag-badge tag-badge--theme badge_formulario mb-2">
                    SAGRILAFT FORMULARIO DE CONOCIMIENTO DE CLIENTES
                </div>

                <div class="card-header">
                    <h5>Socios y/o accionistas (solo para personas jurídicas)</h5>
                </div>

                <div class="form-row" id="clientes_0">
                    <div class="form-group col-md-3">
                        <label>Nombre socio o accionista</label>
                        <input type="text" class="form-control" id="clientes_nombre_0">
                    </div>

                    <div class="form-group col-md-3">
                        <label>Tipo de identificación</label>
                        <select id="clientes_tipo_identificacion_0" class="form-control">
                            <option value="">Selecciona...</option>
                            <option value="C" data-tipo_tercero="1">Cédula de ciudadanía</option>
                            <option value="N" data-tipo_tercero="2">NIT</option>
                            <option value="E" data-tipo_tercero="1">Cédula de extranjería</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Número de identificación</label>
                        <input type="text" class="form-control" id="clientes_numero_documento_0">
                    </div>

                    <div class="form-group col-md-3">
                        <label>% participación</label>
                        <input type="text" class="form-control" id="clientes_porcentaje_participacion_0">
                    </div>
                </div>
            </div>

            <div class="form-group mx-3">
                <button class="btn btn-success" onClick="javascript:agregarCamposClientesSociosAccionistas('clientes')">Agregar</button>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1" id="beneficiarios_cliente">
                <div class="tag-badge tag-badge--theme badge_formulario mb-2">
                    BENEFICIARIOS FINALES DE SOCIOS Y/O ACCIONISTAS IGUALES O SUPERIORES AL 5%
                </div>
                <small>(Solo para personas jurídicas)</small>
                <div class="form-row" id="beneficiarios_cliente_0">
                    <div class="form-group col-md-3">
                        <label>Nombre socio o accionista</label>
                        <input type="text" class="form-control" id="beneficiarios_cliente_nombre_0">
                    </div>

                    <div class="form-group col-md-3">
                        <label>Tipo de identificación</label>
                        <select id="beneficiarios_cliente_tipo_identificacion_0" class="form-control">
                            <option value="">Selecciona...</option>
                            <option value="C" data-tipo_tercero="1">Cédula de ciudadanía</option>
                            <option value="N" data-tipo_tercero="2">NIT</option>
                            <option value="E" data-tipo_tercero="1">Cédula de extranjería</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Número de identificación</label>
                        <input type="text" class="form-control" id="beneficiarios_cliente_numero_documento_0">
                    </div>

                    <div class="form-group col-md-3">
                        <label>% participación</label>
                        <input type="text" class="form-control" id="beneficiarios_cliente_porcentaje_participacion_0">
                    </div>
                </div>
            </div>

            <div class="form-group mx-3">
                <button class="btn btn-success" onClick="javascript:agregarCamposClientesSociosAccionistas('beneficiarios_cliente')">Agregar</button>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1">
                <div class="tag-badge tag-badge--theme badge_formulario mb-2">
                    PERSONA POLÍTICAMENTE EXPUESTA
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <label>¿Por su cargo o actividad maneja recursos públicos? *</label>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recursos_publicos" id="recursos_publicos_si" value="1">
                            <label class="form-check-label" for="recursos_publicos_si">
                                Si
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recursos_publicos" id="recursos_publicos_no" value="0">
                            <label class="form-check-label" for="recursos_publicos_no">
                                No
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="recursos_publicos_cliente">Cuál/quién?</label>
                        <input type="text" class="form-control" id="recursos_publicos_cliente">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <label>¿Por su cargo o actividad ejerce algún grado de poder público? *</label>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="poder_publico" id="poder_publico_si">
                            <label class="form-check-label" for="poder_publico_si">
                                Si
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="poder_publico" id="poder_publico_no">
                            <label class="form-check-label" for="poder_publico_no">
                                No
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="poder_publico_cliente">Cuál/quién?</label>
                        <input type="text" class="form-control" id="poder_publico_cliente">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <label>Por su actividad u oficio, goza usted de reconocimiento público *</label>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reconocimiento_publico" id="reconocimiento_publico_si">
                            <label class="form-check-label" for="reconocimiento_publico_si">
                                Si
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reconocimiento_publico" id="reconocimiento_publico_no">
                            <label class="form-check-label" for="reconocimiento_publico_no">
                                No
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="reconocimiento_publico_cliente">Cuál/quién?</label>
                        <input type="text" class="form-control" id="reconocimiento_publico_cliente">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <label>Existe algún vínculo entre usted y una persona considerada expuesta *</label>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="persona_expuesta" id="persona_expuesta_si">
                            <label class="form-check-label" for="persona_expuesta_si">
                                Si
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="persona_expuesta" id="persona_expuesta_no">
                            <label class="form-check-label" for="persona_expuesta_no">
                                No
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="persona_expuesta_cliente">Cuál/quién?</label>
                        <input type="text" class="form-control" id="persona_expuesta_cliente">
                    </div>
                </div>
            </div>

            <div class="card-divider"></div>
            <div class="card-body card-body--padding--1">
                <div class="tag-badge tag-badge--theme badge_formulario mb-2">
                    INFORMACIÓN FINANCIERA
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="solicitud_ingresos_mensuales">Ingresos mensuales (pesos)</label>
                        <input type="number" class="form-control" id="solicitud_ingresos_mensuales">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_egresos_mensuales">Egresos mensuales (pesos)</label>
                        <input type="number" class="form-control" id="solicitud_egresos_mensuales">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="solicitud_activos">Activos (pesos)</label>
                        <input type="number" class="form-control" id="solicitud_activos">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_pasivos">Pasivos (pesos)</label>
                        <input type="number" class="form-control" id="solicitud_pasivos">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="solicitud_otros_ingresos">Otros ingresos (pesos)</label>
                        <input type="number" class="form-control" id="solicitud_otros_ingresos">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="solicitud_concepto_otros_ingresos">Concepto otros ingresos</label>
                        <input type="number" class="form-control" id="solicitud_concepto_otros_ingresos">
                    </div>
                </div>
            </div>


            <div class="card-body card-body--padding--1">
                <div class="tag-badge tag-badge--theme badge_formulario mb-2">
                    Documentos requeridos
                </div>
                <div class="container">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th rowspan="2">Documentos</th>
                            </tr>
                            <tr>
                                <th>Persona natural</th>
                                <th>Persona jurídica</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Fotocopia cédula del Representante Legal</td>
                                <td class="text-center">X</td>
                                <td class="text-center">X</td>
                            </tr>
                            <tr>
                                <td>Fotocopia RUT</td>
                                <td class="text-center">X</td>
                                <td class="text-center">X</td>
                            </tr>
                            <tr>
                                <td>Fotocopia Cámara de Comercio (no mayor a 30 días)</td>
                                <td class="text-center"></td>
                                <td class="text-center">X</td>
                            </tr>
                            <tr>
                                <td>Extractos bancarios últimos 3 meses</td>
                                <td class="text-center"></td>
                                <td class="text-center">X</td>
                            </tr>
                            <tr>
                                <td>2 referencias comerciales</td>
                                <td class="text-center"></td>
                                <td class="text-center">X</td>
                            </tr>
                            <tr>
                                <td>Estados financieros año anterior</td>
                                <td class="text-center"></td>
                                <td class="text-center">X</td>
                            </tr>
                            <tr>
                                <td>Declaración de renta año anterior</td>
                                <td class="text-center"></td>
                                <td class="text-center">X</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="file-loading">
                    <input id="subir_archivos" type="file" data-browse-on-zone-click="true" multiple>
                </div>

                <div class="card-divider"></div>
                <label class="form-check-label mt-2" for="solicitud_preferencia_enlace1">
                    Deseo recibir el enlace para firmar la solicitud por:
                </label>
                <div class="form-row mt-2">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="solicitud_preferencia_enlace" id="solicitud_preferencia_enlace1" value="1">
                            <label class="form-check-label" for="solicitud_preferencia_enlace1">
                                WhatsApp
                            </label>
                        </div>
                    </div>
                
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="solicitud_preferencia_enlace" id="solicitud_preferencia_enlace2" value="2">
                            <label class="form-check-label" for="solicitud_preferencia_enlace2">
                                Correo electrónico
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group mb-2 mt-2 mx-3 my-2">
                <button class="btn btn-primary btn-block" onClick="javascript:crearSolicitudCredito()">ENVIAR SOLICITUD DE CRÉDITO</button>
            </div>
        </div>
    </div>
</div>

<script>
    agregarCamposClientesSociosAccionistas = (elemento) => {
        let index = $(`#${elemento} .form-row`).length

        $(`#${elemento}`).append(`
            <div class="form-row" id="${elemento}_${index}">
                <div class="form-group col-md-3">
                    <input type="text" class="form-control" id="${elemento}_nombre_${index}">
                </div>

                <div class="form-group col-md-3">
                    <select id="${elemento}_tipo_identificacion_${index}" class="form-control">
                        <option value="">Selecciona...</option>
                        <option value="C" data-tipo_tercero="1">Cédula de ciudadanía</option>
                        <option value="N" data-tipo_tercero="2">NIT</option>
                        <option value="E" data-tipo_tercero="1">Cédula de extranjería</option>
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <input type="text" class="form-control" id="${elemento}_numero_documento_${index}">
                </div>

                <div class="form-group col-md-3">
                    <input type="text" class="form-control" id="${elemento}_porcentaje_participacion_${index}">
                </div>
            </div>
        `)
    }

    agregarCamposPersonaAutorizada = (elemento) => {
        let index = $(`#${elemento} .form-row`).length

        $(`#${elemento}`).append(`
            <div class="form-row" id="${elemento}_${index}">
                <div class="form-group col-md-4">
                    <input type="text" class="form-control" id="${elemento}_nombre_${index}">
                </div>

                <div class="form-group col-md-4">
                    <input type="text" class="form-control" id="${elemento}_identificacion_${index}">
                </div>

                <div class="form-group col-md-4">
                    <input type="text" class="form-control" id="${elemento}_celular_${index}">
                </div>
            </div>
        `)
    }

    obtenerClientesSociosAccionistas = (elemento, solicitudId = null) => {
        let registros = []

        $(`div[id^='${elemento}_']`).each(function(index) {
            let valores = 0

            let registro = {
                nombre: $(`#${elemento}_nombre_${index}`).val(),
                identificacion_tipo_id: $(`#${elemento}_tipo_identificacion_${index} option:selected`).attr('data-tipo_tercero'),
                documento_numero: $(`#${elemento}_numero_documento_${index}`).val(),
                porcentaje_participacion: $(`#${elemento}_porcentaje_participacion_${index}`).val()
            }

            Object.values(registro).forEach(valor => { if(valor) valores+=1 })
            if (valores > 0) {
                registro.solicitud_id = solicitudId
                registro.formulario_tipo = (elemento === 'clientes') ? 1: 2
                registros.push(registro)
            }
        })

        return registros
    }

    obtenerCamposPersonasAutorizadas = (elemento, solicitudId) => {
        let registros = []

        $(`div[id^='${elemento}_']`).each(function(index) {
            let valores = 0

            let registro = {
                nombre: $(`#${elemento}_nombre_${index}`).val(),
                documento_numero: $(`#${elemento}_identificacion_${index}`).val(),
                celular: $(`#${elemento}_celular_${index}`).val(),
            }

            Object.values(registro).forEach(valor => { if(valor) valores+=1 })
            if (valores > 0) {
                registro.solicitud_id = solicitudId
                registro.formulario_tipo = 3
                registros.push(registro)
            }
        })

        return registros
    }

    crearSolicitudCredito = async() => {
        let camposObligatorios = [
            $('#solicitud_persona_tipo'),
            $('#solicitud_nombre'),
            $('#solicitud_numero_documento'),
            $('#solicitud_direccion'),
            $('#solicitud_email'),
            $('#solicitud_celular'),
            $('#solicitud_correo_facturacion_electronica')
        ]

        let camposRadioObligatorios = [
            'solicitud_nueva',
            'solicitud_tipo_documento',
            'recursos_publicos',
            'reconocimiento_publico',
            'persona_expuesta',
            'poder_publico',
            'solicitud_preferencia_enlace',
        ]

        // Si es persona jurídica
        if ($('#solicitud_persona_tipo').val() == 2) {
            camposObligatorios.push($('#solicitud_representante_legal'))
            camposObligatorios.push($('#solicitud_representante_legal_documento'))
        }

        if (!validarCamposObligatorios(camposObligatorios)) return false
        if (!validarCamposTipoRadio(camposRadioObligatorios)) return false

        if ($('#solicitud_persona_tipo').val() == 2 && obtenerClientesSociosAccionistas('clientes').length < 1) {
            mostrarAviso('alerta', `¡Debe rellenar la sección de socios y/o accionistas!`, 20000)
            return false
        }

        if ($("#subir_archivos").fileinput('getFilesCount') < 1) {
            mostrarAviso('alerta', `¡Debe seleccionar los archivos para poder finalizar la solicitud de crédito!`, 20000)
            return false
        }

        let datosSolicitud = {
            tipo: 'clientes_solicitudes_credito',
            nombre: $('#solicitud_nombre').val(),
            persona_tipo_id: $('#solicitud_persona_tipo').val(),
            identificacion_tipo_id: $('#solicitud_tipo_documento option:selected').attr('data-tipo_tercero'),
            documento_numero: $('#solicitud_numero_documento').val(),
            direccion: $('#solicitud_direccion').val(),
            telefono: $('#solicitud_telefono').val(),
            email: $('#solicitud_email').val(),
            celular: $('#solicitud_celular').val(),
            representante_legal: $('#solicitud_representante_legal').val(),
            representante_legal_documento_numero: $('#solicitud_representante_legal_documento').val(),
            email_factura_electronica: $('#solicitud_correo_facturacion_electronica').val(),
            tesoreria_nombre: $('#tesoreria_nombre').val(),
            tesoreria_email: $('#tesoreria_email').val(),
            tesoreria_telefono: $('#tesoreria_telefono').val(),
            tesoreria_celular: $('#tesoreria_celular').val(),
            comercial_nombre: $('#comercial_nombre').val(),
            comercial_email: $('#comercial_email').val(),
            comercial_telefono: $('#comercial_telefono').val(),
            comercial_celular: $('#comercial_celular').val(),
            contabilidad_nombre: $('#contabilidad_nombre').val(),
            contabilidad_email: $('#contabilidad_email').val(),
            contabilidad_telefono: $('#contabilidad_telefono').val(),
            contabilidad_celular: $('#contabilidad_celular').val(),
            referencia_comercial_entidad1: $('#referencia_comercial_entidad1').val(),
            referencia_comercial_cel1: $('#referencia_comercial_celular1').val(),
            referencia_comercial_direccion1: $('#referencia_comercial_direccion1').val(),
            referencia_comercial_entidad2: $('#referencia_comercial_entidad2').val(),
            referencia_comercial_cel2: $('#referencia_comercial_celular2').val(),
            referencia_comercial_direccion2: $('#referencia_comercial_direccion2').val(),
            referencia_bancaria_entidad: $('#referencia_bancaria_entidad').val(),
            referencia_bancaria_tipo: $('#referencia_bancaria_tipo').val(),
            referencia_bancaria_numero: $('#referencia_bancaria_numero').val(),
            reconocimiento_publico: ($(`#reconocimiento_publico_si`).is(':checked')) ? 1: 0,
            reconocimiento_publico_cual: $('#reconocimiento_publico_cliente').val(),
            persona_expuesta: ($(`#persona_expuesta_si`).is(':checked')) ? 1: 0,
            persona_expuesta_cual: $('#persona_expuesta_cliente').val(),
            poder_publico: ($(`#poder_publico_si`).is(':checked')) ? 1: 0,
            poder_publico_cual: $('#poder_publico_cliente').val(),
            recursos_publicos: ($(`#recursos_publicos_si`).is(':checked')) ? 1: 0,
            recursos_publicos_cual: $('#recursos_publicos_cliente').val(),
            ingresos_mensuales: $('#solicitud_ingresos_mensuales').val(),
            egresos_mensuales: $('#solicitud_egresos_mensuales').val(),
            activos: $('#solicitud_activos').val(),
            pasivos: $('#solicitud_pasivos').val(),
            otros_ingresos: $('#solicitud_otros_ingresos').val(),
            concepto_otros_ingresos: $('#solicitud_concepto_otros_ingresos').val(),
            nueva: ($(`#solicitud_nueva1`).is(':checked')) ? 1: 0,
            preferencia_enlace: ($(`#solicitud_preferencia_enlace1`).is(':checked')) ? 1: 2,
        }

        Swal.fire({
            title: 'Estamos creando la solicitud de crédito en nuestros sistemas...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        // Se crea el usuario
        let solicitudId = await consulta('crear', datosSolicitud, false)

        Swal.close()

        let personasAutorizadas = obtenerCamposPersonasAutorizadas("personas_autorizadas", solicitudId.resultado)
        let sociosAccionistas = obtenerClientesSociosAccionistas('clientes', solicitudId.resultado)
        let beneficicariosSociosAccionistas = obtenerClientesSociosAccionistas('beneficiarios_cliente', solicitudId.resultado)

        if (personasAutorizadas.length > 0) consulta('crear', {tipo: "clientes_solicitudes_credito_detalle", valores: personasAutorizadas}, false)
        if (sociosAccionistas.length > 0) consulta('crear', {tipo: "clientes_solicitudes_credito_detalle", valores: sociosAccionistas}, false)
        if (beneficicariosSociosAccionistas.length > 0) consulta('crear', {tipo: "clientes_solicitudes_credito_detalle", valores: beneficicariosSociosAccionistas}, false)

        $('#subir_archivos').data('fileinput').uploadUrl = `${$("#site_url").val()}/clientes/subir/${solicitudId.resultado}`
        $('#subir_archivos').fileinput('upload')
    
        mostrarAviso('exito', `
            ¡Tu solicitud de crédito ha sido creada correctamente!
        `, 20000)

        $('#subir_archivos').on('fileuploaded', async function() {
            await obtenerPromesa(`${$("#site_url").val()}reportes/pdf/solicitud_credito/${solicitudId.resultado}`)

            // Se envía un correo electrónico de notificación
            obtenerPromesa(`${$('#site_url').val()}interfaces/enviar_email`, {tipo: 'solicitud_credito', id: solicitudId.resultado})
        })
    }

    $().ready(() => {
        $("#subir_archivos").fileinput({
            language: "es",
            uploadUrl: `${$("#site_url").val()}/clientes/subir`,
            enableResumableUpload: true,
            initialPreviewAsData: true,
            showDownload: true,
            showUpload: false,
            sizeUnits: ['MB'],
            maxFileSize: 3072,
        })
    })
</script>