<?php
if(isset($id)) {
    $proveedor_marca = $this->proveedores_model->obtener('proveedores_marcas', ['id' => $id]);
    echo "<input type='hidden' id='proveedor_marca_id' value='$proveedor_marca->id' />";
}
?>

<div class="block-space block-space--layout--after-header"></div>
<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <div class="form-group col-12 col-sm-6">
                        <label for="proveedor_nit">Proveedor nit *</label>
                        <input type="text" class="form-control" id="proveedor_nit" value="<?php if(!empty($proveedor_marca)) echo $proveedor_marca->proveedor_nit; ?>">
                    </div>
                    <div class="form-group col-12 col-sm-6">
                        <label for="codigo_marca">Código marca *</label>
                        <input type="text" class="form-control" id="codigo_marca" value="<?php if(!empty($proveedor_marca)) echo $proveedor_marca->marca_codigo; ?>">
                    </div>
                </div>
                <div class="form-group mb-0 pt-3 mt-3">
                    <button class="btn btn-info" onClick="javascript:history.back()">Volver</button>
                    <button class="btn btn-success" onClick="javascript:guardarProveedoresMarcas()">Guardar datos</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    guardarProveedoresMarcas = async () => {
        let camposObligatorios = [
            $('#proveedor_nit'),
            $('#codigo_marca')
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datosProveedoresMarcas = {
            tipo: 'proveedores_marcas',
            proveedor_nit: $('#proveedor_nit').val(),
            marca_codigo: $('#codigo_marca').val()
        }

        if(!$('#proveedor_marca_id').val()) {
            await consulta('crear', datosProveedoresMarcas)
        } else {
            datosProveedoresMarcas.id = $('#proveedor_marca_id').val()
            await consulta('actualizar', datosProveedoresMarcas)
        }
    }
</script>