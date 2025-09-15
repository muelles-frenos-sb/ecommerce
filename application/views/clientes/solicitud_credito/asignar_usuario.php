<?php $solicitud = $this->clientes_model->obtener('clientes_solicitudes_credito', ['id' => $datos['id']]); ?>

<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <div class="form-group col-12 col-sm-12">
                        <label for="usuario">Usuarios *</label>
                        <select id="usuario" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach ($this->configuracion_model->obtener("usuarios") as $usuario) echo "<option value='$usuario->id'>$usuario->nombre_completo</option>"; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group mb-0 pt-2 mt-1">
                    <button class="btn btn-success" onClick="javascript:guardarUsuarioAsignado(<?php echo $datos['id']; ?>)">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($solicitud->usuario_asignado_id) { ?>
    <script>
        $().ready(() => {
            $('#usuario').val('<?php echo $solicitud->usuario_asignado_id; ?>')
        })
    </script>
<?php } ?>

<script>
    guardarUsuarioAsignado = async (id) => {
        let camposObligatorios = [
            $('#usuario')
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datos = {
            tipo: 'clientes_solicitudes_credito',
            id: id,
            usuario_asignado_id: $('#usuario').val()
        }

        await consulta('actualizar', datos)

        mostrarAviso('exito', `
            ¡Se ha asignado el usuario correctamente!<br><br>
        `, 5000)
    }

    $().ready(() => {
        $('#usuario').select2()
    })
</script>