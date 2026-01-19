<?php
// 1. Lógica para DETECTAR si es EDICIÓN o CREACIÓN
$id_importacion = $this->uri->segment(3); // Asumiendo ruta: importaciones/editar/123
$titulo = "Nueva Importación";
$data_imp = null;

if($id_importacion) {
    // Usamos el modelo que creamos anteriormente
    $data_imp = $this->importaciones_model->obtener('importaciones', ['id' => $id_importacion]);
    if($data_imp) {
        $titulo = "Editar Importación #" . str_pad($data_imp->id, 3, '0', STR_PAD_LEFT);
    }
}
?>

<input type="hidden" id="importacion_id" value="<?php echo ($data_imp) ? $data_imp->id : ''; ?>" />

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

            <div class="form-group col-md-6">
                <label for="numero_orden_compra">Número Orden de Compra *</label>
                <input type="text" class="form-control" id="numero_orden_compra" 
                       value="<?php echo ($data_imp) ? $data_imp->numero_orden_compra : ''; ?>" 
                       placeholder="Ej: PO-2026-001" autofocus>
            </div>

            <div class="form-group col-md-6">
                <label for="razon_social">Proveedor (Razón Social) *</label>
                <input type="text" class="form-control" id="razon_social" 
                       value="<?php echo ($data_imp) ? $data_imp->razon_social : ''; ?>" 
                       placeholder="Nombre del proveedor">
            </div>

            <div class="col-12 mb-3 mt-2">
                <h6 class="text-primary border-bottom pb-2">Datos Logísticos</h6>
            </div>

            <div class="form-group col-md-4">
                <label for="pais_origen">País de Origen</label>
                <select id="pais_origen" class="form-control">
                    <option value="">Selecciona...</option>
                    <?php 
                    $paises = ['China', 'Estados Unidos', 'Panamá', 'Alemania', 'Japón', 'Brasil'];
                    foreach($paises as $pais) {
                        $selected = ($data_imp && $data_imp->pais_origen == $pais) ? 'selected' : '';
                        echo "<option value='$pais' $selected>$pais</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group col-md-4">
                <label for="fecha_estimada_llegada">Fecha Estimada de Llegada</label>
                <input type="date" class="form-control" id="fecha_estimada_llegada" 
                       value="<?php echo ($data_imp) ? date('Y-m-d', strtotime($data_imp->fecha_estimada_llegada)) : ''; ?>">
            </div>

            <div class="form-group col-md-4">
                <label for="bl_awb">BL / AWB (Documento Transporte)</label>
                <input type="text" class="form-control" id="bl_awb" 
                       value="<?php echo ($data_imp) ? $data_imp->bl_awb : ''; ?>" 
                       placeholder="Bill of Lading / Air Waybill">
            </div>

            <div class="form-group col-md-4">
                <label for="estado">Estado Actual</label>
                <select id="estado" class="form-control">
                    <?php 
                    $estados = ['En Tránsito', 'Nacionalizado', 'Entregado', 'Cancelado', 'En Bodega Miami'];
                    foreach($estados as $est) {
                        // Si es nuevo, por defecto 'En Tránsito'
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

            <div class="form-group col-md-4">
                <label for="moneda_preferida">Moneda *</label>
                <select id="moneda_preferida" class="form-control">
                    <option value="USD" <?php echo ($data_imp && $data_imp->moneda_preferida == 'USD') ? 'selected' : ''; ?>>Dólar (USD)</option>
                    <option value="COP" <?php echo ($data_imp && $data_imp->moneda_preferida == 'COP') ? 'selected' : ''; ?>>Peso Col (COP)</option>
                    <option value="EUR" <?php echo ($data_imp && $data_imp->moneda_preferida == 'EUR') ? 'selected' : ''; ?>>Euro (EUR)</option>
                </select>
            </div>

            <div class="form-group col-md-4">
                <label for="valor_total">Valor Total (Moneda Extranjera) *</label>
                <input type="number" step="0.01" class="form-control" id="valor_total" 
                       value="<?php echo ($data_imp) ? $data_imp->valor_total : ''; ?>" placeholder="0.00">
            </div>

            <div class="form-group col-md-4">
                <label for="valor_total_cop">Valor Aprox en Pesos (COP)</label>
                <input type="number" step="0.01" class="form-control" id="valor_total_cop" 
                       value="<?php echo ($data_imp) ? $data_imp->valor_total_cop : ''; ?>" placeholder="0">
                <small class="text-muted">Referencial para contabilidad local.</small>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="notas_internas">Notas Internas / Observaciones</label>
                    <textarea class="form-control" id="notas_internas" rows="3"><?php echo ($data_imp) ? $data_imp->notas_internas : ''; ?></textarea>
                </div>
            </div>

            <div class="col-12 text-right mt-3">
                <a class="btn btn-secondary" href="<?php echo site_url("importaciones"); ?>">Cancelar</a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar Importación
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    guardarImportacion = () => {
        // 1. Validar campos obligatorios
        let camposObligatorios = [
            $('#numero_orden_compra'),
            $('#razon_social'),
            $('#valor_total'),
            $('#moneda_preferida')
        ];

        if (!validarCamposObligatorios(camposObligatorios)) return false;

        // 2. Recoger datos
        let datosFormulario = {
            id: $('#importacion_id').val(), // IMPORTANTE: El ID oculto
            numero_orden_compra: $('#numero_orden_compra').val(),
            razon_social: $('#razon_social').val(),
            pais_origen: $('#pais_origen').val(),
            fecha_estimada_llegada: $('#fecha_estimada_llegada').val(),
            bl_awb: $('#bl_awb').val(),
            estado: $('#estado').val(),
            moneda_preferida: $('#moneda_preferida').val(),
            valor_total: $('#valor_total').val(),
            valor_total_cop: $('#valor_total_cop').val() || 0,
            notas_internas: $('#notas_internas').val()
        };

        // 3. Alerta de carga
        Swal.fire({
            title: 'Guardando...',
            text: 'Procesando la información',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading() }
        });

        // 4. Petición AJAX directa al controlador
        $.ajax({
            url: '<?php echo site_url("importaciones/guardar"); ?>',
            method: 'POST',
            data: datosFormulario,
            dataType: 'json',
            success: function(respuesta) {
                Swal.close();

                if (respuesta.status === 'success') {
                    mostrarAviso('exito', respuesta.mensaje);
                    
                    // Redirigir tras 1.5 segundos
                    setTimeout(() => {
                        window.location.href = '<?php echo site_url("importaciones"); ?>';
                    }, 1500);
                } else {
                    mostrarAviso('error', respuesta.mensaje);
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                console.error(xhr.responseText);
                mostrarAviso('error', 'Error de conexión con el servidor. Revisa la consola.');
            }
        });
    }

    // Inicialización
    $(document).ready(function() {
        $('form').submit(function(e) {
            e.preventDefault();
            guardarImportacion();
        });
    });
</script>