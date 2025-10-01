<div class="header__navbar-menu">
    <div class="main-menu">
        <ul class="main-menu__list">
            <li class="main-menu__item mr-1">
                <a href="<?php echo site_url(); ?>" class="main-menu__link">
                    Tienda
                </a>
            </li>

            <?php $this->load->view('bitrix/garantia'); ?>

            <li class="main-menu__item mr-1">
                <a href="<?php echo site_url('blog/taller_aliado'); ?>" class="main-menu__link">
                    Taller Aliado
                </a>
            </li>

            <li class="mr-1 menu_enfasis">
                <a href="<?php echo site_url('blog/credito'); ?>" class="main-menu__link">
                    Crédito
                </a>
            </li>

            <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_ver'], $permisos)) { ?>
                <li class="main-menu__item main-menu__item--submenu--menu main-menu__item--has-submenu">
                    <a href="javascript:;" class="main-menu__link">
                        Ajustes
                        <svg width="7px" height="5px">
                            <path d="M0.280,0.282 C0.645,-0.084 1.238,-0.077 1.596,0.297 L3.504,2.310 L5.413,0.297 C5.770,-0.077 6.363,-0.084 6.728,0.282 C7.080,0.634 7.088,1.203 6.746,1.565 L3.504,5.007 L0.262,1.565 C-0.080,1.203 -0.072,0.634 0.280,0.282 Z" />
                        </svg>
                    </a>
                    <div class="main-menu__submenu">
                        <ul class="menu">
                            <li class="menu__item menu__item--has-submenu">
                                <a href="#" class="menu__link">
                                    Clientes
                                    <span class="menu__arrow">
                                        <svg width="6px" height="9px">
                                            <path d="M0.3,7.4l3-2.9l-3-2.9c-0.4-0.3-0.4-0.9,0-1.3l0,0c0.4-0.3,0.9-0.4,1.3,0L6,4.5L1.6,8.7c-0.4,0.4-0.9,0.4-1.3,0l0,0C-0.1,8.4-0.1,7.8,0.3,7.4z" />
                                        </svg>
                                    </span>
                                </a>
                                <div class="menu__submenu">
                                    <ul class="menu">
                                        <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_recibos_ver'], $permisos)) { ?>
                                            <li class="menu__item">
                                                <a href="<?php echo site_url("configuracion/recibos/ver/2"); ?>" class="menu__link">
                                                    Pagos
                                                </a>
                                            </li>
                                        <?php } ?>

                                        <?php // if(isset($permisos) && in_array(['configuracion' => 'configuracion_recibos_ver'], $permisos)) { ?>
                                        <li class="menu__item">
                                                <a href="<?php echo site_url('clientes/credito/ver'); ?>" class="menu__link">
                                                    Solicitudes de crédito
                                                </a>
                                            </li>
                                        <?php // } ?>

                                        <?php // if(isset($permisos) && in_array(['configuracion' => 'configuracion_recibos_ver'], $permisos)) { ?>
                                        <li class="menu__item">
                                                <a href="<?php echo site_url('clientes/pedidos/ver'); ?>" class="menu__link">
                                                    Pedidos
                                                </a>
                                            </li>
                                        <?php // } ?>
                                    </ul>
                                </div>
                            </li>

                            <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_contactos_ver'], $permisos)) { ?>
                                <li class="menu__item">
                                    <a href="<?php echo site_url("configuracion/contactos/ver"); ?>" class="menu__link">
                                        Contactos
                                    </a>
                                </li>
                            <?php } ?>

                            <li class="menu__item menu__item--has-submenu">
                                <a href="#" class="menu__link">
                                    Logística
                                    <span class="menu__arrow">
                                        <svg width="6px" height="9px">
                                            <path d="M0.3,7.4l3-2.9l-3-2.9c-0.4-0.3-0.4-0.9,0-1.3l0,0c0.4-0.3,0.9-0.4,1.3,0L6,4.5L1.6,8.7c-0.4,0.4-0.9,0.4-1.3,0l0,0C-0.1,8.4-0.1,7.8,0.3,7.4z" />
                                        </svg>
                                    </span>
                                </a>
                                <div class="menu__submenu">
                                    <ul class="menu">
                                        <li class="menu__item">
                                            <a href="<?php echo site_url('logistica/envios/cotizacion'); ?>" class="menu__link">
                                                Cotizar envío con TCC
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_usuarios_ver'], $permisos)) { ?>
                                <li class="menu__item">
                                    <a href="<?php echo site_url('configuracion/usuarios/ver'); ?>" class="menu__link">
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

                            <?php if(isset($permisos) && in_array(['configuracion' => 'configuracion_productos_ver'], $permisos)) { ?>
                                <li class="menu__item">
                                    <a href="<?php echo site_url('configuracion/productos/ver'); ?>" class="menu__link">
                                        Productos
                                    </a>
                                </li>
                            <?php } ?>

                            <?php if(isset($permisos) && in_array(['proveedores' => 'proveedores_ver'], $permisos)) { ?>
                                <li class="menu__item menu__item--has-submenu">
                                    <a href="#" class="menu__link">
                                        Proveedores
                                        <span class="menu__arrow">
                                            <svg width="6px" height="9px">
                                                <path d="M0.3,7.4l3-2.9l-3-2.9c-0.4-0.3-0.4-0.9,0-1.3l0,0c0.4-0.3,0.9-0.4,1.3,0L6,4.5L1.6,8.7c-0.4,0.4-0.9,0.4-1.3,0l0,0C-0.1,8.4-0.1,7.8,0.3,7.4z" />
                                            </svg>
                                        </span>
                                    </a>
                                    <div class="menu__submenu">
                                        <ul class="menu">
                                            <li class="menu__item">
                                                <a href="<?php echo site_url('proveedores/maestro'); ?>" class="menu__link">
                                                    Maestro
                                                </a>
                                            </li>
                                            <li class="menu__item">
                                                <a href="<?php echo site_url('proveedores/solicitudes'); ?>" class="menu__link">
                                                    Solicitudes de precios
                                                </a>
                                            </li>
                                            <li class="menu__item">
                                                <a href="<?php echo site_url('proveedores'); ?>" class="menu__link">
                                                    Cotizaciones
                                                </a>
                                            </li>

                                            <?php if(isset($permisos) && in_array(['proveedores' => 'proveedores_ver_facturas'], $permisos)) { ?>
                                                <li class="menu__item">
                                                    <a href="<?php echo site_url("proveedores/facturas"); ?>" class="menu__link">
                                                        Facturas
                                                    </a>
                                                </li>
                                            <?php } ?>

                                            <?php if(isset($permisos) && in_array(['proveedores' => 'proveedores_ver_certificados'], $permisos)) { ?>
                                                <li class="menu__item">
                                                    <a href="<?php echo site_url("proveedores/certificados"); ?>" class="menu__link">
                                                        Certificados tributarios
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>