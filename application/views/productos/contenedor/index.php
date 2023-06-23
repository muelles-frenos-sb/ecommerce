<div class="block-split__item block-split__item-content col-auto">
    <div class="block">
        <div class="products-view">
            <?php  $this->load->view('productos/contenedor/filtros'); ?>
            <div id="contenedor_productos"></div>
            <div id="contenedor_paginacion"></div>
            <br>

            <button type="button" class="btn btn-primary btn-lg btn-block" onClick="javascript:cargarMasDatos('productos/contenedor')" id="btn_mostrar_mas">Mostrar más resultados</button>
        </div>
    </div>
</div>

<?php
if(isset($_GET['pagina'])) echo "<input type='hidden' id='filtro_pagina' value='{$_GET['pagina']}'>";

if(isset($parametros['marca'])) echo "<input type='hidden' id='filtro_marca' value='{$parametros['marca']}'>";
if(isset($parametros['grupo'])) echo "<input type='hidden' id='filtro_grupo' value='{$parametros['grupo']}'>";
if(isset($parametros['linea'])) echo "<input type='hidden' id='filtro_linea' value='{$parametros['linea']}'>";
if(isset($parametros['busqueda'])) echo "<input type='hidden' id='filtro_busqueda' value='{$parametros['busqueda']}'>";
?>

<script>
    listarProductos = async() => {
        localStorage.simonBolivar_contador = 0


        // let productos = await obtenerPromesa(`${$("#site_url").val()}/productos/obtener`, {tipo: 'detalle'})
        // let numeroPagina = parseInt($('#filtro_pagina').val())
        // let itemsPorPagina = parseInt($('#view-option-limit').val())
        // let cantidadProductos = productos.length

        if(localStorage.simonBolivar_buscarProducto) $('#buscar').val(localStorage.simonBolivar_buscarProducto)

        // const datosPaginacion = paginar(cantidadProductos, numeroPagina, itemsPorPagina)
        
        let datos = {
            contador: localStorage.simonBolivar_contador,
            busqueda: $("#buscar").val(),
        }

        if($('#filtro_marca')) datos.marca = $('#filtro_marca').val()
        if($('#filtro_grupo')) datos.grupo = $('#filtro_grupo').val()
        if($('#filtro_linea')) datos.linea = $('#filtro_linea').val()
        if($('#filtro_busqueda')) datos.busqueda = $('#filtro_busqueda').val()

        cargarInterfaz('productos/contenedor/lista', 'contenedor_productos', datos)
        // cargarInterfaz('productos/contenedor/paginacion', 'contenedor_paginacion', datosPaginacion)
    }

    $().ready(() => {
        listarProductos()

        $('#view-option-limit').change(() => listarProductos)
    })
</script>