<?php
// 1. Lógica de carga para EDICIÓN
if(isset($id)) {
    // Usamos el modelo para buscar en la nueva tabla
    $anticipo = $this->importaciones_model->obtener('importaciones_maestro_anticipos', ['id' => $id]);
    
    // Guardamos el ID oculto si existe
    if($anticipo) {
        echo "<input type='hidden' id='anticipo_id' value='$anticipo->id' />";
    }
}
?>

<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-header">
                <h5><?php echo isset($anticipo) ? 'Editar Anticipo' : 'Nuevo Anticipo Proveedor'; ?></h5>
            </div>
            <div class="card-body card-body--padding--2">
                
                <div class="form-row">
                    
                    <div class="form-group col-12 col-md-4">
                        <label for="nit">NIT / Identificación *</label>
                        <input type="text" class="form-control" id="nit" 
                               placeholder="Ej: 900.123.456-1">
                    </div>

                    <div class="form-group col-12 col-md-4">
                        <label for="proveedor">Nombre Proveedor (Razón Social) *</label>
                        <input type="text" class="form-control" id="proveedor" 
                               placeholder="Nombre de la empresa">
                    </div>

                    <div class="form-group col-12 col-md-4">
                        <label for="porcentaje">Porcentaje Anticipo (%) *</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="porcentaje" 
                                   step="0.01" min="0" max="100" placeholder="Ej: 30">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="form-group mb-0 pt-3 mt-3 text-right">
                    <button class="btn btn-secondary" onClick="javascript:history.back()">Cancelar / Volver</button>
                    <button class="btn btn-success" onClick="javascript:guardarAnticipo()">
                        <i class="fas fa-save"></i> Guardar Datos
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<?php if (isset($anticipo) && $anticipo) { ?>
    <script>
        $().ready(() => {
            $('#nit').val('<?php echo $anticipo->nit; ?>');
            $('#proveedor').val('<?php echo $anticipo->proveedor; ?>');
            $('#porcentaje').val('<?php echo $anticipo->porcentaje; ?>');
        })
    </script>
<?php } ?>

<script>
    guardarAnticipo = async () => {
        // 1. Validar campos obligatorios
        let camposObligatorios = [
            $('#nit'),
            $('#proveedor'),
            $('#porcentaje')
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        // 2. Construir objeto para enviar a la BD
        let datos = {
            tipo: 'importaciones_maestro_anticipos', // Nombre exacto de la tabla
            nit: $('#nit').val(),
            proveedor: $('#proveedor').val(),
            porcentaje: parseFloat($('#porcentaje').val()) || 0
        }

        // 3. Detectar si es Creación o Actualización
        let idExistente = $('#anticipo_id').val();

        try {
            if (!idExistente) {
                // === CREAR ===
                await consulta('crear', datos);
                // Opcional: Limpiar campos tras guardar si deseas
                // $('#nit, #proveedor, #porcentaje').val('');
            } else {
                // === ACTUALIZAR ===
                datos.id = idExistente;
                await consulta('actualizar', datos);
            }
            
            // Redirigir a la lista después de guardar
            setTimeout(() => {
                 // Ajusta esta ruta si tu controlador usa otro nombre para la lista
                 window.location.href = '<?php echo site_url("importaciones/maestro"); ?>';
            }, 1000);

        } catch (error) {
            console.error("Error al guardar:", error);
        }
    }

    // Ya no inicializamos select2 porque son inputs normales
    $().ready(() => {
        $('#nit').focus(); // Poner el cursor en el primer campo
    })
</script>