<style>
    #tabla_productos_metadatos {
        font-size: 0.8em;
        font-family: Futura;
    }

    #tabla_productos_metadatos th {
        background-color: #19287F;
        color: white;
    }
</style>

<div class="table-responsive">
    <table class="table-striped table-bordered" id="tabla_productos_metadatos">
        <thead>
            <tr>
                <th class="text-center">Producto</th>
                <th class="text-center">Palabras clave</th>
                <th class="text-center">Slug</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    $().ready(() => {
        $("#tabla_productos_metadatos").DataTable({
            ajax: {
                url: `${$("#site_url").val()}configuracion/obtener_datos_tabla`,
                data: {
                    tipo: "productos_metadatos",
                },
            },
            columns: [
                {
                    data: 'notas',
                    render: (data, type, row) => {
                        let notas = recortarTexto(data)

                        return `
                            <div class="copiar" data-valor="${data}" title="${data}">${notas}</div>
                        `
                    }
                },
                { data: 'palabras_clave' },
                { data: 'slug', 
                    render: (data, type, row) => {
                        let texto = recortarTexto(`${$('#site_url').val()}${data}`, 40)
                        let url = `${$('#site_url').val()}${data}`

                        return `
                            <a href="${url}" class="copiar" data-valor="${url}" title="${url}">${texto}</a>
                        `
                    }
                },
                {
                    data: null, 
                    render: (data, type, row) => {
                        return null
                    }
                }
            ],
            serverSide: true,
            stateSave: true,
            scrollY: '320px',
            searching: true,
            language: {
                url: '<?php echo base_url(); ?>js/dataTables_espanol.json'
            },
            scrollX: false,
            scrollCollapse: true,
            drawCallback: function() {
                $('.copiar').on('copy', function(evento) {
                    let textoIncompleto = window.getSelection().toString()
                    let elemento = $(this)

                    if (textoIncompleto) {
                        let textoCompleto = elemento.data('valor')

                        evento.preventDefault()
                        evento.originalEvent.clipboardData.setData('text/plain', textoCompleto)
                    }
                })
            }
        })

        $('#contenedor_mensaje_carga').html('')
    })
</script>