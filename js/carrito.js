const agregarProducto = async datos => {
    let {id, precio, referencia, unidad_inventario} = datos

    obtenerPromesa(`${$('#site_url').val()}carrito/agregar`, {
        id: id,
        precio: precio,
        nombre: referencia,
        unidad_inventario: unidad_inventario,
    })
    .then(resultado => {
        mostrarNotificacion({
            tipo: 'carrito_nuevo_producto',
            id: id
        })

        actualizarCarrito(id)
        listarCarrito()
        agregarLog(53, id)
    })
}

/**
 * Actualiza el precio y cantidad de ítems del carrito
 * en el menú superior
 */
const actualizarCarrito = (productoId = null) => {
    obtenerPromesa(`${$('#site_url').val()}carrito/resumen`)
    .then(resultado => {
        $('#carrito_total').text(resultado.total)
        $('#carrito_total_items, #carrito_movil_total_items, #carrito_movil2_total_items').text(resultado.total_items)
        
        cargarInterfaz('core/menu_superior/carrito_detalle', 'contenedor_carrito_detalle')
    })

    if(productoId) {
        cargarBotones('principal', productoId)
        cargarBotones('producto_detalle', productoId)
    }
}

const cargarBotones = async(tipo, productoId) => {
    cargarInterfaz(`productos/botones/${tipo}`, `${tipo}_${productoId}`, {
        id: productoId,
    })
}

const listarCarrito = () => {
    cargarInterfaz('carrito/datos', 'contenedor_carrito_compras')
    cargarInterfaz('carrito/totales', 'contenedor_carrito_compras_totales')
}

const vaciarCarrito = async() => {
    obtenerPromesa(`${$('#site_url').val()}carrito/vaciar`)
    .then(resultado => {
        actualizarCarrito()
        listarCarrito()
    })

    agregarLog(54)
}

const eliminarProducto = async(rowId) => {
    obtenerPromesa(`${$('#site_url').val()}carrito/eliminar/${rowId}`)
    .then(resultado => {
        mostrarNotificacion({
            tipo: 'carrito_producto_eliminado',
        })

        actualizarCarrito()
        listarCarrito()
    })
}

const modificarItem = async(tipo, rowId, productoId) => {
    obtenerPromesa(`${$('#site_url').val()}carrito/modificar_item/${tipo}/${rowId}`)
    .then(resultado => {
        mostrarNotificacion({
            tipo: 'carrito_nuevo_producto',
            id: productoId,
        })
        actualizarCarrito(productoId)
        listarCarrito()
    })
}