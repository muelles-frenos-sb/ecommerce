<div class="indicator indicator--trigger--click">
    <a href="<?php echo site_url('sesion'); ?>" class="indicator__button">
        <span class="indicator__icon">
            <svg width="32" height="32">
                <path d="M16,18C9.4,18,4,23.4,4,30H2c0-6.2,4-11.5,9.6-13.3C9.4,15.3,8,12.8,8,10c0-4.4,3.6-8,8-8s8,3.6,8,8c0,2.8-1.5,5.3-3.6,6.7 C26,18.5,30,23.8,30,30h-2C28,23.4,22.6,18,16,18z M22,10c0-3.3-2.7-6-6-6s-6,2.7-6,6s2.7,6,6,6S22,13.3,22,10z" />
            </svg>
        </span>
        
        <?php if(!$this->session->userdata('usuario_id')) { ?>
            <span class="indicator__title">Hola, bienvenido</span>
            <span class="indicator__value">Inicia sesión</span>
        <?php } else { ?>
            <span class="indicator__title">Hola, bienvenido</span>
            <span class="indicator__value">
                <?php echo $this->session->userdata('nombres'); ?>
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
                        <label for="usuario" class="sr-only">Nombre de usuario</label>
                        <input id="usuario" type="text" class="form-control form-control-sm" placeholder="Nombre de usuario">
                    </div>
                    <div class="form-group">
                        <label for="clave" class="sr-only">Clave</label>
                        <div class="account-menu__form-forgot">
                            <input id="clave" type="password" class="form-control form-control-sm" placeholder="Clave">
                            
                            <?php if(ENVIRONMENT == 'development') { ?>
                                <a href="" class="account-menu__form-forgot-link">¿Olvidaste?</a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group account-menu__form-button">
                        <button type="submit" class="btn btn-primary btn-sm" onClick="javascript:iniciarSesion(event)">Iniciar</button>
                    </div>

                    <?php if(ENVIRONMENT == 'development') { ?>
                        <div class="account-menu__form-link">
                            <a href="account-login.html">Crear cuenta</a>
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