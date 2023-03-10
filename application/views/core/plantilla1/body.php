<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <?php $this->load->view('core/plantilla1/header'); ?>
    </head>

    <body>
        <input type="hidden" id="site_url" value="<?php echo site_url(); ?>">
        
        <div class="site">
            <!-- Menú superior -->
            <?php $this->load->view('core/plantilla1/menu_superior/index'); ?>

            <!-- Contenido principal de cada sitio -->
            <div class="site__body">
                <?php $this->load->view($contenido_principal); ?>
            </div>

            <!-- Footer -->
            <?php $this->load->view('core/plantilla1/footer'); ?>
        </div>

        <!-- Menú móvil -->
        <?php $this->load->view('core/plantilla1/menu_movil/index'); ?>
        
        <!-- Modals -->
        <?php $this->load->view('core/plantilla1/modals'); ?>
        
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