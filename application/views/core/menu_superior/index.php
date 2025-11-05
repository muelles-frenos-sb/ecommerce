<div class="fixed-top">
    <!-- Botón del menú para móviles -->
    <?php $this->load->view('core/menu_superior/movil'); ?>
</div>

<header class="site__header fixed-top">
    <div class="header">
        <div class="header__topbar-classic-bg"></div>

        <div class="header__navbar">
            <!-- Filtros de marcas -->
            <div class="header__navbar-departments">
                <?php $this->load->view('core/menu_superior/marcas'); ?>
            </div>

            <!-- Opciones del menu -->
            <div class="header__navbar-menu">
                <div class="main-menu">
                    <ul class="main-menu__list">
                        <!-- Tienda -->
                        <li class="main-menu__item">
                            <a href="<?php echo site_url(); ?>" class="main-menu__link">
                                Tienda
                            </a>
                        </li><!-- Tienda -->

                        <!-- Crédito -->
                        <li class="main-menu__item main-menu__item--submenu--megamenu main-menu__item--has-submenu">
                            <a href="<?php echo site_url('blog/credito'); ?>" class="main-menu__link">
                                Crédito
                                <svg width="7px" height="5px">
                                    <path d="M0.280,0.282 C0.645,-0.084 1.238,-0.077 1.596,0.297 L3.504,2.310 L5.413,0.297 C5.770,-0.077 6.363,-0.084 6.728,0.282 C7.080,0.634 7.088,1.203 6.746,1.565 L3.504,5.007 L0.262,1.565 C-0.080,1.203 -0.072,0.634 0.280,0.282 Z" />
                                </svg>
                            </a>
                            <div class="main-menu__submenu">
                                <div class="main-menu__megamenu main-menu__megamenu--size--nl">
                                    <div class="megamenu">
                                        <div class="row">
                                            <div class="col-12">
                                                <ul class="megamenu__links megamenu-links megamenu-links--root">
                                                    <li class="megamenu-links__item megamenu-links__item--has-submenu">
                                                        <!-- <a class="megamenu-links__item-link" href="">Opciones</a> -->
                                                        <ul class="megamenu-links">
                                                            <li class="megamenu-links__item">
                                                                <a class="megamenu-links__item-link" href="<?php echo site_url('clientes/credito'); ?>">Solicita crédito</a>
                                                            </li>
                                                            <li class="megamenu-links__item">
                                                                <a class="megamenu-links__item-link" href="https://api.whatsapp.com/send?phone=573162694009&text=%C2%A1Hola!,%20me%20gustar%C3%ADa%20consultar%20mi%20cr%C3%A9dito%20en%20Repuestos%20Sim%C3%B3n%20Bol%C3%ADvar" target="_blank">Consulta tu cupo</a>
                                                            </li>
                                                            <li class="megamenu-links__item">
                                                                <a class="megamenu-links__item-link" href="<?php echo site_url('clientes/credito'); ?>">Actualizar tus datos</a>
                                                            </li>
                                                            <li class="megamenu-links__item">
                                                                <a class="megamenu-links__item-link" href="<?php echo site_url('clientes'); ?>">Paga facturas</a>
                                                            </li>
                                                            <li class="megamenu-links__item">
                                                                <a class="megamenu-links__item-link" href="https://www.youtube.com/watch?v=6-oYSVOMPaY" target="_blank">¿Cómo pagar por la página web?</a>
                                                            </li>
                                                            <li class="megamenu-links__item">
                                                                <a class="megamenu-links__item-link" href="https://youtu.be/UzmWpRfq398?si=XYHS-y27Wbw4ruNK" target="_blank">¿Cómo diligenciar la solicitud de crédito?</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li><!-- Crédito -->

                        <!-- Talleres aliados -->
                        <li class="main-menu__item">
                            <a href="<?php echo site_url('blog/taller_aliado'); ?>" class="main-menu__link">
                                Talleres aliados
                            </a>
                        </li><!-- Talleres aliados -->

                        <!-- Nosotros -->
                        <li class="main-menu__item main-menu__item--submenu--megamenu main-menu__item--has-submenu">
                            <a href="<?php echo site_url('blog/nosotros'); ?>" class="main-menu__link">
                                Nosotros
                                <svg width="7px" height="5px">
                                    <path d="M0.280,0.282 C0.645,-0.084 1.238,-0.077 1.596,0.297 L3.504,2.310 L5.413,0.297 C5.770,-0.077 6.363,-0.084 6.728,0.282 C7.080,0.634 7.088,1.203 6.746,1.565 L3.504,5.007 L0.262,1.565 C-0.080,1.203 -0.072,0.634 0.280,0.282 Z" />
                                </svg>
                            </a>
                            <div class="main-menu__submenu">
                                <div class="main-menu__megamenu main-menu__megamenu--size--nl">
                                    <div class="megamenu">
                                        <div class="row">
                                            <div class="col-12">
                                                <ul class="megamenu__links megamenu-links megamenu-links--root">
                                                    <li class="megamenu-links__item megamenu-links__item--has-submenu">
                                                        <!-- <a class="megamenu-links__item-link" href="">Opciones</a> -->
                                                        <ul class="megamenu-links">
                                                            <li class="megamenu-links__item">
                                                                <a class="megamenu-links__item-link" href="<?php echo site_url('blog/nosotros'); ?>">Catálogos</a>
                                                            </li>
                                                            <li class="megamenu-links__item">
                                                                <a class="megamenu-links__item-link" href="<?php echo site_url('blog/contacto'); ?>">Datos de contacto</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li><!-- Nosotros -->
                        
                        <!-- Blog -->
                        <li class="main-menu__item">
                            <a href="<?php echo site_url('blog'); ?>" class="main-menu__link">
                                Blog
                            </a>
                        </li><!-- Blog -->

                        <!-- Contacto -->
                        <li class="main-menu__item main-menu__item--submenu--megamenu main-menu__item--has-submenu">
                            <a href="<?php echo site_url('blog/contacto'); ?>" class="main-menu__link">
                                Contacto
                                <svg width="7px" height="5px">
                                    <path d="M0.280,0.282 C0.645,-0.084 1.238,-0.077 1.596,0.297 L3.504,2.310 L5.413,0.297 C5.770,-0.077 6.363,-0.084 6.728,0.282 C7.080,0.634 7.088,1.203 6.746,1.565 L3.504,5.007 L0.262,1.565 C-0.080,1.203 -0.072,0.634 0.280,0.282 Z" />
                                </svg>
                            </a>
                            <div class="main-menu__submenu">
                                <div class="main-menu__megamenu main-menu__megamenu--size--nl">
                                    <div class="megamenu">
                                        <div class="row">
                                            <div class="col-12">
                                                <ul class="megamenu__links megamenu-links megamenu-links--root">
                                                    <li class="megamenu-links__item megamenu-links__item--has-submenu">
                                                        <!-- <a class="megamenu-links__item-link" href="">Opciones</a> -->
                                                        <ul class="megamenu-links">
                                                            <li class="megamenu-links__item">
                                                                <a class="megamenu-links__item-link" href="<?php echo site_url('logistica/garantias'); ?>">Garantía</a>
                                                            </li>
                                                            <li class="megamenu-links__item">
                                                                <a class="megamenu-links__item-link" href="#">PQRS</a>
                                                            </li>
                                                            <li class="megamenu-links__item">
                                                                <a class="megamenu-links__item-link" href="#">Cartera</a><br>
                                                            </li>
                                                            <li class="megamenu-links__item">
                                                                <?php $this->load->view('bitrix/empleo'); ?>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li><!-- Contacto -->

                        <!-- Más -->
                        <li class="main-menu__item main-menu__item--submenu--megamenu main-menu__item--has-submenu">
                            <a href="#" class="main-menu__link">
                                Más...
                                <svg width="7px" height="5px">
                                    <path d="M0.280,0.282 C0.645,-0.084 1.238,-0.077 1.596,0.297 L3.504,2.310 L5.413,0.297 C5.770,-0.077 6.363,-0.084 6.728,0.282 C7.080,0.634 7.088,1.203 6.746,1.565 L3.504,5.007 L0.262,1.565 C-0.080,1.203 -0.072,0.634 0.280,0.282 Z" />
                                </svg>
                            </a>
                            <div class="main-menu__submenu">
                                <div class="main-menu__megamenu main-menu__megamenu--size--nl">
                                    <div class="megamenu">
                                        <div class="row">
                                            <div class="col-12">
                                                <ul class="megamenu__links megamenu-links megamenu-links--root">
                                                    <li class="megamenu-links__item megamenu-links__item--has-submenu">
                                                        <ul class="megamenu-links">
                                                            <li class="megamenu-links__item">
                                                                <a class="megamenu-links__item-link" href="<?php echo site_url('logistica/garantias'); ?>">Garantía</a>
                                                            </li>
                                                            <li class="megamenu-links__item">
                                                                <a class="megamenu-links__item-link" href="<?php echo site_url('blog/distribuidores'); ?>">Distribuidores</a><br>
                                                            </li>
                                                            <li class="megamenu-links__item">
                                                                <a class="megamenu-links__item-link" href="<?php echo site_url('denuncias'); ?>">Denuncias</a>
                                                            </li>
                                                            <li class="megamenu-links__item">
                                                                <?php $this->load->view('bitrix/empleo'); ?>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li><!-- Más -->
                    </ul>
                </div>
            </div><!-- Opciones del menu -->

            <!-- Botón de pago de facturas -->
            <div class="header__navbar-phone phone">
                <a href="<?php echo site_url('clientes'); ?>" class="phone__body" target="_blank">
                    <img src="<?php echo base_url(); ?>images/boton_pago.png" height="32" alt="Paga tus facturas">
                </a>
            </div><!-- Botón de pago de facturas -->
        </div>

        <!-- Logo -->
        <div class="header__logo">
            <a href="<?php echo site_url(); ?>" class="logo">
                <div class="logo__image">
                    <img src="<?php echo base_url().'images/logo.png'; ?>" height="70">
                </div>
            </a>
        </div><!-- Logo -->

        <!-- Búsqueda dinámica -->
        <div class="header__search">
            <?php $this->load->view('core/menu_superior/busqueda'); ?>
        </div><!-- Búsqueda dinámica -->

        
        <div class="header__indicators">
            <!-- Sesión -->
            <?php $this->load->view('core/menu_superior/sesion'); ?>

            <!-- Carrito de compras -->
            <div class="indicator indicator--trigger--click">
                <a href="#" class="indicator__button">
                    <span class="indicator__icon">
                        <svg width="32" height="32">
                            <circle cx="10.5" cy="27.5" r="2.5" />
                            <circle cx="23.5" cy="27.5" r="2.5" />
                            <path d="M26.4,21H11.2C10,21,9,20.2,8.8,19.1L5.4,4.8C5.3,4.3,4.9,4,4.4,4H1C0.4,4,0,3.6,0,3s0.4-1,1-1h3.4C5.8,2,7,3,7.3,4.3 l3.4,14.3c0.1,0.2,0.3,0.4,0.5,0.4h15.2c0.2,0,0.4-0.1,0.5-0.4l3.1-10c0.1-0.2,0-0.4-0.1-0.4C29.8,8.1,29.7,8,29.5,8H14 c-0.6,0-1-0.4-1-1s0.4-1,1-1h15.5c0.8,0,1.5,0.4,2,1c0.5,0.6,0.6,1.5,0.4,2.2l-3.1,10C28.5,20.3,27.5,21,26.4,21z" />
                        </svg>
                        <span class="indicator__counter" id="carrito_total_items">0</span>
                    </span>
                    <!-- <span class="indicator__title">Tu compra</span> -->
                    <span class="indicator__value" id="carrito_total">$ 0</span>
                </a>

                <div class="indicator__content">
                    <div class="dropcart" id="contenedor_carrito_detalle"></div>
                </div>
            </div><!-- Carrito de compras -->

            <!-- Redes sociales -->
            <div class="indicator">
                <div class="footer-newsletter__social-links social-links">
                    <ul class="social-links__list">
                        <li class="social-links__item social-links__item--instagram">
                            <a href="https://www.instagram.com/repuestossimonbolivar/" target="_blank"><i class="fab fa-instagram"></i></a>
                        </li>

                        <li class="social-links__item social-links__item--facebook">
                            <a href="https://www.facebook.com/RepuestosSimonBolivar" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        </li>

                        <li class="social-links__item social-links__item--tiktok">
                            <a href="https://www.tiktok.com/@repuestossimonbolivar" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="-100 -50 700 700">
                                    <path d="M448 209.9a210.1 210.1 0 0 1 -122.8-39.3V349.4A162.6 162.6 0 1 1 185 188.3V278.2a74.6 74.6 0 1 0 52.2 71.2V0l88 0a121.2 121.2 0 0 0 1.9 22.2h0A122.2 122.2 0 0 0 381 102.4a121.4 121.4 0 0 0 67 20.1z"/>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </div>
            </div><!-- Redes sociales -->
        </div>
    </div>
</header>