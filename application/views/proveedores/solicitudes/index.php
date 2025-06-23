<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Solicitudes de cotización</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div class="mb-4">
            <a class="btn btn-success" href="<?php echo site_url('proveedores/solicitudes/crear'); ?>">Crear</a>
        </div>

        <div id="contenedor_solicitudes"></div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    eliminarSolicitudes = async (id) => {
        let confirmacion = await confirmar('Eliminar', `¿Está seguro de eliminar la solicitud de cotización?`)
        
        if (confirmacion) {
            let eliminar = await consulta('eliminar', {tipo: 'proveedores_cotizaciones_solicitudes', id: id})

            if (eliminar) {
                listarSolicitudes()
            }
        }
    }

    listarSolicitudes = () => {
        cargarInterfaz('proveedores/solicitudes/lista', 'contenedor_solicitudes')
    }

    $().ready(() => {
        listarSolicitudes()
    })
</script>