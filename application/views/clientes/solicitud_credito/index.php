<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de solicitudes de crédito</h1>
        </div>
    </div>
</div>

<div class="w-100 p-5">
    <div id="contenedor_solicitudes_credito"></div>
    <div id="contenedor_asignar_usuario"></div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    cargarAsignarUsuario = (id) => {
        cargarInterfaz('clientes/solicitud_credito/asignar_usuario', 'contenedor_asignar_usuario', {id: id})
    }

    listarSolicitudesCredito = () => {
        cargarInterfaz('clientes/solicitud_credito/lista', 'contenedor_solicitudes_credito')
    }

    $().ready(() => {
        listarSolicitudesCredito()
    })
</script>