<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <?php $this->load->view('core/header'); ?>
    </head>

    <body>
        <input type="hidden" id="site_url" value="<?php echo site_url(); ?>">
        <input type="hidden" id="cantidad_datos" value="<?php echo $this->config->item('cantidad_datos'); ?>">
        
        <div class="site">
            <!-- Menú superior -->
            <?php $this->load->view('core/menu_superior/index'); ?>

            <!-- Contenido principal de cada sitio -->
            <div class="site__body">
                <?php $this->load->view($contenido_principal); ?>
            </div>

            <!-- Footer -->
            <?php $this->load->view('core/footer'); ?>
        </div>

        <!-- Menú móvil -->
        <?php $this->load->view('core/menu_movil/index'); ?>
        
        <!-- Modals -->
        <?php $this->load->view('core/modals'); ?>
        
        <!-- scripts -->
        <script src="<?php echo base_url(); ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="<?php echo base_url(); ?>vendor/owl-carousel/owl.carousel.min.js"></script>
        <script src="<?php echo base_url(); ?>vendor/nouislider/nouislider.min.js"></script>
        <script src="<?php echo base_url(); ?>vendor/photoswipe/photoswipe.min.js"></script>
        <script src="<?php echo base_url(); ?>vendor/photoswipe/photoswipe-ui-default.min.js"></script>
        <script src="<?php echo base_url(); ?>vendor/select2/js/select2.min.js"></script>
        <script src="<?php echo base_url(); ?>js/number.js"></script>
        <script src="<?php echo base_url(); ?>js/main.js?<?php echo date('Ymdhis'); ?>"></script>
    </body>
</html>