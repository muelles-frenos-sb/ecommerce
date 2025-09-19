<div class="block">
    <div class="container">
        <div class="card mb-lg-0">
            <div class="card-body card-body--padding--2">
                <div class="form-row">
                    <div class="form-group col-12 col-sm-12">
                        <button class="btn btn-primary btn-block" onclick="javascript:realizarEnvioFirmaBot(<?php echo $datos['id']; ?>)"><i class="fas fa-signature"></i> Programar envío de la firma</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    realizarEnvioFirmaBot = async (id) => {
        let confirmacion = await confirmar('enviar', `¿Estás seguro de realizar el envío de la firma?`)
        if(!confirmacion) return false

        let datos = {
            tipo: 'clientes_solicitudes_credito',
            id: id,
            fecha_envio_firma: true
        }

        await consulta('actualizar', datos)

        mostrarAviso('exito', `
            ¡Se realizará el envío de la firma!<br><br>
        `, 5000)

        listarSolicitudesCredito()
    }
</script>