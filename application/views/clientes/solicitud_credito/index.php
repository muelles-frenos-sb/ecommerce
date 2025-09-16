<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de solicitudes de crédito</h1>
        </div>
    </div>
</div>

<div class="w-100 p-5">
    <div id="contenedor_solicitudes_credito"></div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    listarSolicitudesCredito = () => {
        cargarInterfaz('clientes/solicitud_credito/lista', 'contenedor_solicitudes_credito')
    }

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

    $().ready(() => {
        listarSolicitudesCredito()
    })
</script>