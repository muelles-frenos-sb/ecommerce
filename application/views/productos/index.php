<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <nav class="breadcrumb block-header__breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb__list">
                    <li class="breadcrumb__spaceship-safe-area" role="presentation"></li>
                    <li class="breadcrumb__item breadcrumb__item--parent breadcrumb__item--first">
                        <a href="<?php echo site_url(); ?>" class="breadcrumb__item-link">Inicio</a>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--parent">
                        <a href="<?php echo site_url('productos'); ?>" class="breadcrumb__item-link">Productos</a>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--current breadcrumb__item--last" aria-current="page">
                        <span class="breadcrumb__item-link">Página actual</span>
                    </li>
                    <li class="breadcrumb__title-safe-area" role="presentation"></li>
                </ol>
            </nav>
            <h1 class="block-header__title">Productos</h1>
        </div>
    </div>
</div>

<div class="block-split block-split--has-sidebar">
    <div class="container">
        <div class="block-split__row row no-gutters">
            <?php $this->load->view('productos/menu_lateral/index'); ?>
            <?php $this->load->view('productos/contenedor/index'); ?>
        </div>
        <div class="block-space block-space--layout--before-footer"></div>
    </div>
</div>

<script>
    $().ready(() => {
        // let tipoVista = (localStorage.mfsb_productosFiltroVista) ? localStorage.mfsb_productosFiltroVista : 'list'
        // $('button[]').attr('data-layout').addClass('layout-switcher__button--active')

        // $('.layout-switcher__button').click(evento => {
        //     localStorage.mfsb_productosFiltroVista = evento.currentTarget.attributes['data-layout'].nodeValue
        // })
    })
</script>