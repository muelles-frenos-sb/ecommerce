<div class="indicator indicator--trigger--click">
    <a href="account-login.html" class="indicator__button">
        <span class="indicator__icon">
            <svg width="32" height="32">
                <path d="M16,18C9.4,18,4,23.4,4,30H2c0-6.2,4-11.5,9.6-13.3C9.4,15.3,8,12.8,8,10c0-4.4,3.6-8,8-8s8,3.6,8,8c0,2.8-1.5,5.3-3.6,6.7
C26,18.5,30,23.8,30,30h-2C28,23.4,22.6,18,16,18z M22,10c0-3.3-2.7-6-6-6s-6,2.7-6,6s2.7,6,6,6S22,13.3,22,10z" />
            </svg>
        </span>
        <span class="indicator__title">Hola, inicia sesión</span>
        <span class="indicator__value">Mi cuenta</span>
    </a>
    <div class="indicator__content">
        <div class="account-menu">
            <form class="account-menu__form">
                <div class="account-menu__form-title">
                    Inicia sesión en tu cuenta
                </div>
                <div class="form-group">
                    <label for="header-signin-email" class="sr-only">Nombre de usuario</label>
                    <input id="header-signin-email" type="email" class="form-control form-control-sm" placeholder="Nombre de usuario">
                </div>
                <div class="form-group">
                    <label for="header-signin-password" class="sr-only">Clave</label>
                    <div class="account-menu__form-forgot">
                        <input id="header-signin-password" type="password" class="form-control form-control-sm" placeholder="Clave">
                        <a href="" class="account-menu__form-forgot-link">¿Olvidaste?</a>
                    </div>
                </div>
                <div class="form-group account-menu__form-button">
                    <button type="submit" class="btn btn-primary btn-sm">Iniciar</button>
                </div>
                <div class="account-menu__form-link">
                    <a href="account-login.html">Crear cuenta</a>
                </div>
            </form>
            <div class="account-menu__divider"></div>
            <a href="" class="account-menu__user">
                <div class="account-menu__user-avatar">
                    <img src="<?php echo base_url(); ?>images/avatars/avatar-4.jpg" alt="">
                </div>
                <div class="account-menu__user-info">
                    <div class="account-menu__user-name">John Arley Cano</div>
                    <div class="account-menu__user-email">contacto@johnarleycano.com</div>
                </div>
            </a>
            <div class="account-menu__divider"></div>
            <ul class="account-menu__links">
                <li><a href="account-dashboard.html">Dashboard</a></li>
                <li><a href="account-dashboard.html">Garage</a></li>
                <li><a href="account-profile.html">Editar perfil</a></li>
                <li><a href="account-orders.html">Mis pedidos</a></li>
                <li><a href="account-addresses.html">Mis direcciones</a></li>
            </ul>
            <div class="account-menu__divider"></div>
            <ul class="account-menu__links">
                <li><a href="account-login.html">Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</div>