<div class="header__navbar-menu">
    <div class="main-menu">
        <ul class="main-menu__list">
            <li class="main-menu__item main-menu__item--submenu--menu">
                <a href="<?php echo site_url('inicio'); ?>" class="main-menu__link">
                    Tienda
                </a>
            </li>

            <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_ver'], $permisos)) { ?>
                <li class="main-menu__item main-menu__item--submenu--menu main-menu__item--has-submenu">
                    <a href="<?php echo site_url(); ?>" class="main-menu__link">
                        Configuración
                        <svg width="7px" height="5px">
                            <path d="M0.280,0.282 C0.645,-0.084 1.238,-0.077 1.596,0.297 L3.504,2.310 L5.413,0.297 C5.770,-0.077 6.363,-0.084 6.728,0.282 C7.080,0.634 7.088,1.203 6.746,1.565 L3.504,5.007 L0.262,1.565 C-0.080,1.203 -0.072,0.634 0.280,0.282 Z" />
                        </svg>
                    </a>
                    <div class="main-menu__submenu">
                        <ul class="menu">
                            <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_facturas_ver'], $permisos)) { ?>
                                <li class="menu__item">
                                    <a href="<?php echo site_url('configuracion/facturas/ver'); ?>" class="menu__link">
                                        Facturas
                                    </a>
                                </li>
                            <?php } ?>

                            <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_terceros_ver'], $permisos)) { ?>
                                <li class="menu__item">
                                    <a href="<?php echo site_url('configuracion/terceros/ver'); ?>" class="menu__link">
                                        Usuarios
                                    </a>
                                </li>
                            <?php } ?>

                            <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_perfiles_ver'], $permisos)) { ?>
                                <li class="menu__item">
                                    <a href="<?php echo site_url('configuracion/perfiles/ver'); ?>" class="menu__link">
                                        Perfiles
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </li>
            <?php } ?>
            
            <?php $this->load->view('bitrix/garantia'); ?>

            <li class="main-menu__item main-menu__item--submenu--menu main-menu__item--has-submenu">
                <a href="<?php echo site_url('blog/taller_aliado'); ?>" class="main-menu__link">
                    Taller Aliado
                </a>
            </li>

            <li class="main-menu__item main-menu__item--submenu--menu main-menu__item--has-submenu">
                <a href="<?php echo site_url('blog/contacto'); ?>" class="main-menu__link">
                    Contacto
                </a>
            </li>
        </ul>
    </div>
</div>