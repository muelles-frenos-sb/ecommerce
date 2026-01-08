<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de campañas</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div class="row mb-4">
            <div class="col-3">
                <a class="btn btn-success" href="<?php echo site_url('marketing/campanias/crear'); ?>">Crear campaña</a>
            </div>

            <div class="col-9 text-right">
                <button type="button" class="btn btn-success importar">Importar desde archivo plano</button>

                <a type="button" class="btn btn-info" href="<?php echo base_url().'archivos/plantillas/marketing_importacion_campanias_contactos.xlsx'; ?>" download>Descargar archivo plano</a>

                <input type="file" class="d-none" id="importar_archivo" onchange="javascript:importarCampanias()" accept=".xlsx,.xls,.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
            </div>
        </div>
    <div id="contenedor_campanias"></div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    importarCampanias = () => {
        Swal.fire({
            title: 'Estamos subiendo el archivo y importando las campañas en nuestros sistemas...',
            text: 'Por favor, espera.',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`,
            showConfirmButton: false,
            allowOutsideClick: false
        })

        let archivo = $('#importar_archivo').prop('files')[0]
        let documento = new FormData()
        documento.append("archivo", archivo)

        let subida = new XMLHttpRequest()
        subida.open('POST', `${$("#site_url").val()}marketing/importar_campanias`)
        subida.send(documento)
        subida.onload = evento => {
            let respuesta = JSON.parse(evento.target.responseText)

            Swal.close()

            if (respuesta.exito) {
                listarCampanias()
                mostrarAviso('exito', `¡${respuesta.mensaje}!`, 20000)
                return false
            } 

            mostrarAviso('error', `¡${respuesta.mensaje}!`, 20000)
        }
    }

    listarCampanias = () => {
        // Si no hay valor en la búsqueda, pero si en loca storage, lo pone
        if($("#buscar_campania").val() == "" && localStorage.simonBolivar_busquedaCampania) $("#buscar_campania").val(localStorage.simonBolivar_busquedaCampania)

        localStorage.simonBolivar_contador = 0

        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar_campania").val()
        }

        cargarInterfaz('marketing/lista', 'contenedor_campanias', datos)
    }

    $().ready(() => {
        listarCampanias()

        $("#buscar_campania").keyup(() => {
            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_busquedaCampania = $("#buscar_campania").val()

            listarCampanias()
        })

        $(".importar").click(() => $("#importar_archivo").trigger('click'))
    })
</script>