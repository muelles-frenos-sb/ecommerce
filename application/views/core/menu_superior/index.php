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
            <!-- <div class="indicator">
                <a href="wishlist.html" class="indicator__button">
                    <span class="indicator__icon">
                        <svg width="32" height="32">
                            <path d="M23,4c3.9,0,7,3.1,7,7c0,6.3-11.4,15.9-14,16.9C13.4,26.9,2,17.3,2,11c0-3.9,3.1-7,7-7c2.1,0,4.1,1,5.4,2.6l1.6,2l1.6-2 C18.9,5,20.9,4,23,4 M23,2c-2.8,0-5.4,1.3-7,3.4C14.4,3.3,11.8,2,9,2c-5,0-9,4-9,9c0,8,14,19,16,19s16-11,16-19C32,6,28,2,23,2L23,2 z" />
                        </svg>
                    </span>
                </a>
            </div> -->
            
            <!-- Datos del perfil del usuario -->
            <?php $this->load->view('core/menu_superior/perfil'); ?>
            
            <!-- Carrito de compras -->
            <?php $this->load->view('core/menu_superior/carrito'); ?>
        </div>
    </div>
</header>