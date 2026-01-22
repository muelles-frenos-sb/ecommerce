<?php
// 1. Lógica para DETECTAR si es EDICIÓN o CREACIÓN
$id_importacion = $this->uri->segment(3); 
$titulo = "Nueva Importación";
$data_imp = null;
$data_pago = null; // Variable para almacenar el pago si existe

if($id_importacion) {
    // Usamos el modelo cargado para la importación
    $data_imp = $this->importaciones_model->obtener('importaciones', ['id' => $id_importacion]);
    
    if($data_imp) {
        $titulo = "Editar Importación #" . str_pad($data_imp->id, 3, '0', STR_PAD_LEFT);
        
        // --- NUEVO: BUSCAR SI YA TIENE UN PAGO DE ANTICIPO ---
        // Asumimos que buscas un pago asociado a esta importación. 
        // Si tienes múltiples pagos, esto traerá el primero que encuentre.
        $data_pago = $this->importaciones_model->obtener('importaciones_pagos', ['importacion_id' => $data_imp->id]);
    }
}
?>

<input type="hidden" id="importacion_id" value="<?php echo ($data_imp) ? $data_imp->id : ''; ?>" />
<input type="hidden" id="pago_id" value="<?php echo ($data_pago) ? $data_pago[0]->id : ''; ?>" />

<div class="card">
    <div class="card-header">
        <h5><?php echo $titulo; ?></h5>
    </div>
    <div class="card-divider"></div>
    <div class="card-body card-body--padding--2">
        <form class="row">
            
            <div class="col-12 mb-3">
                <h6 class="text-primary border-bottom pb-2">Información del Proveedor y Orden</h6>
            </div>

            <div class="form-group col-md-4">
                <label for="numero_orden_compra">Número Orden de Compra *</label>
                <input type="text" class="form-control" id="numero_orden_compra" 
                       value="<?php echo ($data_imp) ? $data_imp->numero_orden_compra : ''; ?>" 
                       placeholder="Ej: PO-2026-001" autofocus>
            </div>

            <div class="form-group col-md-4">
                <label for="razon_social">Proveedor (Razón Social) *</label>
                <input type="text" class="form-control" id="razon_social" 
                       value="<?php echo ($data_imp) ? $data_imp->razon_social : ''; ?>" 
                       placeholder="Nombre del proveedor">
            </div>

            <div class="form-group col-md-4">
                <label for="contacto_principal">Contacto Principal</label>
                <input type="text" class="form-control" id="contacto_principal" 
                       value="<?php echo ($data_imp) ? $data_imp->contacto_principal : ''; ?>" 
                       placeholder="Persona de contacto">
            </div>

            <div class="form-group col-md-4">
                <label for="email_contacto">Email de Contacto</label>
                <input type="email" class="form-control" id="email_contacto" 
                       value="<?php echo ($data_imp) ? $data_imp->email_contacto : ''; ?>" 
                       placeholder="correo@proveedor.com">
            </div>

            <div class="form-group col-md-4">
                <label for="telefono_contacto">Teléfono de Contacto</label>
                <input type="text" class="form-control" id="telefono_contacto" 
                       value="<?php echo ($data_imp) ? $data_imp->telefono_contacto : ''; ?>" 
                       placeholder="+1 555 000 000">
            </div>

            <div class="form-group col-md-4">
                <label for="direccion">Dirección Física</label>
                <textarea class="form-control" id="direccion" rows="2" placeholder="Dirección completa..."><?php echo ($data_imp) ? $data_imp->direccion : ''; ?></textarea>
            </div>

            <div class="col-12 mb-3 mt-2">
                <h6 class="text-primary border-bottom pb-2">Datos Logísticos</h6>
            </div>

            <div class="form-group col-md-3">
                <label for="pais_origen">País de Origen *</label>
                <select id="pais_origen" class="form-control" 
                        data-valor-actual="<?php echo ($data_imp) ? $data_imp->pais_origen : ''; ?>">
                    <option value="">Cargando países...</option>
                </select>
            </div>

            <div class="form-group col-md-3">
                <label for="fecha_estimada_llegada">Fecha Estimada Llegada</label>
                <input type="date" class="form-control" id="fecha_estimada_llegada" 
                       value="<?php echo ($data_imp) ? date('Y-m-d', strtotime($data_imp->fecha_estimada_llegada)) : ''; ?>">
            </div>

            <div class="form-group col-md-3">
                <label for="fecha_ingreso_siesa">Fecha Ingreso SIESA</label>
                <input type="date" class="form-control" id="fecha_ingreso_siesa" 
                       value="<?php echo ($data_imp && $data_imp->fecha_ingreso_siesa) ? date('Y-m-d', strtotime($data_imp->fecha_ingreso_siesa)) : ''; ?>">
                <small class="form-text text-muted">Opcional</small>
            </div>

            <div class="form-group col-md-3">
                <label for="bl_awb">BL / AWB</label>
                <input type="text" class="form-control" id="bl_awb" 
                       value="<?php echo ($data_imp) ? $data_imp->bl_awb : ''; ?>" 
                       placeholder="Bill of Lading / Air Waybill">
            </div>

            <div class="form-group col-md-3">
                <label for="estado">Estado Actual</label>
                <select id="estado" class="form-control">
                    <?php 
                    $estados = ['En Tránsito', 'Nacionalizado', 'Entregado', 'Cancelado', 'En Bodega Miami'];
                    foreach($estados as $est) {
                        $defecto = (!$data_imp && $est == 'En Tránsito') ? 'selected' : '';
                        $selected = ($data_imp && $data_imp->estado == $est) ? 'selected' : $defecto;
                        echo "<option value='$est' $selected>$est</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-12 mb-3 mt-2">
                <h6 class="text-primary border-bottom pb-2">Valores y Moneda</h6>
            </div>

            <div class="form-group col-md-3">
                <label for="moneda_preferida">Moneda *</label>
                <select id="moneda_preferida" class="form-control">
                    <option value="USD" <?php echo ($data_imp && $data_imp->moneda_preferida == 'USD') ? 'selected' : ''; ?>>Dólar (USD)</option>
                    <option value="COP" <?php echo ($data_imp && $data_imp->moneda_preferida == 'COP') ? 'selected' : ''; ?>>Peso Col (COP)</option>
                    <option value="EUR" <?php echo ($data_imp && $data_imp->moneda_preferida == 'EUR') ? 'selected' : ''; ?>>Euro (EUR)</option>
                </select>
            </div>

            <div class="form-group col-md-3">
                <label for="valor_trm">Valor TRM</label>
                <input type="number" step="0.01" class="form-control" id="valor_trm" 
                       value="<?php echo ($data_imp) ? $data_imp->valor_trm : ''; ?>" placeholder="0.00">
            </div>

            <div class="form-group col-md-3">
                <label for="valor_total">Valor Total (Moneda Ext.) *</label>
                <input type="number" step="0.01" class="form-control" id="valor_total" 
                       value="<?php echo ($data_imp) ? $data_imp->valor_total : ''; ?>" placeholder="0.00">
            </div>

            <div class="form-group col-md-3">
                <label for="valor_total_cop">Valor Aprox (COP)</label>
                <input type="number" step="0.01" class="form-control" id="valor_total_cop" 
                       value="<?php echo ($data_imp) ? $data_imp->valor_total_cop : ''; ?>" placeholder="0">
            </div>

            <div class="form-group col-md-3">
                <label for="impuestos_dian">Impuestos DIAN</label>
                <input type="number" step="0.01" class="form-control" id="impuestos_dian" 
                       value="<?php echo ($data_imp) ? $data_imp->impuestos_dian : ''; ?>" placeholder="0.00">
            </div>

            <div class="col-12 mt-2">
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <?php 
                            $tiene_anticipo = ($data_imp && ($data_imp->requiere_anticipo == 1 || $data_imp->porcentaje_anticipo > 0)) ? 'checked' : ''; 
                        ?>
                        <input type="checkbox" class="custom-control-input" id="requiere_anticipo" onchange="toggleAnticipo()" <?php echo $tiene_anticipo; ?>>
                        <label class="custom-control-label font-weight-bold" for="requiere_anticipo">¿Requiere Anticipo?</label>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-3" id="div_porcentaje_anticipo" style="display: none;">
                <label for="porcentaje_anticipo">Porcentaje Anticipo (%)</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="porcentaje_anticipo" 
                           value="<?php echo ($data_imp) ? $data_imp->porcentaje_anticipo : ''; ?>" placeholder="Ej: 30">
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-3">
                <h6 class="text-primary border-bottom pb-2">Observaciones y Condiciones</h6>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="condiciones_pago">Condiciones de Pago</label>
                    <textarea class="form-control" id="condiciones_pago" rows="3" placeholder="Ej: 50% anticipo..."><?php echo ($data_imp) ? $data_imp->condiciones_pago : ''; ?></textarea>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="notas_internas">Notas Internas</label>
                    <textarea class="form-control" id="notas_internas" rows="3"><?php echo ($data_imp) ? $data_imp->notas_internas : ''; ?></textarea>
                </div>
            </div>

            <div class="col-12 text-right mt-3">
                <a class="btn btn-secondary" href="<?php echo site_url("importaciones"); ?>">Cancelar</a>
                <button type="button" class="btn btn-success" onclick="guardarImportacion()">
                    <i class="fas fa-save"></i> Guardar Importación
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    function toggleAnticipo() {
        const check = document.getElementById('requiere_anticipo');
        const divPorcentaje = document.getElementById('div_porcentaje_anticipo');
        
        if (check.checked) {
            divPorcentaje.style.display = 'block';
            document.getElementById('porcentaje_anticipo').focus();
        } else {
            divPorcentaje.style.display = 'none';
        }
    }

    guardarImportacion = async () => {
        // 1. Validar campos obligatorios
        let camposObligatorios = [
            $('#numero_orden_compra'),
            $('#razon_social'),
            $('#valor_total'),
            $('#moneda_preferida'),
            $('#pais_origen')
        ];

        if (!validarCamposObligatorios(camposObligatorios)) return false;

        Swal.fire({
            title: 'Guardando...',
            didOpen: () => { Swal.showLoading() }
        });

        // 2. Obtener valores
        let v_total = parseFloat($('#valor_total').val()) || 0;
        let v_cop   = parseFloat($('#valor_total_cop').val()) || 0;
        let v_impuestos = parseFloat($('#impuestos_dian').val()) || 0;
        let v_trm   = parseFloat($('#valor_trm').val()) || 0;

        let requiereAnticipo = $('#requiere_anticipo').is(':checked');
        let v_porc  = requiereAnticipo ? (parseFloat($('#porcentaje_anticipo').val()) || 0) : 0;
        
        let fechaSiesa = $('#fecha_ingreso_siesa').val();
        if(fechaSiesa === "") fechaSiesa = null;

        // 3. Objeto Principal (Importación)
        var datos = {
            id: $('#importacion_id').val(),
            tipo: 'importaciones', 
            
            numero_orden_compra: $('#numero_orden_compra').val(),
            razon_social:        $('#razon_social').val(),
            contacto_principal:  $('#contacto_principal').val(), 
            email_contacto:      $('#email_contacto').val(),     
            telefono_contacto:   $('#telefono_contacto').val(),  
            direccion:           $('#direccion').val(),          
            
            pais_origen:         $('#pais_origen').val(),
            fecha_estimada_llegada: $('#fecha_estimada_llegada').val(),
            fecha_ingreso_siesa:    fechaSiesa,                  
            bl_awb:              $('#bl_awb').val(),
            estado:              $('#estado').val(),
            
            moneda_preferida:    $('#moneda_preferida').val(),
            valor_total:         v_total,
            valor_total_cop:     v_cop,
            impuestos_dian:      v_impuestos, 
            valor_trm:           v_trm,                          
            
            requiere_anticipo:   requiereAnticipo ? 1 : 0, 
            porcentaje_anticipo: v_porc,
            
            condiciones_pago:    $('#condiciones_pago').val(),   
            notas_internas:      $('#notas_internas').val()
        }

        console.log("Datos Importación:", datos);

        let idImportacion = $('#importacion_id').val();
        let pagoIdExistente = $('#pago_id').val(); // Capturamos ID del pago si existe

        try {
            // ===========================
            // PASO 1: GUARDAR IMPORTACIÓN
            // ===========================
            
            let respuestaImp = null;

            // === EDICIÓN DE IMPORTACIÓN ===
            if (idImportacion && idImportacion !== "") {
                await consulta('actualizar', datos);
                respuestaImp = { resultado: { resultado: idImportacion } }; // Simulamos estructura de respuesta
            } 
            // === CREACIÓN DE IMPORTACIÓN ===
            else {
                datos.fecha_creacion = '<?php echo date("Y-m-d H:i:s"); ?>';
                datos.usuario_id = '<?php echo $this->session->userdata("usuario_id"); ?>';
                respuestaImp = await consulta('crear', datos, false);
                // Asignamos el nuevo ID para usarlo abajo
                idImportacion = respuestaImp.resultado.resultado; 
            }

            // ===========================================
            // PASO 2: GESTIÓN INTELIGENTE DE PAGOS/ANTICIPO
            // ===========================================
            
            if (requiereAnticipo && v_porc > 0 && idImportacion) {
                
                let montoAnticipo = v_total * (v_porc / 100);
                
                // Objeto base del pago
                let datosPago = {
                    tipo: 'importaciones_pagos',
                    importacion_id: idImportacion,
                    estado_id: 1, // Pendiente
                    observaciones: 'Anticipo (' + v_porc + '%) - Valor Total Imp: ' + v_total,
                    valor_moneda_extranjera: montoAnticipo
                };

                // CASO A: SI YA EXISTE UN PAGO -> LO ACTUALIZAMOS
                if (pagoIdExistente && pagoIdExistente !== "") {
                    console.log("Actualizando pago existente ID:", pagoIdExistente);
                    datosPago.id = pagoIdExistente;
                    // Opcional: Agregar fecha de actualización o usuario que edita
                    await consulta('actualizar', datosPago, false);
                } 
                // CASO B: NO EXISTE PAGO -> LO CREAMOS
                else {
                    console.log("Creando nuevo pago de anticipo");
                    datosPago.fecha_creacion = '<?php echo date("Y-m-d H:i:s"); ?>';
                    datosPago.usuario_id = '<?php echo $this->session->userdata("usuario_id"); ?>';
                    await consulta('crear', datosPago, false);
                }
            }

            Swal.close();
            mostrarAviso('exito', 'Datos guardados correctamente.');
            
            setTimeout(() => {
                window.location.href = '<?php echo site_url("importaciones"); ?>';
            }, 1500);

        } catch(error) {
            Swal.close();
            console.error("ERROR CRÍTICO:", error);
            alert("Error al procesar: " + error);
        }
    }

    $(document).ready(async function() {
        try {
            await listarDatos('pais_origen', { tipo: 'paises' }, $('#pais_origen').data('valor-actual'));
        } catch (e) {
            console.warn("Error cargando países", e);
        }
        toggleAnticipo();
    });
</script>