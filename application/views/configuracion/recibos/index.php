<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de recibos</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xxl">
        <div class="block-zone__widget-header">
            <div class="block-zone__tabs">
                <?php foreach ($this->configuracion_model->obtener('recibos_tipos', ['activo' => 1]) as $recibo_tipo) { ?>
                    <button type="button" class="block-zone__tabs-button" id="pestana_recibo_tipo_<?php echo $recibo_tipo->id; ?>">
                        <a href="<?php echo site_url("configuracion/recibos/ver/$recibo_tipo->id"); ?>">
                            <?php echo $recibo_tipo->nombre; ?>
                        </a>
                    </button>
                <?php } ?>
            </div>
        </div>
        
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
        if($("#buscar_recibo").val() == "" && localStorage.simonBolivar_busquedaRecibo) $("#buscar_recibo").val(localStorage.simonBolivar_busquedaRecibo)

        localStorage.simonBolivar_contador = 0

        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar_recibo").val(),
            id_tipo_recibo: '<?php echo $id_tipo_recibo; ?>'
        }

        cargarInterfaz('configuracion/recibos/lista', 'contenedor_recibos', datos)
    }

    $().ready(() => {
        listarRecibos()

        $(`#pestana_recibo_tipo_<?php echo $id_tipo_recibo; ?>`).addClass('block-zone__tabs-button--active')

        $("#buscar_recibo").keyup(() => {
            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_busquedaRecibo = $("#buscar_recibo").val()

            listarRecibos()
        })
    })
</script>