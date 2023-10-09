
<div class="fixed-top">
    <!-- Botón del menú para móviles -->
    <?php $this->load->view('core/menu_superior/movil'); ?>
</div>

<header class="site__header fixed-top">
    <div class="header">
        <div class="header__megamenu-area megamenu-area"></div>
        <div class="header__topbar-start-bg"></div>
        
        <!-- Sección superior izquierda -->
        <?php $this->load->view('core/menu_superior/izquierda'); ?>

        <div class="header__topbar-end-bg"></div>
        
        <!-- Sección superior derecha -->
        <?php $this->load->view('core/menu_superior/derecha'); ?>
        
        <div class="header__navbar">
            <!-- Filtros de categorías -->
            <?php $this->load->view('core/menu_superior/filtros'); ?>
            
            <!-- Menú de opciones -->
            <?php $this->load->view('core/menu_superior/menu') ?>
        </div>
        <div class="header__logo">
            <a href="<?php echo site_url(); ?>" class="logo">
                <!-- <div class="logo__slogan">
                    TODO PARA VEHÍCULOS PESADOS
                </div> -->

                <div class="logo__image">
                    <img src="<?php echo base_url().'images/logo.png'; ?>" height="70">
                </div>
            </a>
        </div>
        
        <!-- Búsqueda dinámica -->
        <?php $this->load->view('core/menu_superior/busqueda'); ?>

        <div class="header__indicators">
            <div class="main-menu mr-1">
                <ul class="main-menu__list">
                    <li class="main-menu__item">
                        <a href="<?php echo site_url('blog/taller_aliado'); ?>" class="main-menu__link">
                            Mayorista
                        </a>
                    </li>
                </ul>
            </div>

            <div class="indicator mt-1">
                <a href="<?php echo site_url('productos?busqueda=outlet'); ?>">
                    <img src="<?php echo base_url(); ?>images/outlet.png" height="27px" alt="Outlet">
                </a>
            </div>
        </div>
    </div>
</header>