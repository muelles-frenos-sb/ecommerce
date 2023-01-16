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
                        <a class='megamenu-links__item-link' href='#'>${grupo.nombre}</a>
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
                        <a class='megamenu-links__item-link' href='#'>${linea.nombre}</a>
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

consulta = (tipo, datos, notificacion = true) => {
    let respuesta = obtenerPromesa(`${$('#site_url').val()}/${tipo}`, datos)
        .then(resultado => {
            switch (tipo) {
                default:
                    return resultado
                break;
            }
        }).catch(error => console.error(error))

    return respuesta
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