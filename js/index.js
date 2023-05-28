cargarFiltros = async(tipo, elemento, datos) => {
    switch(tipo) {
        case 'grupos':
            $(`#${elemento}`).html('')

            grupos = await consulta('configuracion/obtener', {
                tipo: 'grupos',
                marca_id: datos.marca_id
            })

            $.each(grupos, function(key, grupo){
                $(`#${elemento}`).append(`
                    <li class='megamenu-links__item'>
                        <a class='megamenu-links__item-link' href='productos?grupo=${grupo.nombre}'>${grupo.nombre}</a>
                    </li>
                `)
            })
        break

        case 'lineas':
            $(`#${elemento}`).html('')

            lineas = await consulta('configuracion/obtener', {
                tipo: 'lineas',
                marca_id: datos.marca_id
            })

            $.each(lineas, function(key, linea){
                $(`#${elemento}`).append(`
                    <li class='megamenu-links__item'>
                        <a class='megamenu-links__item-link' href='productos?grupo=${linea.nombre}'>${linea.nombre}</a>
                    </li>
                `)
            })
        break
    }
}

cargarInterfaz = async(url = '', contenedor = '', datos = null, tipo = null) => {
    // // Se muestra la carga
    // $('#cargando').show()

    $(`#${contenedor}`).html('')

    // Carga de la interfaz
    $(`#${contenedor}`).load(`${$("#site_url").val()}${url}`, {tipo: tipo, datos: datos}, (respuesta, estado, xhr) => {
        // Si hay error
        if(estado == 'error') console.error(xhr)

        // // Si fue exitoso, se oculta la carga
        // if(estado == 'success') $("#cargando").hide()
    })
}

cargarMasDatos = tipo => {
    // Se aumenta el contador
    localStorage.simonBolivar_contador = (localStorage.simonBolivar_contador)
    ? parseInt(localStorage.simonBolivar_contador) + parseInt($('#cantidad_datos').val())
    : 0

    var datos = {
        tipo: tipo,
        contador: parseInt(localStorage.simonBolivar_contador),
        busqueda: $("#buscar").val(),
    }

    $.ajax({
        url: `${$('#site_url').val()}interfaces/cargar_mas_datos`,
        data: {datos: datos},
        type: 'POST',
        // beforeSend: () => $('#cargando').show()
    })
    .done(data => {
        $("#datos").append(data)

        // $('#cargando').hide()
    })
    .fail((jqXHR, ajaxOptions, thrownError) => console.error('El servidor no responde.'))
}

consulta = (tipo, datos, notificacion = true) => {
    let respuesta = obtenerPromesa(`${$('#site_url').val()}interfaces/${tipo}`, datos)
        .then(resultado => {
            switch (tipo) {
                case "actualizar":
                    if (notificacion) mostrarNotificacion('exito', 'Se actualizaron los datos')
                    return resultado
                break;

                case "crear":
                    if (notificacion) mostrarNotificacion('exito', 'Se almacenaron los datos')
                    return resultado
                break;

                case "eliminar":
                    if (notificacion) mostrarNotificacion('exito', 'Se eliminaron los datos')
                    return resultado
                break;

                case "obtener":
                    return resultado
                break;

                default:
                    return resultado
                break;
            }

        }).catch(error => console.error(error))

    return respuesta
}

const iniciarSesion = async(evento) => {
    evento.preventDefault()

    let nombreUsuario = $('#usuario')
    let clave = $('#clave')

    let campos = [
        nombreUsuario,
        clave,
    ]

    // Validación de campos obligatorios
    if (!validarCamposObligatorios(campos)) {
        mostrarNotificacion('alerta', 'Hay campos obligatorios por diligenciar')
        return false
    }

    let datos = {
        tipo: 'usuario',
        login: $.trim(nombreUsuario.val()),
        clave: $.trim(clave.val()),
    }

    let usuario = await obtenerPromesa(`${$('#site_url').val()}sesion/obtener_datos`, datos)
    
    // Si no se encontró el usuario
    if(!usuario) {
        mostrarNotificacion('alerta', 'El usuario y clave que ha digitado no existen en la base de datos. Por favor verifique nuevamente.')
        return false
    }

    // Si el usuario está desactivado
    if(usuario.estado == 0) {
        mostrarNotificacion('error', `El usuario ${nombreUsuario.val()} se encuentra desactivado.`)
        return false
    }

    // Se genera el inicio de sesión
    let sesion = await obtenerPromesa(`${$('#site_url').val()}sesion/iniciar`, {id: usuario.id})

    // Si tuvo éxito, se redirecciona
    if(sesion) location.href = `${$('#site_url').val()}inicio`
}

mostrarNotificacion = (tipo, mensaje, tiempo = 2000) => {
    switch (tipo) {
        case 'exito':
            titulo = 'Éxito'
            icono = 'success'
        break;

        case 'error':
            titulo = 'Error'
            icono = 'error'
        break;

        case 'alerta':
            titulo = 'Alerta'
            icono = 'warning'
        break;

        case 'info':
            titulo = 'Información'
            icono = 'info'
        break;

        case 'pregunta':
            titulo = 'Pregunta'
            icono = 'question'
        break;
    }

    Swal.fire({
        confirmButtonText: 'Aceptar',
        icon: icono,
        // position: 'top-end',
        html: mensaje,
        timer: tiempo,
        title: titulo,
    })
}

const obtenerPromesa = (url, opciones) => {
    return new Promise((resolve, reject) => {
        // Datos a enviar                
        var datos = new FormData()
        datos.append('datos', JSON.stringify(opciones))

        // Creación de solicitud http
        const xhttp = new XMLHttpRequest()
        xhttp.open(`POST`, url, true)

        // Cuando cambie el estado
        xhttp.onreadystatechange = (() => {
            if(xhttp.readyState === 4) {
                // Si el estado es exitoso
                (xhttp.status === 200)
                    ? resolve(JSON.parse(xhttp.responseText)) // Envía el string del peso
                    : reject(new Error('Error', url)) // Envía el error
            }
        })

        // Se envía la solicitud
        xhttp.send(datos)
    }) 
}

const paginar = (cantidadItems, numeroPagina, itemsPorPagina) => {
    // Si no se define el número de la página, inicia en 1
    if(isNaN(numeroPagina)) numeroPagina = 1

    // Se calcula cuántas páginas creará
    let cantidadPaginas = Math.ceil(cantidadItems / itemsPorPagina)
    
    // Si la página que se solicita es mayor a la que pueda existir, se trae la última
    if(numeroPagina > cantidadPaginas) numeroPagina = cantidadPaginas

    numeroPagina -= 1
    let desde = numeroPagina * itemsPorPagina

    // Lógica para la página siguiente
    // Al llegar a la última página, volverá a la primera;
    // Sino, pasará a la siguiente
    let paginaSiguiente = (numeroPagina >= cantidadPaginas - 1) ? 1 : numeroPagina + 2 ;

    // Lógica para la página anterior
    // Al llegar a la primera página, volverá a la primera;
    // Sino, pasará a la anterior
    let paginaAnterior = (numeroPagina < 1) ? cantidadPaginas : numeroPagina ;

    let respuesta = {
        cantidad_items: cantidadItems,
        cantidad_paginas: cantidadPaginas,
        desde: desde,
        items_por_pagina: itemsPorPagina,
        pagina_actual: (numeroPagina + 1),
        pagina_siguiente: paginaSiguiente,
        pagina_anterior: paginaAnterior
    }

    return respuesta
}

const validarCamposObligatorios = campos => {
    let validacionesExitosas = campos.length
    let exito = true

    //Recorrido para validar cada campo
    for (var i = 0; i < campos.length; i++){
        // Se remueve la validación a todos los campos
        $(`.invalid-feedback`).remove()
        campos[i].removeClass(`is-invalid`)

        // Si el campo está vacío
        if($.trim(campos[i].val()) == "") {
            // Se resta el campo al total de validaciones exitosas
            validacionesExitosas--

            // Se marcan los campos en rojo con un mensaje
            campos[i].addClass(`is-invalid`)
            // campos[i].after(`<div class="invalid-feedback">Este campo no puede estar vacío</div>`)
        }
    }

    // Si los exitosos son todos
    if(validacionesExitosas != campos.length) {
        mostrarNotificacion('alerta', 'Hay campos obligatorios por diligenciar')

        // No es exitoso
        exito = false
    }
    
    return exito
}