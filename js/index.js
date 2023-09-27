const agregarLog = async (tipoId, observacion = null) => {
    let datos = {
        tipo: 'logs',
        log_tipo_id: tipoId,
    }

    if(observacion) datos.observacion = observacion

    await consulta('crear', datos, false)
}

cargarFiltros = async(tipo, elemento, datos) => {
    switch(tipo) {
        case 'grupos':
            $(`#${elemento}`).html('')

            marca = await consulta('obtener', {
                tipo: 'marca',
                nombre: datos.marcaNombre
            })

            grupos = await consulta('obtener', {
                tipo: 'grupos',
                marca: datos.marcaNombre
            })

            $.each(grupos, function(key, grupo){
                $(`#${elemento}`).append(`
                    <li class='megamenu-links__item'>
                        <a class='megamenu-links__item-link' href='${$('#site_url').val()}productos?marca=${marca.nombre}&grupo=${grupo.nombre}'>${grupo.nombre}</a>
                    </li>
                `)
            })
        break;

        case 'lineas':
            $(`#${elemento}`).html('')

            marca = await consulta('obtener', {
                tipo: 'marca',
                nombre: datos.marcaNombre
            })

            lineas = await consulta('obtener', {
                tipo: 'lineas',
                marca: datos.marcaNombre
            })

            $.each(lineas, function(key, linea){
                $(`#${elemento}`).append(`
                    <li class='megamenu-links__item'>
                        <a class='megamenu-links__item-link' href='${$('#site_url').val()}productos?marca=${marca.nombre}&linea=${linea.nombre}'>${linea.nombre}</a>
                    </li>
                `)
            })
        break;
    }
}

cargarInterfaz = async(vista = 'index', contenedor = 'contenedor_principal', datos = null, tipo = null) => {
    // Se muestra la carga
    $('#cargando').show()

    $(`#${contenedor}`).html('')

    // Carga de la interfaz
    $(`#${contenedor}`).load(`${$("#site_url").val()}interfaces`, {tipo: tipo, vista: vista, datos: datos}, (respuesta, estado, xhr) => {
        // Si hay error
        if(estado == 'error') console.error(xhr)

        // Si fue exitoso, se oculta la carga
        if(estado == 'success') $("#cargando").hide()
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

    if($('#filtro_marca')) datos.marca = $('#filtro_marca').val()
    if($('#filtro_grupo')) datos.grupo = $('#filtro_grupo').val()
    if($('#filtro_linea')) datos.linea = $('#filtro_linea').val()

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

consulta = (tipo, datos, notificacion = true, mensaje = '') => {
    let respuesta = obtenerPromesa(`${$('#site_url').val()}interfaces/${tipo}`, datos)
        .then(resultado => {
            switch (tipo) {
                case "actualizar":
                    if (notificacion) mostrarAviso('exito', 'Se actualizaron los datos')
                    return resultado;
                break;

                case "crear":
                    if (notificacion) mostrarAviso('exito', 'Se almacenaron los datos')
                    return resultado;
                break;

                case "eliminar":
                    if (notificacion) mostrarAviso('exito', 'Se eliminaron los datos')
                    return resultado;
                break;

                case "obtener":
                    return resultado;
                break;

                default:
                    return resultado;
                break;
            }

        }).catch(error => console.error(error))

    return respuesta
}

/**
 * Obtiene las sucursales del API de Siesa y las almacena en la base de datos
 */
gestionarSucursales = async(numeroDocumento) => {
    // Mensaje mientras se consultan los datos
    $('#contenedor_mensaje_carga').html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Consultando las sucursales del cliente...`)

    // Se eliminan todas las sucursales del tercero
    await consulta('eliminar', {tipo: 'clientes_sucursales', f200_nit: numeroDocumento}, false)

    var paginas = 100
    var exito = true

    // Se recorren las páginas
    for (let pagina = 1; pagina <= paginas; pagina++) {
        // Se obtienen los registros en esa página
        await consulta('obtener', {tipo: 'clientes_sucursales', numero_documento: numeroDocumento, pagina: pagina}, false)
        .then(sucursales => {
            // Si se obtuvieron registros, se insertan en la base de datos
            if(sucursales.codigo == 0) consulta('crear', {tipo: 'clientes_sucursales', valores: sucursales.detalle.Table}, false)

            // Si no hay más registros, se cambia la variable
            if(sucursales.codigo == 1) exito = false
            
            return exito
        })
        
        // Se detiene el ciclo si no hay más registros
        if(!exito) break
    }
}

const iniciarSesion = async(evento, url = null) => {
    evento.preventDefault()

    let nombreUsuario = $('#usuario')
    let clave = $('#clave')

    let campos = [
        nombreUsuario,
        clave,
    ]

    // Validación de campos obligatorios
    if (!validarCamposObligatorios(campos)) {
        mostrarAviso('alerta', 'Hay campos obligatorios por diligenciar')
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
        mostrarAviso('alerta', 'El usuario y clave que ha digitado no existen en la base de datos. Por favor verifique nuevamente.')
        return false
    }

    // Si el usuario está desactivado
    if(usuario.estado == 0) {
        mostrarAviso('error', `El usuario ${nombreUsuario.val()} se encuentra desactivado.`)
        return false
    }

    // Se genera el inicio de sesión
    let sesion = await obtenerPromesa(`${$('#site_url').val()}sesion/iniciar`, {id: usuario.id})

    // Si tuvo éxito, se redirecciona
    if(sesion) {
        if(url) {
            location.href = url
        } else {
            location.href = `${$('#site_url').val()}inicio`
        }
    }
}

/**
 * Toma una cadena de texto y le elimina valores alfabéticos
 */
const limpiarCadena = valor => valor.replace(/[\a-z\&\/\\#,+()$~%.'":*?<>{}/ /_|¿?\-\°!=¡]/g, '')

const mostrarAviso = (tipo, mensaje, tiempo = 2000) => {
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

const mostrarNotificacion = (datos) => {
    if(datos.tipo == 'carrito_nuevo_producto') {
        datos.titulo = 'Nuevo producto agregado al carrito'
    }

    if(datos.tipo == 'carrito_producto_eliminado') {
        datos.titulo = 'Producto eliminado del carrito'
    }

    cargarInterfaz('core/notificacion', 'contenedor_notificacion', datos)

    setTimeout(() => $('.notification').addClass('ocultar'), 3000);
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
        mostrarAviso('alerta', 'Hay campos obligatorios por diligenciar')

        // No es exitoso
        exito = false
    }
    
    return exito
}