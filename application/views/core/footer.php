<footer class="site__footer">
    <div class="site-footer">
        <div class="decor site-footer__decor decor--type--bottom">
            <div class="decor__body">
                <div class="decor__start"></div>
                <div class="decor__end"></div>
                <div class="decor__center"></div>
            </div>
        </div>
        <div class="site-footer__widgets">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-xl-4">
                        <div class="site-footer__widget footer-contacts">
                            <h5 class="footer-contacts__title">Contáctanos</h5>
                            <address class="footer-contacts__contacts">
                                <dl>
                                    <dt>Número telefónico</dt>
                                    <dd><?php echo $this->config->item('telefono'); ?></dd>
                                </dl>
                                <dl>
                                    <dt>Correo electrónico</dt>
                                    <dd><?php echo $this->config->item('email'); ?></dd>
                                </dl>
                                <dl>
                                    <dt>Nuestra ubicación</dt>
                                    <dd>
                                        <a href="<?php echo $this->config->item('url_facebook'); ?>" target="_blank">
                                            <?php echo $this->config->item('direccion'); ?>
                                        </a>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>Horarios de atención</dt>
                                    <dd><?php echo $this->config->item('horario_semana'); ?></dd>
                                    <dd><?php echo $this->config->item('horario_fines_semana'); ?></dd>
                                </dl>
                            </address>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-xl-2">
                        <div class="site-footer__widget footer-links">
                            <h5 class="footer-links__title">Información</h5>
                            <ul class="footer-links__list">
                                <li class="footer-links__item"><a href="<?php echo site_url('nosotros'); ?>" class="footer-links__link">Acerca de nosotros</a></li>
                                <li class="footer-links__item"><a href="<?php echo site_url('contacto'); ?>" class="footer-links__link">Contacto</a></li>
                                <li class="footer-links__item"><a href="" class="footer-links__link">Política de tratamiento de datos</a></li>
                                <!-- <li class="footer-links__item"><a href="" class="footer-links__link">Brands</a></li>
                                <li class="footer-links__item"><a href="" class="footer-links__link">Contact Us</a></li>
                                <li class="footer-links__item"><a href="" class="footer-links__link">Returns</a></li>
                                <li class="footer-links__item"><a href="" class="footer-links__link">Site Map</a></li> -->
                            </ul>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-xl-2">
                        <div class="site-footer__widget footer-links">
                            <h5 class="footer-links__title">Mi cuenta</h5>
                            <ul class="footer-links__list">
                                <li class="footer-links__item"><a href="" class="footer-links__link">Store Location</a></li>
                                <li class="footer-links__item"><a href="" class="footer-links__link">Order History</a></li>
                                <li class="footer-links__item"><a href="" class="footer-links__link">Wish List</a></li>
                                <li class="footer-links__item"><a href="" class="footer-links__link">Newsletter</a></li>
                                <li class="footer-links__item"><a href="" class="footer-links__link">Specials</a></li>
                                <li class="footer-links__item"><a href="" class="footer-links__link">Gift Certificates</a></li>
                                <li class="footer-links__item"><a href="" class="footer-links__link">Affiliate</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="site-footer__widget footer-newsletter">
                            <h5 class="footer-contacts__title">Acerca de nosotros</h5>
                            <div class="footer-contacts__text">
                                Somos una empresa de repuestos para vehículos de carga pesada.
                                <hr>

                                Ofrecemos productos de la más alta calidad y un servicio excepcional a nuestros clientes en el Valle de Aburrá y Colombia. Con más de 20 años de experiencia en el mercado, nos hemos ganado la confianza de nuestros clientes gracias a nuestra pasión por lo que hacemos y nuestra dedicación a la excelencia.
                                <hr>

                                Somos importadores de marcas reconocidas como SKF, Mansons, KTC y Randon, siempre nos esforzamos por ofrecer lo mejor a nuestros clientes.
                                <hr>

                                Somos una empresa líder en el mercado de repuestos con más de 20 años de experiencia. Contamos con excelente respaldo de nuestros proveedores (Cummins, Hércules, Coexito, KTC, Timken, Koyo, Grupo A) entre otros.
                            </div><br>

                            <!-- <h5 class="footer-newsletter__title">Newsletter</h5>
                            <div class="footer-newsletter__text">
                                Enter your email address below to subscribe to our newsletter and keep up to date with discounts and special offers.
                            </div>
                            <form action="" class="footer-newsletter__form">
                                <label class="sr-only" for="footer-newsletter-address">Email Address</label>
                                <input type="text" class="footer-newsletter__form-input" id="footer-newsletter-address" placeholder="Email Address...">
                                <button class="footer-newsletter__form-button">Subscribe</button>
                            </form> -->
                            <!-- <div class="footer-newsletter__text footer-newsletter__text--social"></div> -->
                            <h5 class="footer-contacts__title">Síguenos en nuestras redes sociales</h5>
                            <div class="footer-newsletter__social-links social-links">
                                <ul class="social-links__list">
                                    <li class="social-links__item social-links__item--facebook"><a href="https://www.facebook.com/RepuestosSimonBolivar" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                    <li class="social-links__item social-links__item--twitter"><a href="https://twitter.com/myfsimonbolivar" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                    <li class="social-links__item social-links__item--youtube"><a href="https://www.youtube.com/@repuestossimonbolivar2617" target="_blank"><i class="fab fa-youtube"></i></a></li>
                                    <li class="social-links__item social-links__item--instagram"><a href="https://www.instagram.com/repuestossimonbolivar/" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                    <li class="social-links__item social-links__item--linkedin"><a href="https://www.linkedin.com/company/sim%C3%B3n-bol%C3%ADvar" target="_blank"><i class="fab fa-linkedin"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="site-footer__bottom">
            <div class="container">
                <div class="site-footer__bottom-row">
                    <div class="site-footer__copyright">
                        Un desarrollo de <a href="https://johnarleycano.com" target="_blank">John Arley Cano</a> - <?php echo "Versión ".exec('git describe --tags'); ?>
                    </div>
                    <div class="site-footer__payments">
                        <img src="<?php echo base_url(); ?>images/payments.png" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>