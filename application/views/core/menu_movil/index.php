<div class="mobile-menu">
    <div class="mobile-menu__backdrop"></div>
    <div class="mobile-menu__body">
        <button class="mobile-menu__close" type="button">
            <svg width="12" height="12">
                <path d="M10.8,10.8L10.8,10.8c-0.4,0.4-1,0.4-1.4,0L6,7.4l-3.4,3.4c-0.4,0.4-1,0.4-1.4,0l0,0c-0.4-0.4-0.4-1,0-1.4L4.6,6L1.2,2.6 c-0.4-0.4-0.4-1,0-1.4l0,0c0.4-0.4,1-0.4,1.4,0L6,4.6l3.4-3.4c0.4-0.4,1-0.4,1.4,0l0,0c0.4,0.4,0.4,1,0,1.4L7.4,6l3.4,3.4 C11.2,9.8,11.2,10.4,10.8,10.8z" />
            </svg>
        </button>
        <div class="mobile-menu__panel">
            <div class="mobile-menu__panel-header">
                <div class="mobile-menu__panel-title">Menú</div>
            </div>
            <div class="mobile-menu__panel-body">
                <div class="mobile-menu__settings-list">
                    <div class="mobile-menu__setting" data-mobile-menu-item>
                        <button class="mobile-menu__setting-button" title="Language" data-mobile-menu-trigger>
                            <span class="mobile-menu__setting-title">Marcas</span>
                            <span class="mobile-menu__setting-arrow"><svg width="6px" height="9px">
                                    <path d="M0.3,7.4l3-2.9l-3-2.9c-0.4-0.3-0.4-0.9,0-1.3l0,0c0.4-0.3,0.9-0.4,1.3,0L6,4.5L1.6,8.7c-0.4,0.4-0.9,0.4-1.3,0l0,0C-0.1,8.4-0.1,7.8,0.3,7.4z" />
                                </svg>
                            </span>
                        </button>
                        <div class="mobile-menu__setting-panel" data-mobile-menu-panel>
                            <div class="mobile-menu__panel mobile-menu__panel--hidden">
                                <div class="mobile-menu__panel-header">
                                    <button class="mobile-menu__panel-back" type="button">
                                        <svg width="7" height="11">
                                            <path d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z" />
                                        </svg>
                                    </button>
                                    <div class="mobile-menu__panel-title">Seleccionar marca</div>
                                </div>
                                <div class="mobile-menu__panel-body">
                                    <ul class="mobile-menu__links">
                                        <li data-mobile-menu-item>
                                            <a href="<?php echo site_url("productos"); ?>" type="button" class="" data-mobile-menu-trigger>
                                                TODAS
                                            </a>
                                        </li>

                                        <?php foreach ($this->configuracion_model->obtener('marcas') as $marca) { ?>
                                            <li data-mobile-menu-item onClick="javascript:location.href='<?php echo site_url("productos?marca=$marca->nombre"); ?>'">
                                                <button type="button" data-mobile-menu-trigger>
                                                    <?php echo $marca->nombre; ?>
                                                </button>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mobile-menu__divider"></div>
                <div class="mobile-menu__indicators">
                    <?php if(!$this->session->userdata('usuario_id')) { ?>
                        <a class="mobile-menu__indicator" href="<?php echo site_url('sesion'); ?>">
                            <span class="mobile-menu__indicator-icon">
                                <svg width="20" height="20">
                                    <path d="M20,20h-2c0-4.4-3.6-8-8-8s-8,3.6-8,8H0c0-4.2,2.6-7.8,6.3-9.3C4.9,9.6,4,7.9,4,6c0-3.3,2.7-6,6-6s6,2.7,6,6 c0,1.9-0.9,3.6-2.3,4.7C17.4,12.2,20,15.8,20,20z M14,6c0-2.2-1.8-4-4-4S6,3.8,6,6s1.8,4,4,4S14,8.2,14,6z" />
                                </svg>
                            </span>
                            <span class="mobile-menu__indicator-title">INICIAR</span>
                        </a>
                    <?php } else { ?>
                        <a class="mobile-menu__indicator" href="<?php echo site_url('perfil/index/dashboard'); ?>">
                            <span class="mobile-menu__indicator-icon">
                                <svg width="20" height="20">
                                    <path d="M20,20h-2c0-4.4-3.6-8-8-8s-8,3.6-8,8H0c0-4.2,2.6-7.8,6.3-9.3C4.9,9.6,4,7.9,4,6c0-3.3,2.7-6,6-6s6,2.7,6,6 c0,1.9-0.9,3.6-2.3,4.7C17.4,12.2,20,15.8,20,20z M14,6c0-2.2-1.8-4-4-4S6,3.8,6,6s1.8,4,4,4S14,8.2,14,6z" />
                                </svg>
                            </span>
                            <span class="mobile-menu__indicator-title">
                                <?php echo $this->session->userdata('nombres'); ?>
                            </span>
                        </a>
                    <?php } ?>

                    <a class="mobile-menu__indicator" href="<?php echo site_url('carrito/ver'); ?>">
                        <span class="mobile-menu__indicator-icon">
                            <svg width="20" height="20">
                                <circle cx="7" cy="17" r="2" />
                                <circle cx="15" cy="17" r="2" />
                                <path d="M20,4.4V5l-1.8,6.3c-0.1,0.4-0.5,0.7-1,0.7H6.7c-0.4,0-0.8-0.3-1-0.7L3.3,3.9C3.1,3.3,2.6,3,2.1,3H0.4C0.2,3,0,2.8,0,2.6 V1.4C0,1.2,0.2,1,0.4,1h2.5c1,0,1.8,0.6,2.1,1.6L5.1,3l2.3,6.8c0,0.1,0.2,0.2,0.3,0.2h8.6c0.1,0,0.3-0.1,0.3-0.2l1.3-4.4 C17.9,5.2,17.7,5,17.5,5H9.4C9.2,5,9,4.8,9,4.6V3.4C9,3.2,9.2,3,9.4,3h9.2C19.4,3,20,3.6,20,4.4z" />
                            </svg>
                            <span class="mobile-menu__indicator-counter" id="carrito_movil_total_items">0</span>
                        </span>
                        <span class="mobile-menu__indicator-title">Carrito</span>
                    </a>
                </div>
                <div class="mobile-menu__divider"></div>
                <ul class="mobile-menu__links">
                    <li class="main-menu__item main-menu__item--submenu--menu main-menu__item--has-submenu">
                        <a href="<?php echo site_url(); ?>" class="main-menu__link">
                            Tienda
                        </a>
                    </li>

                    <li class="main-menu__item main-menu__item--submenu--menu main-menu__item--has-submenu">
                        <a href="<?php echo site_url('blog/taller_aliado'); ?>" class="main-menu__link">
                            Taller aliado
                        </a>
                    </li>

                    <li class="main-menu__item main-menu__item--submenu--menu main-menu__item--has-submenu">
                        <a href="<?php echo site_url('blog/nosotros'); ?>" class="main-menu__link">
                            Nosotros
                        </a>
                    </li>
                    
                    <li class="main-menu__item main-menu__item--submenu--menu main-menu__item--has-submenu">
                        <a href="<?php echo site_url('blog/contacto'); ?>" class="main-menu__link">
                            Contacto
                        </a>
                    </li>
                    
                    <!-- Garantía -->
                    <?php $this->load->view('bitrix/garantia'); ?>

                    <!-- Botón para pago de facturas -->
                    <p class="m-2">
                        <a href="<?php echo site_url('clientes'); ?>" class="ml-1">
                            <img src="<?php echo base_url(); ?>images/boton_pago.png" height="32" alt="Paga tus facturas">
                        </a>
                    </p>

                    <?php if($this->session->userdata('usuario_id')) { ?>
                        <li data-mobile-menu-item>
                            <a href="<?php echo site_url('sesion/cerrar'); ?>" data-mobile-menu-trigger>
                                Cerrar sesión
                            </a>
                        </li>
                    <?php } ?>

                    <!-- <li data-mobile-menu-item>
                        <a href="index.html" class="" data-mobile-menu-trigger>
                            Home
                            <svg width="7" height="11">
                                <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9 C-0.1,9.8-0.1,10.4,0.3,10.7z" />
                            </svg>
                        </a>
                        <div class="mobile-menu__links-panel" data-mobile-menu-panel>
                            <div class="mobile-menu__panel mobile-menu__panel--hidden">
                                <div class="mobile-menu__panel-header">
                                    <button class="mobile-menu__panel-back" type="button">
                                        <svg width="7" height="11">
                                            <path d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z" />
                                        </svg>
                                    </button>
                                    <div class="mobile-menu__panel-title">Home</div>
                                </div>
                                <div class="mobile-menu__panel-body">
                                    <ul class="mobile-menu__links">
                                        <li data-mobile-menu-item>
                                            <a href="index.html" class="" data-mobile-menu-trigger>
                                                Home One
                                            </a>
                                        </li>
                                        <li data-mobile-menu-item>
                                            <a href="index2.html" class="" data-mobile-menu-trigger>
                                                Home Two
                                            </a>
                                        </li>
                                        <li data-mobile-menu-item>
                                            <a href="header-spaceship-variant-one.html" class="" data-mobile-menu-trigger>
                                                Header Spaceship
                                                <svg width="7" height="11">
                                                    <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9 C-0.1,9.8-0.1,10.4,0.3,10.7z" />
                                                </svg>
                                            </a>
                                            <div class="mobile-menu__links-panel" data-mobile-menu-panel>
                                                <div class="mobile-menu__panel mobile-menu__panel--hidden">
                                                    <div class="mobile-menu__panel-header">
                                                        <button class="mobile-menu__panel-back" type="button">
                                                            <svg width="7" height="11">
                                                                <path d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z" />
                                                            </svg>
                                                        </button>
                                                        <div class="mobile-menu__panel-title">Header Spaceship</div>
                                                    </div>
                                                    <div class="mobile-menu__panel-body">
                                                        <ul class="mobile-menu__links">
                                                            <li data-mobile-menu-item>
                                                                <a href="header-spaceship-variant-one.html" class="" data-mobile-menu-trigger>
                                                                    Variant One
                                                                </a>
                                                            </li>
                                                            <li data-mobile-menu-item>
                                                                <a href="header-spaceship-variant-two.html" class="" data-mobile-menu-trigger>
                                                                    Variant Two
                                                                </a>
                                                            </li>
                                                            <li data-mobile-menu-item>
                                                                <a href="header-spaceship-variant-three.html" class="" data-mobile-menu-trigger>
                                                                    Variant Three
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li data-mobile-menu-item>
                                            <a href="header-classic-variant-one.html" class="" data-mobile-menu-trigger>
                                                Header Classic
                                                <svg width="7" height="11">
                                                    <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9 C-0.1,9.8-0.1,10.4,0.3,10.7z" />
                                                </svg>
                                            </a>
                                            <div class="mobile-menu__links-panel" data-mobile-menu-panel>
                                                <div class="mobile-menu__panel mobile-menu__panel--hidden">
                                                    <div class="mobile-menu__panel-header">
                                                        <button class="mobile-menu__panel-back" type="button">
                                                            <svg width="7" height="11">
                                                                <path d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z" />
                                                            </svg>
                                                        </button>
                                                        <div class="mobile-menu__panel-title">Header Classic</div>
                                                    </div>
                                                    <div class="mobile-menu__panel-body">
                                                        <ul class="mobile-menu__links">
                                                            <li data-mobile-menu-item>
                                                                <a href="header-classic-variant-one.html" class="" data-mobile-menu-trigger>
                                                                    Variant One
                                                                </a>
                                                            </li>
                                                            <li data-mobile-menu-item>
                                                                <a href="header-classic-variant-two.html" class="" data-mobile-menu-trigger>
                                                                    Variant Two
                                                                </a>
                                                            </li>
                                                            <li data-mobile-menu-item>
                                                                <a href="header-classic-variant-three.html" class="" data-mobile-menu-trigger>
                                                                    Variant Three
                                                                </a>
                                                            </li>
                                                            <li data-mobile-menu-item>
                                                                <a href="header-classic-variant-four.html" class="" data-mobile-menu-trigger>
                                                                    Variant Four
                                                                </a>
                                                            </li>
                                                            <li data-mobile-menu-item>
                                                                <a href="header-classic-variant-five.html" class="" data-mobile-menu-trigger>
                                                                    Variant Five
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li data-mobile-menu-item>
                                            <a href="mobile-header-variant-one.html" class="" data-mobile-menu-trigger>
                                                Mobile Header
                                                <svg width="7" height="11">
                                                    <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9 C-0.1,9.8-0.1,10.4,0.3,10.7z" />
                                                </svg>
                                            </a>
                                            <div class="mobile-menu__links-panel" data-mobile-menu-panel>
                                                <div class="mobile-menu__panel mobile-menu__panel--hidden">
                                                    <div class="mobile-menu__panel-header">
                                                        <button class="mobile-menu__panel-back" type="button">
                                                            <svg width="7" height="11">
                                                                <path d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z" />
                                                            </svg>
                                                        </button>
                                                        <div class="mobile-menu__panel-title">Mobile Header</div>
                                                    </div>
                                                    <div class="mobile-menu__panel-body">
                                                        <ul class="mobile-menu__links">
                                                            <li data-mobile-menu-item>
                                                                <a href="mobile-header-variant-one.html" class="" data-mobile-menu-trigger>
                                                                    Variant One
                                                                </a>
                                                            </li>
                                                            <li data-mobile-menu-item>
                                                                <a href="mobile-header-variant-two.html" class="" data-mobile-menu-trigger>
                                                                    Variant Two
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li> -->
                </ul>
                <div class="mobile-menu__spring"></div>
                <div class="mobile-menu__divider"></div>
                <a class="mobile-menu__contacts" href="<?php echo site_url('blog/contacto'); ?>">
                    <div class="mobile-menu__contacts-subtitle">Llámanos</div>
                    <div class="mobile-menu__contacts-title"><?php echo $this->config->item('telefono'); ?></div>
                </a>
            </div>
        </div>
    </div>
</div>