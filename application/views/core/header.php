<!-- Google TAG Manager para poder crear eventos y rastrear elementos -->
<script>
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-NK3HNKKP');
</script>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="format-detection" content="telephone=no">

<!-- Meta TAG para Google Analytics -->
<meta name="google-site-verification" content="6YWQqaPE-WcHqYZrqVAiR44E-So6A330C0ZWxMy7dhM"/>

<?php 
if (isset($metadatos)) {
    echo "<meta name='description' content='{$metadatos['descripcion']}'/>";
    echo "<meta name='keywords' content='{$metadatos['palabras_clave']}'/>";
    echo "<title>{$metadatos['titulo']}</title>";
} else {
    echo "<title>Simón Bolívar - Repuestos y Accesorios</title>";
}
?>

<link rel="icon" type="image/png" href="<?php echo base_url(); ?>images/favicon.ico">

<!-- fonts -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i">

<!-- css -->
<link rel="stylesheet" href="<?php echo base_url(); ?>vendor/bootstrap/css/bootstrap.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>vendor/owl-carousel/assets/owl.carousel.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>vendor/photoswipe/photoswipe.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>vendor/photoswipe/default-skin/default-skin.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>vendor/select2/css/select2.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>css/select2.min.css" />
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css"> -->
<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.header-spaceship-variant-one.css?<?php echo date('Ymdhis'); ?>" media="(min-width: 1200px)">
<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.mobile-header-variant-one.css?<?php echo date('Ymdhis'); ?>" media="(max-width: 1199px)">
<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css?<?php echo date('Ymdhis'); ?>">
<link rel="stylesheet" href="<?php echo base_url(); ?>css/notificacion.css?<?php echo date('Ymdhis'); ?>">
<link rel="stylesheet" href="<?php echo base_url(); ?>css/datatables.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url(); ?>css/estilos.css?<?php echo date('Ymdhis'); ?>">

<!-- font - fontawesome -->
<link rel="stylesheet" href="<?php echo base_url(); ?>vendor/fontawesome/css/all.min.css?">

<!-- Scripts -->
<script type="text/javascript" src="https://checkout.wompi.co/widget.js"></script>
<script src="<?php echo base_url(); ?>vendor/jquery/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>js/select2.min.js"></script>
<script src="<?php echo base_url(); ?>js/datatables.js"></script>
<script src="<?php echo base_url(); ?>vendor/sweetalert2/sweetalert2.min.js"></script>
<script src="<?php echo base_url(); ?>js/carrito.js?<?php echo date('Ymdhis'); ?>"></script>
<script src="<?php echo base_url(); ?>js/index.js?<?php echo date('Ymdhis'); ?>"></script>