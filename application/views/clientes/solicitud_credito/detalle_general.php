<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de solicitudes de crédito</h1>
        </div>
    </div>
</div>

<div class="w-100 p-5">
    <div class="block-zone__widget-header">
        <div class="block-zone__tabs">
            <button type="button" class="block-zone__tabs-button botones-tabs" id="pestana_formulario" onclick="javascript:cargarFormulario(<?php echo $id; ?>)">
                Formulario
            </button>
            <button type="button" class="block-zone__tabs-button botones-tabs" id="pestana_archivos" onclick="javascript:cargarArchivos(<?php echo $id; ?>)">
                Archivos
            </button>
            <button type="button" class="block-zone__tabs-button botones-tabs" id="pestana_asignar" onclick="javascript:cargarAsignarUsuario(<?php echo $id; ?>)">
                Asignar usuario
            </button>
        </div>
    </div>

    <div id="contenedor_detalle"></div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    cargarFormulario = (id) => {
        $(".botones-tabs").removeClass('block-zone__tabs-button--active')

        cargarInterfaz('clientes/solicitud_credito/detalle', 'contenedor_detalle', {id: id})

        $("#pestana_formulario").addClass('block-zone__tabs-button--active')
    }

    cargarArchivos = (id) => {
        $(".botones-tabs").removeClass('block-zone__tabs-button--active')

        cargarInterfaz('clientes/solicitud_credito/archivos', 'contenedor_detalle', {id: id})

        $("#pestana_archivos").addClass('block-zone__tabs-button--active')
    }

    cargarAsignarUsuario = (id) => {
        $(".botones-tabs").removeClass('block-zone__tabs-button--active')

        cargarInterfaz('clientes/solicitud_credito/asignar_usuario', 'contenedor_detalle', {id: id})

        $("#pestana_asignar").addClass('block-zone__tabs-button--active')
    }

    $().ready(() => {
        cargarAsignarUsuario(<?php echo $id; ?>)
    })
</script>