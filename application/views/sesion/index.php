<div class="site__body">
    <div class="block-space block-space--layout--after-header"></div>
    <div class="block">
        <div class="container container--max--lg">
            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card flex-grow-1 mb-md-0 mr-0 mr-lg-3 ml-0 ml-lg-4">
                        <div class="card-body card-body--padding--2">
                            <h3 class="card-title">Iniciar sesión</h3>
                            <form>
                                <div class="form-group">
                                    <label for="sesion_login">Nombre de usuario</label>
                                    <input id="sesion_login" type="text" class="form-control form-control-sm" placeholder="Nombre de usuario">
                                </div>
                                <div class="form-group">
                                    <label for="sesion_clave">Clave</label>
                                    <div class="account-menu__form-forgot">
                                        <input id="sesion_clave" type="password" class="form-control form-control-sm" placeholder="Clave">
                                        
                                        <a href="<?php echo site_url('sesion/recordar_clave'); ?>" class="account-menu__form-forgot-link">Recordar clave</a>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <div class="form-check">
                                        <span class="input-check form-check-input">
                                            <span class="input-check__body">
                                                <input class="input-check__input" type="checkbox" id="signin-remember">
                                                <span class="input-check__box"></span>
                                                <span class="input-check__icon"><svg width="9px" height="7px">
                                                        <path d="M9,1.395L3.46,7L0,3.5L1.383,2.095L3.46,4.2L7.617,0L9,1.395Z" />
                                                    </svg>
                                                </span>
                                            </span>
                                        </span>
                                        <label class="form-check-label" for="signin-remember">Remember Me</label>
                                    </div>
                                </div> -->
                                <div class="form-group mb-0">
                                    <button type="submit" class="btn btn-primary mt-3" onClick="javascript:iniciarSesion(event, '<?php echo $url; ?>', 'sesion_login', 'sesion_clave')">Iniciar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="block-space block-space--layout--before-footer"></div>
</div>