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
        console.log(resultado)
        actualizarCarrito()
    })

    // let producto = await consulta('obtener', {tipo: 'producto_existente', NB_IDN: id})
    
    // // Si intenta pedir un producto inactivo
    // if(producto.NB_ESTADO == '0') {
    //     agregarLog(8, `Producto ${id}`)
    //     mostrarNotificacion('alerta', 'No se puede agregar un producto inactivo')
    //     return false
    // }

    // if(variosProductos) {
    //     if (!validarCamposObligatorios([$('#cantidad_producto')])) return false

    //     // Primero, se eliminan los productos asociados al pedido
    //     await consulta('eliminar', {tipo: 'pedidos_detalle', producto_id: id, pedido_id: sessionStorage.pedidoId}, false)

    //     let items = []

    //     // Ahora, recorremos cada ítem
    //     for (let index = 0; index < parseInt($('#cantidad_producto').val()); index++) {
    //         items.push({
    //             pedido_id: sessionStorage.pedidoId,
    //             producto_id: id,
    //             precio: precio,
    //             precio_sin_iva: precioSinIva
    //         })
    //     }

    //     // Se agrega los ítems al pedido
    //     await promesa(`${$("#site_url").val()}interfaces/crear_productos_items`, items)

    //     mostrarPopup(`${$('#cantidad_producto').val()} ítems agregados`, 'exito')
    //     actualizarCantidad(id)
    //     cerrarModal()
    // } else {
    //     // Si no estamos en el cotizador y no hay un área seleccionada
    //     if($('#modulo_carrito').val() != 'cotizador' && !$('#sede_menu').val()) {
    //         mostrarNotificacion('alerta', 'Seleccione primero la sede a la cuál asociará el pedido')
    //         return false
    //     }

    //     // Si no hay un pedido abierto
    //     if(!sessionStorage.pedidoId) {
    //         let sedeId = ($("#modulo_carrito").val() != 'cotizador') ? $('#sede_menu').val() : 146

    //         let datosPedido = {
    //             tipo: 'pedido',
    //             sede_id: sedeId
    //         }

    //         // Si es el cotizador
    //         if(($("#modulo_carrito").val() == 'cotizador')) {
    //             let datosCliente = JSON.parse(localStorage.alvarez_soluciones_cotizacion)

    //             // Se agregan los datos del cliente que cotiza
    //             datosPedido.cliente_email = datosCliente.email_cliente
    //             datosPedido.cliente_nombre = datosCliente.nombre_cliente
    //             datosPedido.cliente_telefono = datosCliente.telefono_cliente
    //             datosPedido.cliente_tipo = datosCliente.tipo_cliente
    //             datosPedido.cliente_documento = datosCliente.documento_cliente
                
    //             // se agrega un valor para diferenciar los pedidos
    //             datosPedido.cotizacion = 1
    //         }

    //         // Se crea un pedido
    //         let pedido = await promesa(`${$("#site_url").val()}interfaces/crear`, datosPedido)

    //         if(pedido.resultado) {
    //             pedidoId = pedido.resultado

    //             // Si es el cotizador
    //             if($('#modulo_carrito').val() == 'cotizador') {
    //                 // Se agrega el id del pedido a la sesión
    //                 sessionStorage.pedidoId = pedido.resultado
    //             } else {
    //                 // Se valida un pedido abierto
    //                 validarPedidoAbierto()
    //             }
    //         }
    //     } else {
    //         pedidoId = sessionStorage.pedidoId
    //     }

    //     // Se agrega el ítem al pedido
    //     promesa(`${$("#site_url").val()}interfaces/crear`, {tipo: 'pedido_item', pedido_id: pedidoId, producto_id: id, precio: precio, precio_sin_iva: precioSinIva})
    //     .then(item => {
    //         mostrarPopup('Producto agregado', 'exito')
    //         actualizarCantidad(id)
    //     })
    // }

    // // Se oculta o muestra el carrito
    // if($('#modulo_carrito').val() == 'cotizador') $('#carrito_cotizador, #cancelar_cotizacion').attr('hidden', (sessionStorage.pedidoId) ? false : true)
}

const eliminarProducto = async(rowId) => {
    obtenerPromesa(`${$('#site_url').val()}carrito/eliminar/${rowId}`)
    .then(resultado => {
        console.log(resultado)
        actualizarCarrito()
        listarCarrito()
    })
}

const modificarItem = async(tipo, rowId) => {
    obtenerPromesa(`${$('#site_url').val()}carrito/modificar_item/${tipo}/${rowId}`)
    .then(resultado => {
        console.log(resultado)
        actualizarCarrito()
        listarCarrito()
    })
}