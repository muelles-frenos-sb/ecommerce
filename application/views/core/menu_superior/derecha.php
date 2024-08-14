<div class="header__topbar-end">
    <div class="topbar topbar--spaceship-end">
        <!-- Perfil -->
        <div class="indicator indicator--trigger--click">
            <a href="<?php echo site_url('sesion'); ?>" class="indicator__button mt-1">
                <span class="indicator__icon">
                    <img src="<?php echo base_url(); ?>images/icons/perfil.svg" height="24">
                </span>
                
                <?php if(!$this->session->userdata('usuario_id')) { ?>
                    <span class="indicator__value">Inicia sesión</span>
                <?php } else { ?>
                    <span class="indicator__value">
                        <?php echo $this->session->userdata('nombres'); ?>
                        
                        <svg width="8px" height="8px">
                            <path d="M0.280,0.282 C0.645,-0.084 1.238,-0.077 1.596,0.297 L3.504,2.310 L5.413,0.297 C5.770,-0.077 6.363,-0.084 6.728,0.282 C7.080,0.634 7.088,1.203 6.746,1.565 L3.504,5.007 L0.262,1.565 C-0.080,1.203 -0.072,0.634 0.280,0.282 Z" />
                        </svg>
                    </span>
                <?php } ?>
            </a>
            <div class="indicator__content">
                <div class="account-menu">
                    <?php if(!$this->session->userdata('usuario_id')) { ?>
                        <!-- Formulario para el inicio de sesión -->
                        <form class="account-menu__form">
                            <div class="account-menu__form-title">
                                Inicia sesión en tu cuenta
                            </div>
                            <div class="form-group">
                                <label for="menu_login" class="sr-only">Nombre de usuario</label>
                                <input id="menu_login" type="text" class="form-control form-control-sm" placeholder="Nombre de usuario">
                            </div>
                            <div class="form-group">
                                <label for="menu_clave" class="sr-only">Clave</label>
                                <div class="account-menu__form-forgot">
                                    <input id="menu_clave" type="password" class="form-control form-control-sm" placeholder="Clave">
                                    
                                    <?php if(ENVIRONMENT == 'development') { ?>
                                        <a href="" class="account-menu__form-forgot-link">Recordar</a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group account-menu__form-button">
                                <button type="submit" class="btn btn-primary btn-sm" onClick="javascript:iniciarSesion(event, null, 'menu_login', 'menu_clave')">Iniciar</button>
                            </div>

                            <?php if(ENVIRONMENT == 'development') { ?>
                                <div class="account-menu__form-link">
                                    <a href="<?php echo site_url('usuarios/registro'); ?>">Crear cuenta</a>
                                </div>
                            <?php } ?>
                        </form>
                    <?php } else { ?>
                        <!-- Datos del usuario logueado -->
                        <div class="account-menu__divider"></div>
                        <a href="" class="account-menu__user">
                            <div class="account-menu__user-avatar">
                                <img src="<?php echo base_url(); ?>images/avatars/avatar-4.jpg" alt="">
                            </div>
                            <div class="account-menu__user-info">
                                <div class="account-menu__user-name"><?php echo "{$this->session->userdata('nombres')} {$this->session->userdata('primer_apellido')}"; ?></div>
                                <div class="account-menu__user-email"><?php echo $this->session->userdata('email'); ?></div>
                            </div>
                        </a>
                        <div class="account-menu__divider"></div>
                        <ul class="account-menu__links">
                            <li><a href="<?php echo site_url('perfil/index/dashboard'); ?>">Dashboard</a></li>
                            <li><a href="<?php echo site_url('perfil/index/garage'); ?>">Garage</a></li>
                            <li><a href="<?php echo site_url('perfil/index/editar'); ?>">Editar perfil</a></li>
                            <li><a href="<?php echo site_url('perfil/index/pedidos'); ?>">Mis pedidos</a></li>
                            <li><a href="<?php echo site_url('perfil/index/direcciones'); ?>">Mis direcciones</a></li>
                        </ul>
                        <div class="account-menu__divider"></div>
                        <ul class="account-menu__links">
                            <li><a href="<?php echo site_url('sesion/cerrar'); ?>">Cerrar sesión</a></li>
                        </ul>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Carrito -->
        <div class="indicator indicator--trigger--click">
            <a href="#" class="indicator__button mt-1">
                <span class="indicator__icon">
                    <span class="indicator__counter" id="carrito_total_items">0</span>
                    <img src="<?php echo base_url(); ?>images/icons/carrito.svg" height="24">
                </span>
                <!-- <span class="indicator__title">Carrito</span> -->
                <span class="indicator__value" id="carrito_total">
                    $ 0
                </span>
            </a>
            <div class="indicator__content">
                <div class="dropcart" id="contenedor_carrito_detalle"></div>
            </div>
        </div>
    </div>
</div>