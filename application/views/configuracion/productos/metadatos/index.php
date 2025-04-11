<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Productos metadatos</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div class="mb-4">
            <a class="btn btn-success" href="<?php echo site_url('configuracion/productos/crear'); ?>">Crear</a>
        </div>

        <div id="contenedor_productos_metadatos"></div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    recortarTexto = (texto, textoLimite = 50) => {
        let textoRecortado = texto.slice(0, textoLimite)
        textoRecortado += (texto.length > textoLimite ? "..." : "")

        return textoRecortado
    }

    eliminarProductoMetaDatos = async (id) => {
        let confirmacion = await confirmar('Eliminar', `¿Está seguro de eliminar el metadato del producto?`)
        
        if (confirmacion) {
            let eliminar = await consulta('eliminar', {tipo: 'productos_metadatos', id: id})

            if (eliminar) {
                listarProductosMetaDatos()
            }
        }
    }

    listarProductosMetaDatos = () => {
        cargarInterfaz('configuracion/productos/metadatos/lista', 'contenedor_productos_metadatos')
    }

    $().ready(() => {
        listarProductosMetaDatos()
    })
</script>