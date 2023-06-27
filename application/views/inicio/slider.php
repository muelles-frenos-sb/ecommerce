<?php
$opciones = [
    'modulo_id' => 1
];
$registros = $this->configuracion_model->obtener('sliders', $opciones);
?>

<div class="block block-slideshow">
    <div class="block-slideshow__carousel" id="block-slideshow__carousel">
        <div class="owl-carousel">
            <?php foreach ($registros as $slider) { ?>
                <a class="block-slideshow__item" href="<?php echo site_url('productos'); ?>">
                    <span class="block-slideshow__item-image block-slideshow__item-image--desktop" style="background-image: url('images/slides/<?php echo $slider->id; ?>.jpg?12345')"></span>
                    <span class="block-slideshow__item-image block-slideshow__item-image--mobile" style="background-image: url('images/slides/<?php echo $slider->id; ?>-mobile.jpg?12345')"></span>
                    
                    <?php if($slider->titulo_enfasis_activo == 1) { ?>
                        <span class="block-slideshow__item-offer">
                            <?php echo $slider->titulo_enfasis; ?>
                        </span>
                    <?php } ?>

                    <span class="block-slideshow__item-title">
                    <?php echo str_replace(["\r\n", "\r", "\n"], '<br>', $slider->titulo_principal); ?>
                    </span>
                    <span class="block-slideshow__item-details">
                    <?php echo $slider->descripcion; ?>
                    </span>
                    <span class="block-slideshow__item-button">
                        Comprar ahora
                    </span>
                </a>
            <?php } ?>
        </div>
    </div>
</div>