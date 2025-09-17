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
            <button type="button" class="block-zone__tabs-button botones-tabs" id="pestana_formulario" onClick="javascript:cargarFormulario(<?php echo $id; ?>)">
                Formulario
            </button>
            <button type="button" class="block-zone__tabs-button botones-tabs" id="pestana_archivos" onClick="javascript:cargarArchivos(<?php echo $id; ?>)">
                Archivos
            </button>
            <button type="button" class="block-zone__tabs-button botones-tabs" id="pestana_opciones" onClick="javascript:cargarOpciones(<?php echo $id; ?>)">
                Opciones
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

    cargarOpciones = (id) => {
        $(".botones-tabs").removeClass('block-zone__tabs-button--active')

        cargarInterfaz('clientes/solicitud_credito/opciones', 'contenedor_detalle', {id: id})

        $("#pestana_opciones").addClass('block-zone__tabs-button--active')
    }

    $().ready(() => {
        cargarFormulario(<?php echo $id; ?>)
    })
</script>