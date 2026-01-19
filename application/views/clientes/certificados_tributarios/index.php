<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de certificados tributarios</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div id="contenedor_certificados_tributarios"></div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    listarCertificadosTrubutarios = () => {
        // Si no hay valor en la búsqueda, pero si en loca storage, lo pone
        if($("#buscar_certificado_tributario").val() == "" && localStorage.simonBolivar_busquedaCertificadoTributario) $("#buscar_certificado_tributario").val(localStorage.simonBolivar_busquedaCertificadoTributario)

        localStorage.simonBolivar_contador = 0

        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar_certificado_tributario").val()
        }

        cargarInterfaz('clientes/certificados_tributarios/lista', 'contenedor_certificados_tributarios', datos)
    }

    $().ready(() => {
        listarCertificadosTrubutarios()

        $("#buscar_certificado_tributario").keyup(() => {
            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_busquedaCertificadoTributario = $("#buscar_certificado_tributario").val()

            listarCertificadosTrubutarios()
        })
    })
</script>