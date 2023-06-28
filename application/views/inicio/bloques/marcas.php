<div class="block-zone__card category-card category-card--layout--overlay">
    <div class="category-card__body">
        <div class="category-card__overlay-image">
            <img srcset="<?php echo base_url(); ?>images/categories/marcas.jpg 530w,<?php echo base_url(); ?> images/categories/marcas.jpg 305w" src="<?php echo base_url(); ?>images/categories/category-overlay-1.jpg" sizes="(max-width: 575px) 530px, 305px" alt="">
        </div>
        <div class="category-card__overlay-image category-card__overlay-image--blur">
            <img srcset="<?php echo base_url(); ?>images/categories/marcas.jpg 530w, <?php echo base_url(); ?>images/categories/marcas.jpg 305w" src="<?php echo base_url(); ?>images/categories/category-overlay-1.jpg" sizes="(max-width: 575px) 530px, 305px" alt="">
        </div>
        <div class="category-card__content">
            <div class="category-card__info">
                <div class="category-card__name">
                    <a href="category-4-columns-sidebar.html">Marcas</a>
                </div>
                <ul class="category-card__children">
                    <?php foreach($this->configuracion_model->obtener('marcas') as $marca) echo "<li><a href='".site_url('productos')."?marca=$marca->nombre'>$marca->nombre</a></li>"; ?>
                </ul>
                <div class="category-card__actions">
                    <a href="<?php echo site_url('productos'); ?>" class="btn btn-primary btn-sm">Ver todos</a>
                </div>
            </div>
        </div>
    </div>
</div>