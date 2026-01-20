<style>
    #tabla_campanias tbody td {
        font-size: 0.9em;
        padding: 8px;
    }
</style>

<table class="table-striped table-bordered" id="tabla_campanias"></table>

<input type="file" class="d-none" id="importar_archivo" onchange="importarCampanias()" accept=".xlsx,.xls,.csv">

<div class="modal fade" id="modal_prueba_whatsapp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fa fa-whatsapp"></i> Enviar prueba de WhatsApp
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_prueba_whatsapp" onsubmit="event.preventDefault(); ejecutarEnvioPrueba();">
                    <input type="hidden" id="id_campania_prueba">
                    <div class="form-group">
                        <label for="telefono_prueba" class="font-weight-bold">Número de teléfono destino:</label>
                        <input type="text" class="form-control form-control-lg" id="telefono_prueba" placeholder="Ej: 584121234567" required>
                        <small class="text-muted">
                            <i class="fa fa-info-circle"></i> Ingresa el número con el código de país (sin el +).
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-info" onclick="ejecutarEnvioPrueba()">
                    <i class="fa fa-paper-plane"></i> Enviar Prueba
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let campania_id = null

    // --- LÓGICA EXISTENTE DE IMPORTACIÓN ---
    const seleccionarCampania = (id) => {
        campania_id = id
        $("#importar_archivo").val(null).trigger('click')
    }

    importarCampanias = () => {
        Swal.fire({
            title: 'Estamos subiendo el archivo e importando las campañas...',
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

    // --- NUEVA LÓGICA: ENVÍO DE PRUEBA WHATSAPP ---
    const abrirModalPrueba = (id) => {
        $('#id_campania_prueba').val(id)
        $('#telefono_prueba').val('') // Limpiar el input
        $('#modal_prueba_whatsapp').modal('show')
        
        // Dar foco al input automáticamente
        setTimeout(() => { $('#telefono_prueba').focus() }, 500)
    }

    const ejecutarEnvioPrueba = () => {
        let id = $('#id_campania_prueba').val()
        let telefono = $('#telefono_prueba').val()

        if(!telefono) {
            mostrarAviso('alerta', 'Debes ingresar un número de teléfono.')
            return false
        }

        $('#modal_prueba_whatsapp').modal('hide')

        Swal.fire({
            title: 'Enviando mensaje...',
            text: 'Conectando con la API de WhatsApp',
            imageUrl: `${$('#base_url').val()}images/cargando.webp`, // Usamos tu imagen de carga
            showConfirmButton: false,
            allowOutsideClick: false
        })

        $.ajax({
            url: `${$("#site_url").val()}marketing/enviar_prueba_whatsapp`,
            method: 'POST',
            data: { 
                campania_id: id, 
                telefono: telefono 
            },
            dataType: 'json',
            success: function(respuesta) {
                Swal.close()
                if(respuesta.exito) {
                    mostrarAviso('exito', 'Mensaje de prueba enviado correctamente.')
                } else {
                    mostrarAviso('error', respuesta.mensaje || 'Error al enviar el mensaje.')
                }
            },
            error: function() {
                Swal.close()
                mostrarAviso('error', 'Error de conexión con el servidor.')
            }
        })
    }

    // --- INICIALIZACIÓN ---
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
                            <div class="p-1" style="width: 150px;"> <a class="btn btn-sm btn-primary"
                                   href="${$("#site_url").val()}marketing/campanias/editar/${data.id}" 
                                   title="Editar campaña">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                <button class="btn btn-sm btn-success"
                                        title="Importar contactos"
                                        onclick="seleccionarCampania(${data.id})">
                                    <i class="fa fa-upload"></i>
                                </button>

                                <button class="btn btn-sm btn-secondary"
                                        title="Enviar mensaje de prueba"
                                        onclick="abrirModalPrueba(${data.id})">
                                    <i class="fa fa-paper-plane"></i>
                                </button>

                            </div>
                        `
                    }
                }
            ],
            columnDefs: [
                { targets: '_all', className: 'dt-head-center p-1' }
            ],
            deferRender: true,
            fixedHeader: true,
            info: true,
            initComplete: function () {
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