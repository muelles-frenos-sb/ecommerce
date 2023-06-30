<?php $this->load->view('inicio/slider'); ?>

<?php $this->load->view('inicio/caracteristicas'); ?>
<!-- <div class="block-space block-space--layout--divider-nl"></div> -->

<?php $this->load->view('inicio/buscar_repuestos'); ?>
<div class="block-space block-space--layout--divider-nl"></div>

<?php $this->load->view('inicio/productos_destacados'); ?>
<div class="block-space block-space--layout--divider-nl"></div>

<?php //$this->load->view('inicio/ofertas'); ?>
<!-- <div class="block-space block-space--layout--divider-lg"></div> -->

<div class="block block-zone">
    <div class="container">
        <div class="block-zone__body">
            <?php
            $this->data['tipo'] = 'marca';
            $this->load->view('inicio/bloques/marcas');
            $this->load->view('inicio/bloques/productos_destacados', $this->data);
            ?>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--divider-sm"></div>

<div class="block block-zone">
    <div class="container">
        <div class="block-zone__body">
            <?php
            $this->data['tipo'] = 'grupo';
            $this->load->view('inicio/bloques/grupos');
            $this->load->view('inicio/bloques/productos_destacados', $this->data);
            ?>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--divider-sm"></div>

<div class="block block-zone">
    <div class="container">
        <div class="block-zone__body">
            <?php
            $this->data['tipo'] = 'linea';
            $this->load->view('inicio/bloques/lineas');
            $this->load->view('inicio/bloques/productos_destacados', $this->data);
            ?>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--divider-nl"></div>

<?php $this->load->view('inicio/bloques_banners'); ?>
<div class="block-space block-space--layout--divider-nl"></div>

<?php $this->load->view('inicio/nuevos_productos'); ?>
<div class="block-space block-space--layout--divider-nl"></div>

<?php // $this->load->view('inicio/ultimas_noticias'); ?>
<!-- <div class="block-space block-space--layout--divider-nl"></div> -->

<?php $this->load->view('inicio/marcas'); ?>
<div class="block-space block-space--layout--divider-nl d-xl-block d-none"></div>

<div class="block block-products-columns">
    <div class="container">
        <div class="row">
            <?php
            $this->data['titulo'] = 'Para tí';
            $this->load->view('inicio/footer_productos_destacados', $this->data);
            $this->data['titulo'] = 'Destacados';
            $this->load->view('inicio/footer_productos_destacados', $this->data);
            $this->data['titulo'] = 'Ofertas';
            $this->load->view('inicio/footer_productos_destacados', $this->data);
            ?>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>