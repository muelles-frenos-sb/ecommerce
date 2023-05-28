<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Terceros</h1>
        </div>
    </div>
</div>
<div class="block">
    <div class="container container--max--xl">
        <div class="form-row">
            <div class="form-group col-md-12">
                <input type="text" class="form-control" id="buscar" placeholder="Buscar por alguna palabra clave">
            </div>
        </div>

        <div id="contenedor_terceros"></div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    listarTerceros = () => {
        // Si no hay valor en la búsqueda, pero si en loca storage, lo pone
        if($("#buscar").val() == "" && localStorage.simonBolivar_busquedaTercero) $("#buscar").val(localStorage.simonBolivar_busquedaTercero)

        localStorage.simonBolivar_contador = 0

        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar").val(),
        }

        cargarInterfaz('configuracion/terceros/lista', 'contenedor_terceros', datos)
    }

    $().ready(() => {
        listarTerceros()

        $("#buscar").keyup(() => {
            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_busquedaTercero = $("#buscar").val()

            // Recarga de la vista
            listarTerceros()
        })
    })
</script>