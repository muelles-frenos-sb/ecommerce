<div class="header__navbar-departments">
    <div class="departments">
        <button class="departments__button" type="button">
            <span class="departments__button-icon"><svg width="16px" height="12px">
                    <path d="M0,7L0,5L16,5L16,7L0,7ZM0,0L16,0L16,2L0,2L0,0ZM12,12L0,12L0,10L12,10L12,12Z" />
                </svg>
            </span>
            <span class="departments__button-title">Categorías</span>
            <span class="departments__button-arrow"><svg width="9px" height="6px">
                    <path d="M0.2,0.4c0.4-0.4,1-0.5,1.4-0.1l2.9,3l2.9-3c0.4-0.4,1.1-0.4,1.4,0.1c0.3,0.4,0.3,0.9-0.1,1.3L4.5,6L0.3,1.6C-0.1,1.3-0.1,0.7,0.2,0.4z" />
                </svg>
            </span>
        </button>
        <div class="departments__menu">
            <div class="departments__arrow"></div>
            <div class="departments__body">
                <ul class="departments__list">
                    <li class="departments__list-padding" role="presentation"></li>
                    <!-- Marcas -->
                    <?php foreach ($this->configuracion_model->obtener('marcas') as $marca) { ?>
                        <li class="departments__item departments__item--submenu--megamenu departments__item--has-submenu">
                            <a href="<?php echo site_url("productos?marca=$marca->nombre"); ?>" class="departments__item-link" id="filtro_marca_<?php echo $marca->id; ?>" data-marca-id="<?php echo $marca->id; ?>">
                                <?php echo $marca->nombre; ?>
                                <span class="departments__item-arrow">
                                    <svg width="7" height="11">
                                        <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9C-0.1,9.8-0.1,10.4,0.3,10.7z" />
                                    </svg>
                                </span>
                            </a>

                            <div class="departments__item-menu">
                                <div class="megamenu departments__megamenu departments__megamenu--size--md">
                                    <div class="megamenu__image">
                                        <img src="<?php echo base_url(); ?>images/departments/departments-4.jpg" alt="">
                                    </div>
                                    <div class="row">
                                        <!-- Grupos -->
                                        <div class="col-4">
                                            <ul class="megamenu__links megamenu-links megamenu-links--root">
                                                <li class="megamenu-links__item megamenu-links__item--has-submenu">
                                                    <a class="megamenu-links__item-link" href="">Grupos</a>
                                                    <ul class="megamenu-links" id="filtros_grupos_<?php echo $marca->id; ?>"></ul>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Líneas -->
                                        <div class="col-4">
                                            <ul class="megamenu__links megamenu-links megamenu-links--root">
                                                <li class="megamenu-links__item megamenu-links__item--has-submenu">
                                                    <a class="megamenu-links__item-link" href="">Líneas</a>
                                                    <ul class="megamenu-links" id="filtros_lineas_<?php echo $marca->id; ?>">
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                    <li class="departments__list-padding" role="presentation"></li>
                </ul>
                <div class="departments__menu-container"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $().ready(() => {
        $("[id^='filtro_marca']").mouseover((evento) => {
            let marcaId = $(evento.currentTarget).data("marca-id")
            cargarFiltros("grupos", `filtros_grupos_${marcaId}`, {marca_id: marcaId})
            cargarFiltros("lineas", `filtros_lineas_${marcaId}`, {marca_id: marcaId})
        })
    })
</script>