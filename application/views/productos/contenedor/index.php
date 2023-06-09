<div class="block-split__item block-split__item-content col-auto">
    <div class="block">
        <div class="products-view">
            <div id="contenedor_datos"></div>
            <div id="contenedor_paginacion"></div>
        </div>
    </div>
</div>

<?php
if(isset($_GET['pagina'])) echo "<input type='hidden' id='filtro_pagina' value='{$_GET['pagina']}'>";

if(isset($parametros['marca'])) echo "<input type='hidden' id='filtro_marca' value='{$parametros['marca']}'>";
if(isset($parametros['grupo'])) echo "<input type='hidden' id='filtro_grupo' value='{$parametros['grupo']}'>";
if(isset($parametros['linea'])) echo "<input type='hidden' id='filtro_linea' value='{$parametros['linea']}'>";
?>

<script>
    listarProductos = async() => {
        let productos = await obtenerPromesa(`${$("#site_url").val()}/productos/obtener`, {tipo: 'detalle'})
        let numeroPagina = parseInt($('#filtro_pagina').val())
        let itemsPorPagina = parseInt($('#view-option-limit').val())
        let cantidadProductos = productos.length

        const datosPaginacion = paginar(cantidadProductos, numeroPagina, itemsPorPagina)

        if($('#filtro_marca')) datosPaginacion.marca = $('#filtro_marca').val()
        if($('#filtro_grupo')) datosPaginacion.grupo = $('#filtro_grupo').val()
        if($('#filtro_linea')) datosPaginacion.linea = $('#filtro_linea').val()

        cargarInterfaz('productos/contenedor/datos', 'contenedor_datos', datosPaginacion)
        cargarInterfaz('productos/contenedor/paginacion', 'contenedor_paginacion', datosPaginacion)
    }

    $().ready(() => {
        listarProductos()

        $('#view-option-limit').change(() => listarProductos)
    })
</script>