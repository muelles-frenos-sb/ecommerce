<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Recibos</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div class="form-row">
            <div class="form-group col-md-12">
                <input type="text" class="form-control" id="buscar_recibo" placeholder="Buscar por alguna palabra clave" autofocus>
            </div>
        </div>

        <div id="contenedor_recibos"></div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    listarRecibos = () => {
        // Si no hay valor en la búsqueda, pero si en loca storage, lo pone
        if($("#buscar_recibo").val() == "" && localStorage.simonBolivar_busquedaFactura) $("#buscar_recibo").val(localStorage.simonBolivar_busquedaFactura)

        localStorage.simonBolivar_contador = 0

        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar").val(),
        }

        cargarInterfaz('configuracion/recibos/lista', 'contenedor_recibos', datos)
    }

    $().ready(() => {
        listarRecibos()

        $("#buscar_recibo").keyup(() => {
            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_busquedaFactura = $("#buscar_recibo").val()

            listarRecibos()
        })
    })
</script>