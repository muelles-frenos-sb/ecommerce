<div class="block-split__item block-split__item-content col-auto">
    <div class="block">
        <div class="products-view">
            <?php $this->load->view('productos/contenedor/filtros'); ?>
            <?php // $this->load->view('productos/contenedor/datos'); ?>
            <div id="contenedor_datos"></div>
            <div id="contenedor_paginacion"></div>
        </div>
    </div>
</div>

<?php
if(isset($_GET['pagina'])) echo "<input type='hidden' id='filtro_pagina' value='{$_GET['pagina']}'>";
?>

<script>
    listarProductos = async() => {
        let productos = await obtenerPromesa(`${$("#site_url").val()}/productos/obtener`, {tipo: 'detalle'})
        let numeroPagina = parseInt($('#filtro_pagina').val())
        let itemsPorPagina = parseInt($('#view-option-limit').val())
        let cantidadProductos = productos.length

        const datosPaginacion = paginar(cantidadProductos, numeroPagina, itemsPorPagina)

        cargarInterfaz('productos/cargar_vista', 'contenedor_datos', datosPaginacion)
        cargarInterfaz('productos/paginar', 'contenedor_paginacion', datosPaginacion)
    }

    $().ready(() => {
        listarProductos()

        $('#view-option-limit').change(() => listarProductos)
    })
</script>