<?php
// Se obtiene un arreglo con todos los productos destacados que se van a mostrar al inicio
$productos = $this->productos_model->obtener('productos_destacados');
$productos_outlet = $this->productos_model->obtener('productos_outlet');

/****************************************************************************************
 **************************************** Slider ****************************************
 ****************************************************************************************/
$this->load->view('inicio/slider');
echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 ******************** Información de envíos, pago seguro y garantía *********************
 ****************************************************************************************/
$this->load->view('inicio/caracteristicas');
echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 ******************************** Buscador de repuestos *********************************
 ****************************************************************************************/
$this->load->view('inicio/buscar_repuestos');
echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 ******************************** Productos destacados **********************************
 ****************************************************************************************/
$this->data['desde'] = 1;   // Posición del arreglo donde comenzará
$this->data['hasta'] = 10;  // Posición del arreglo donde terminará
$this->data['productos'] = $productos;
$this->load->view('inicio/productos_destacados', $this->data);
echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 *************************************** Outlet *****************************************
 ****************************************************************************************/
$this->data['productos'] = $productos_outlet;
$this->load->view('inicio/outlet', $this->data);
echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 *********************** Bloque de marcas y productos destacados ************************
 ****************************************************************************************/
echo "
<div class='block block-zone'>
    <div class='container'>
        <div class='block-zone__body'>";
            $this->data['desde'] = 11; // Posición del arreglo donde comenzará
            $this->data['hasta'] = 20; // Posición del arreglo donde terminará
            $this->data['productos'] = $productos;
            $this->data['tipo'] = 'marca';
            $this->load->view('inicio/bloques/marcas');
            $this->load->view('inicio/bloques/productos_destacados', $this->data);
        echo "
        </div>
    </div>
</div>";
echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 ********************************** Productos nuevos ************************************
 ****************************************************************************************/
$this->data['desde'] = 21; // Posición del arreglo donde comenzará
$this->data['hasta'] = 30; // Posición del arreglo donde terminará
$this->data['productos'] = $productos;
$this->load->view('inicio/nuevos_productos', $this->data);
echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 ************************ Bloque de grupos y productos destacados ***********************
 ****************************************************************************************/
echo "
<div class='block block-zone'>
    <div class='container'>
        <div class='block-zone__body'>";
            $this->data['desde'] = 31; // Posición del arreglo donde comenzará
            $this->data['hasta'] = 40; // Posición del arreglo donde terminará
            $this->data['productos'] = $productos;
            $this->data['tipo'] = 'grupo';
            $this->load->view('inicio/bloques/grupos');
            $this->load->view('inicio/bloques/productos_destacados', $this->data);
            echo "
        </div>
    </div>
</div>";
echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 **************************************** Marcas ****************************************
 ****************************************************************************************/
$this->load->view('inicio/marcas');
echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 *********************** Bloque de líneas y productos destacados ************************
 ****************************************************************************************/
echo "
<div class='block block-zone'>
    <div class='container'>
        <div class='block-zone__body'>";
            $this->data['desde'] = 41;
            $this->data['hasta'] = 50;
            $this->data['productos'] = $productos;
            $this->data['tipo'] = 'linea';
            $this->load->view('inicio/bloques/lineas');
            $this->load->view('inicio/bloques/productos_destacados', $this->data);
        echo "
        </div>
    </div>
</div>";
echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 *************************** Banners de outlet y promociones ****************************
 ****************************************************************************************/
$this->load->view('inicio/bloques_banners');
echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 *********************************** Últimas noticias ***********************************
 ****************************************************************************************/
// $this->load->view('inicio/ultimas_noticias');
// echo "<div class='block-space block-space--layout--divider-nl'></div>";

/****************************************************************************************
 ********************************* Resumen de productos *********************************
 ****************************************************************************************/
echo 
"<div class='block block-products-columns'>
    <div class='container'>
        <div class='row'>";
            // Columna 1
            $this->data['desde'] = 51;
            $this->data['hasta'] = 54;
            $this->data['productos'] = $productos;
            $this->data['titulo'] = 'Para tí';
            $this->load->view('inicio/footer_productos_destacados', $this->data);

            // Columna 2
            $this->data['desde'] = 55;
            $this->data['hasta'] = 58;
            $this->data['productos'] = $productos;
            $this->data['titulo'] = 'Destacados';
            $this->load->view('inicio/footer_productos_destacados', $this->data);
            
            // Columna 3
            $this->data['desde'] = 59;
            $this->data['hasta'] = 62;
            $this->data['productos'] = $productos;
            $this->data['titulo'] = 'Ofertas';
            $this->load->view('inicio/footer_productos_destacados', $this->data);
        echo "
        </div>
    </div>
</div>";
echo "<div class='block-space block-space--layout--divider-nl'></div>";
?>