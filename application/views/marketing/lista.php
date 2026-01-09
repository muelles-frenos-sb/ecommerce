<style>
    #tabla_campanias tbody td {
        font-size: 0.9em;
        padding: 8px;
    }
</style>

<!-- Inicialización de la tabla -->
<table class="table-striped table-bordered" id="tabla_campanias"></table>

<!-- Input file oculto -->
<input type="file" class="d-none" id="importar_archivo" onchange="importarCampanias()" accept=".xlsx,.xls,.csv">

<script>
    // Se usa el mismo patrón del index
    let campania_id = null

    const seleccionarCampania = (id) => {
        campania_id = id
        $("#importar_archivo").val(null).trigger('click')
    }

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
        documento.append("campania_id", campania_id)

        let subida = new XMLHttpRequest()
        subida.open('POST', `${$("#site_url").val()}marketing/importar_campanias`)
        subida.send(documento)

        subida.onload = evento => {
            let respuesta = JSON.parse(evento.target.responseText)
            Swal.close()

            if (respuesta.exito) {
                tablaCampanias.ajax.reload(null, false)
                mostrarAviso('exito', `¡${respuesta.mensaje}!`, 20000)
                return false
            }

            mostrarAviso('error', `¡${respuesta.mensaje}!`, 20000)
        }
    }

    $().ready(async () => {
        tablaCampanias = $("#tabla_campanias").DataTable({
            ajax: {
                url: `${$("#site_url").val()}marketing/obtener_datos_tabla`,
                data: datos => {
                    datos.tipo = 'campanias'

                    datos.filtros_personalizados = {
                        id: $('#filtro_id').val(),
                        fecha_inicio: $('#filtro_fecha_inicio').val(),
                        fecha_finalizacion: $('#filtro_fecha_finalizacion').val(),
                        cantidad_contactos: $('#filtro_cantidad_contactos').val(),
                        cantidad_envios: $('#filtro_cantidad_envios').val(),
                    }
                }
            },
            columns: [
                { 
                    title: `
                        Id
                        <input type="number" id="filtro_id" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'id',
                    className: 'dt-body-right'
                },
                { 
                    title: `
                        Fecha de inicio
                        <input type="date" id="filtro_fecha_inicio" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'fecha_inicio'
                },
                { 
                    title: `
                        Fecha de finalización
                        <input type="date" id="filtro_fecha_finalizacion" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'fecha_finalizacion'
                },
                { 
                    title: `
                        Cantidad de contactos
                        <input type="number" id="filtro_cantidad_contactos" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'cantidad_contactos',
                    className: 'dt-body-right'
                },
                { 
                    title: `
                        Cantidad de envíos
                        <input type="number" id="filtro_cantidad_envios" class="form-control form-control-sm border-secondary">
                    `,
                    data: 'cantidad_envios',
                    className: 'dt-body-right'
                },
                {
                    title: 'Acciones',
                    data: null, 
                    render: (data, type, row) => {
                        return `
                            <div class="p-1" style="width: 100px;">
                                <a class="btn btn-sm btn-primary"
                                href="${$("#site_url").val()}marketing/campanias/editar/${data.id}">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                <button class="btn btn-sm btn-success"
                                        title="Importar contactos"
                                        onclick="seleccionarCampania(${data.id})">
                                    <i class="fa fa-upload"></i>
                                </button>
                            </div>
                        `
                    }
                }
            ],
            columnDefs: [
                { targets: '_all', className: 'dt-head-center p-1' } // Todo el encabezado alineado al centro
            ],
            deferRender: true,
            fixedHeader: true,
            info: true,
            initComplete: function () {
                // Cuando un campo de filtro personalizado cambie, se redibuja la tabla
                $(`input[id^='filtro_'], select[id^='filtro_']`).on('keyup change', () => tablaCampanias.draw())
            },
            language: {
                decimal: ',',
                thousands: '.',
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            ordering: false,
            orderCellsTop: true,
            pageLength: 100,
            paging: true,
            processing: true,
            scrollCollapse: true,
            scroller: true,
            scrollX: false,
            scrollY: false,
            searching: true,
            serverSide: true,
            stateSave: false,
        })

        $(".importar").click(() => $("#importar_archivo").trigger('click'))
    })
</script>