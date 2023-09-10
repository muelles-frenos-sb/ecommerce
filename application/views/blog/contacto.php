<div class="site__body">
    <div class="block-header block-header--has-breadcrumb block-header--has-title">
        <div class="container">
            <div class="block-header__body">
                <nav class="breadcrumb block-header__breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb__list">
                        <li class="breadcrumb__spaceship-safe-area" role="presentation"></li>
                        <li class="breadcrumb__item breadcrumb__item--parent breadcrumb__item--first">
                            <a href="<?php echo site_url('inicio'); ?>" class="breadcrumb__item-link">Inicio</a>
                        </li>
                        <li class="breadcrumb__item breadcrumb__item--current breadcrumb__item--last" aria-current="page">
                            <span class="breadcrumb__item-link">Contacto</span>
                        </li>
                        <li class="breadcrumb__title-safe-area" role="presentation"></li>
                    </ol>
                </nav>
                <h1 class="block-header__title">¿Requieres atención personalizada?</h1>
            </div>
        </div>
    </div>
    <div class="block">
        <div class="container container--max--lg">
            <div class="card contacts">
                <div class="contacts__map">
                    <iframe src='https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.7746308650835!2d-75.61597258590696!3d6.160930728982827!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e4683d1416ca7a9%3A0x1be78eb6e324212a!2zU2ltw7NuIEJvbMOtdmFyIChJdGFnw7xpKQ!5e0!3m2!1ses-419!2sco!4v1651263923151!5m2!1ses-419!2sco' frameborder='0' scrolling='no' marginheight='0' marginwidth='0'></iframe>
                </div>
                <div class="card-body card-body--padding--2">
                    <div class="row">
                        <div class="col-12 col-lg-12 pb-4 pb-lg-0">
                            <div class="mr-1">
                                <h4 class="contact-us__header card-title">Itagüí</h4>
                                <div class="contact-us__address">
                                    <p>
                                        Parqueadero Bodegas del Río<br>
                                        Calle 31 # 41 -15<br>
                                        310 411 40 48
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activación de formulario de Bitrix24 -->
                    <?php $this->load->view('bitrix/contacto'); ?>
                </div>
            </div>
        </div>
        <div class="block-space block-space--layout--divider-nl d-xl-block d-none"></div>

        <div class="container container--max--lg">
            <div class="card contacts">
                <div class="contacts__map">
                    <iframe src='https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d9430.280640079027!2d-75.44190447573469!3d6.401141006510524!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e443ba1a9104fbb%3A0x2fd6757871b1d819!2sMuelles%20y%20Frenos%20Simon%20Bolivar%20(Girardota)!5e0!3m2!1ses-419!2sco!4v1651264186993!5m2!1ses-419!2sco' frameborder='0' scrolling='no' marginheight='0' marginwidth='0'></iframe>
                </div>
                <div class="card-body card-body--padding--2">
                    <div class="row">
                        <div class="col-12 col-lg-12 pb-4 pb-lg-0">
                            <div class="mr-1">
                                <h4 class="contact-us__header card-title">Girardota</h4>
                                <div class="contact-us__address">
                                    <p>
                                        Doble Calzada Bello Hatillo, 200 metros antes del peaje El Trapiche, estación de servicio La Molienda<br>
                                        310 411 40 48
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activación de formulario de Bitrix24 -->
                    <?php $this->load->view('bitrix/contacto'); ?>
                </div>
            </div>
        </div>
        <div class="block-space block-space--layout--divider-nl d-xl-block d-none"></div>

        <div class="container container--max--lg">
            <div class="card contacts">
                <div class="contacts__map">
                    <iframe src='https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15866.673361106776!2d-75.36936253309253!3d6.175136976487411!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e469f8e0639dcc1%3A0xae98b2ba910ce228!2sMuelles%20y%20Frenos%20Simon%20Bolivar%20(Marinilla)!5e0!3m2!1ses-419!2sco!4v1651264137007!5m2!1ses-419!2sco' frameborder='0' scrolling='no' marginheight='0' marginwidth='0'></iframe>
                </div>
                <div class="card-body card-body--padding--2">
                    <div class="row">
                        <div class="col-12 col-lg-12 pb-4 pb-lg-0">
                            <div class="mr-1">
                                <h4 class="contact-us__header card-title">Marinillla</h4>
                                <div class="contact-us__address">
                                    <p>
                                        Autopista Medellín - Bogotá, km 39+000 - Servicentro Terpel<br>
                                        310 411 40 48
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activación de formulario de Bitrix24 -->
                    <?php $this->load->view('bitrix/contacto'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="block-space block-space--layout--before-footer"></div>
</div>