<?php $this->load->view('inicio/slider'); ?>
<?php $this->load->view('inicio/buscar_repuestos'); ?>
<div class="block-space block-space--layout--divider-nl"></div>

<?php $this->load->view('inicio/caracteristicas'); ?>
<div class="block-space block-space--layout--divider-nl"></div>

<?php $this->load->view('inicio/productos_destacados'); ?>
<div class="block-space block-space--layout--divider-nl"></div>

<?php //$this->load->view('inicio/ofertas'); ?>
<!-- <div class="block-space block-space--layout--divider-lg"></div> -->

<div class="block block-zone">
    <div class="container">
        <div class="block-zone__body">
            <?php $this->load->view('inicio/bloques/marcas'); ?>
            <?php $this->load->view('inicio/bloques/marcas_detalles'); ?>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--divider-sm"></div>

<div class="block block-zone">
    <div class="container">
        <div class="block-zone__body">
            <?php $this->load->view('inicio/bloques/grupos'); ?>
            <?php $this->load->view('inicio/bloques/grupos_detalles'); ?>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--divider-sm"></div>

<div class="block block-zone">
    <div class="container">
        <div class="block-zone__body">
            <?php $this->load->view('inicio/bloques/tres'); ?>
            <?php $this->load->view('inicio/bloques/tres_detalles'); ?>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--divider-nl"></div>

<?php $this->load->view('inicio/bloques_banners'); ?>
<div class="block-space block-space--layout--divider-nl"></div>

<?php $this->load->view('inicio/nuevos_productos'); ?>
<div class="block-space block-space--layout--divider-nl"></div>

<?php $this->load->view('inicio/ultimas_noticias'); ?>
<div class="block-space block-space--layout--divider-nl"></div>

<?php $this->load->view('inicio/marcas'); ?>
<div class="block-space block-space--layout--divider-nl d-xl-block d-none"></div>

<div class="block block-products-columns">
    <div class="container">
        <div class="row">
            <?php $this->load->view('inicio/footer_productos_destacados'); ?>
            <?php $this->load->view('inicio/footer_ofertas'); ?>
            <?php $this->load->view('inicio/footer_mas_vendidos'); ?>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>