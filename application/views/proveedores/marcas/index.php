<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Marcas proveedores</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container container--max--xl">
        <div class="mb-4">
            <a class="btn btn-success" href="<?php echo site_url('proveedores/marcas/crear'); ?>">Crear</a>
        </div>

        <div id="contenedor_marcas"></div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>

<script>
    eliminarMarcas = async (id) => {
        let confirmacion = await confirmar('Eliminar', `¿Está seguro de eliminar la marca?`)
        
        if (confirmacion) {
            let eliminar = await consulta('eliminar', {tipo: 'proveedores_marcas', id: id})

            if (eliminar) {
                listarMarcas()
            }
        }
    }

    listarMarcas = () => {
        cargarInterfaz('proveedores/marcas/lista', 'contenedor_marcas')
    }

    $().ready(() => {
        listarMarcas()
    })
</script>