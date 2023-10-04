<!-- Botón del menú para móviles -->
<?php $this->load->view('core/menu_superior/movil'); ?>

<header class="site__header">
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
            <a href="<?php echo site_url('inicio'); ?>" class="logo">
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
            <div class="indicator">
                <a href="<?php echo site_url('productos?busqueda=outlet'); ?>">
                    <img src="<?php echo base_url(); ?>images/outlet.jpg" width="50px" alt="Outlet">
                </a>
            </div>
            
            <!-- Datos del perfil del usuario -->
            <?php $this->load->view('core/menu_superior/perfil'); ?>
            
            <!-- Carrito de compras -->
            <?php $this->load->view('core/menu_superior/carrito'); ?>
        </div>
    </div>
</header>