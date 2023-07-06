const actualizarCarrito = () => {
    obtenerPromesa(`${$('#site_url').val()}carrito/resumen`)
    .then(resultado => {
        $('#carrito_total').text(resultado.total)
        $('#carrito_total_items').text(resultado.total_items)
        
        cargarInterfaz('core/menu_superior/carrito_detalle', 'contenedor_carrito_detalle')
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
}

const agregarProducto = async(id, precio, nombre) => {
    obtenerPromesa(`${$('#site_url').val()}carrito/agregar/${id}/${precio}/${nombre}`)
    .then(resultado => {
        mostrarNotificacion({
            tipo: 'carrito_nuevo_producto',
            id: id
        })
        actualizarCarrito()
        listarCarrito()
    })
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

const modificarItem = async(tipo, rowId) => {
    obtenerPromesa(`${$('#site_url').val()}carrito/modificar_item/${tipo}/${rowId}`)
    .then(resultado => {
        mostrarNotificacion({
            tipo: 'carrito_nuevo_producto',
        })
        actualizarCarrito()
        listarCarrito()
    })
}