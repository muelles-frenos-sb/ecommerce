<?php
if (isset($id)) {
    $campania = $this->marketing_model->obtener('marketing_campanias', ['id' => $id]);
    echo "<input type='hidden' id='campania_id' value='$campania->id' />";
}
?>

<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <div class="form-group col-6 col-md-6">
                        <label for="fecha_inicio">Fecha de inicio *</label>
                        <input type="date" class="form-control" id="fecha_inicio" value="<?php echo (isset($campania)) ? $campania->fecha_inicio : date('Y-m-d') ; ?>">
                    </div>
                    <div class="form-group col-6 col-md-6">
                        <label for="fecha_finalizacion">Fecha finalización *</label>
                        <input type="date" class="form-control" id="fecha_finalizacion" value="<?php echo (isset($campania)) ? $campania->fecha_finalizacion : date('Y-m-d') ; ?>">
                    </div>
                    <div class="form-group col-6 col-md-6">
                        <label for="campania_nombre">Nombre *</label>
                        <input type="text" class="form-control" id="campania_nombre" value="<?php echo (isset($campania) ? $campania->nombre : '')?>">
                    </div>
                    <div class="form-group col-6 col-md-6">
                        <label for="campania_descripcion">Descripción </label>
                        <input type="text" class="form-control" id="campania_descripcion" value="<?php echo (isset($campania) ? $campania->descripcion : '')?>">
                    </div>
                </div>

                <button class="btn btn-info" onClick="javascript:history.back()">Volver</button>
                <button class="btn btn-success" onclick="javascript:guardarCampania()">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    guardarCampania = async() => {
        let id = $("#campania_id").val()

        let camposObligatorios = [
            $("#fecha_inicio"),
            $("#fecha_finalizacion"),
            $("#campania_nombre")
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datos = {
            tipo: 'marketing_campanias',
            fecha_inicio: $("#fecha_inicio").val(),
            fecha_finalizacion: $("#fecha_finalizacion").val(),
            nombre: $("#campania_nombre").val(),
            descripcion: $("#campania_descripcion").val()
        }

        if (id) {
            datos.id = id
            await consulta('actualizar', datos)
        } else {
            await consulta('crear', datos)
        }
    }
</script>