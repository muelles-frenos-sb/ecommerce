<?php
$productos = $this->productos_model->obtener('productos', $datos);
// print_r($datos);

if(empty($productos)) echo 'No se encontraron resultados con los filtros seleccionados';

foreach($productos as $producto) {
?>
    <div class="products-list__item">
        <div class="product-card">
            <!-- Opciones de cuadrícula -->
            <div class="product-card__actions-list">
                <button class="product-card__action product-card__action--quickview" type="button" aria-label="Vista rápida">
                    <svg width="16" height="16">
                        <path d="M14,15h-4v-2h3v-3h2v4C15,14.6,14.6,15,14,15z M13,3h-3V1h4c0.6,0,1,0.4,1,1v4h-2V3z M6,3H3v3H1V2c0-0.6,0.4-1,1-1h4V3z M3,13h3v2H2c-0.6,0-1-0.4-1-1v-4h2V13z" />
                    </svg>
                </button>
                <button class="product-card__action product-card__action--wishlist" type="button" aria-label="Agregar a favoritos">
                    <svg width="16" height="16">
                        <path d="M13.9,8.4l-5.4,5.4c-0.3,0.3-0.7,0.3-1,0L2.1,8.4c-1.5-1.5-1.5-3.8,0-5.3C2.8,2.4,3.8,2,4.8,2s1.9,0.4,2.6,1.1L8,3.7 l0.6-0.6C9.3,2.4,10.3,2,11.3,2c1,0,1.9,0.4,2.6,1.1C15.4,4.6,15.4,6.9,13.9,8.4z" />
                    </svg>
                </button>
                <button class="product-card__action product-card__action--compare" type="button" aria-label="Añadir para comparar">
                    <svg width="16" height="16">
                        <path d="M9,15H7c-0.6,0-1-0.4-1-1V2c0-0.6,0.4-1,1-1h2c0.6,0,1,0.4,1,1v12C10,14.6,9.6,15,9,15z" />
                        <path d="M1,9h2c0.6,0,1,0.4,1,1v4c0,0.6-0.4,1-1,1H1c-0.6,0-1-0.4-1-1v-4C0,9.4,0.4,9,1,9z" />
                        <path d="M15,5h-2c-0.6,0-1,0.4-1,1v8c0,0.6,0.4,1,1,1h2c0.6,0,1-0.4,1-1V6C16,5.4,15.6,5,15,5z" />
                    </svg>
                </button>
            </div>

            <!-- Detalle del producto -->
            <div class="product-card__image">
                <div class="image image--type--product">
                    <a href="<?php echo site_url("productos/ver/$producto->id"); ?>" class="image__body">
                        <img class="image__tag" src="<?php echo $this->config->item('url_fotos').trim($producto->marca).'/'.$producto->referencia.'.jpg'; ?>">
                    </a>
                </div>
                <div class="status-badge status-badge--style--<?php echo ($producto->disponible > 0) ? "success" : "failure"; ?> product-card__fit status-badge--has-icon status-badge--has-text">
                    <div class="status-badge__body">
                        
                            <div class="status-badge__icon">
                                <svg width="13" height="13">
                                    <?php if ($producto->disponible > 0) { ?>
                                        <path d="M12,4.4L5.5,11L1,6.5l1.4-1.4l3.1,3.1L10.6,3L12,4.4z" />
                                    <?php } else { ?>
                                        <path d="M6.5,0C2.9,0,0,2.9,0,6.5S2.9,13,6.5,13S13,10.1,13,6.5S10.1,0,6.5,0z M6.5,2c0.9,0,1.7,0.3,2.4,0.7L2.7,8.9 C2.3,8.2,2,7.4,2,6.5C2,4,4,2,6.5,2z M6.5,11c-0.9,0-1.7-0.3-2.4-0.7l6.2-6.2C10.7,4.8,11,5.6,11,6.5C11,9,9,11,6.5,11z" />
                                    <?php } ?>
                                </svg>
                            </div>
                        
                        <div class="status-badge__text"><?php echo ($producto->disponible > 0) ? "$producto->disponible unidades disponibles" : "Agotado"; ?></div>
                        <div class="status-badge__tooltip" tabindex="0" data-toggle="tooltip" title="Part&#x20;Fit&#x20;for&#x20;2011&#x20;Ford&#x20;Focus&#x20;S"></div>
                    </div>
                </div>
            </div>

            <div class="product-card__info">
                <div class="product-card__meta"><span class="product-card__meta-title">Referencia:</span> <?php echo $producto->referencia; ?></div>
                <div class="product-card__name">
                    <div>
                        <div class="product-card__badges">
                            <div class="tag-badge tag-badge--sale">sale</div>
                            <div class="tag-badge tag-badge--new">new</div>
                            <div class="tag-badge tag-badge--hot">hot</div>
                        </div>
                        <a href="<?php echo site_url("productos/ver/$producto->id"); ?>"><?php echo substr($producto->notas, 0, 50); ?></a>
                    </div>
                </div>
                <!-- <div class="product-card__rating">
                    <div class="rating product-card__rating-stars">
                        <div class="rating__body">
                            <div class="rating__star rating__star--active"></div>
                            <div class="rating__star rating__star--active"></div>
                            <div class="rating__star rating__star--active"></div>
                            <div class="rating__star rating__star--active"></div>
                            <div class="rating__star"></div>
                        </div>
                    </div>
                    <div class="product-card__rating-label">4 on 3 reviews</div>
                </div> -->
                <div class="product-card__features">
                    <ul>
                        <li>Marca: <?php echo $producto->marca; ?></li>
                        <li>Grupo: <?php echo $producto->grupo; ?></li>
                        <li>Línea: <?php echo $producto->linea; ?></li>
                        <li><?php echo $producto->notas; ?></li>
                    </ul>
                </div>
            </div>
            <div class="product-card__footer">
                <?php if ($producto->disponible > 0) { ?>
                    <div class="product-card__prices">
                        <div class="product-card__price product-card__price--current">
                            <?php echo '$ '.number_format($producto->precio, 0, ',', '.'); ?>
                        </div>
                    </div>
                
                    <button class="product-card__addtocart-icon" type="button" aria-label="Agregar al carrito">
                        <svg width="20" height="20">
                            <circle cx="7" cy="17" r="2" />
                            <circle cx="15" cy="17" r="2" />
                            <path d="M20,4.4V5l-1.8,6.3c-0.1,0.4-0.5,0.7-1,0.7H6.7c-0.4,0-0.8-0.3-1-0.7L3.3,3.9C3.1,3.3,2.6,3,2.1,3H0.4C0.2,3,0,2.8,0,2.6 V1.4C0,1.2,0.2,1,0.4,1h2.5c1,0,1.8,0.6,2.1,1.6L5.1,3l2.3,6.8c0,0.1,0.2,0.2,0.3,0.2h8.6c0.1,0,0.3-0.1,0.3-0.2l1.3-4.4 C17.9,5.2,17.7,5,17.5,5H9.4C9.2,5,9,4.8,9,4.6V3.4C9,3.2,9.2,3,9.4,3h9.2C19.4,3,20,3.6,20,4.4z" />
                        </svg>
                    </button>
                
                    <button class="product-card__addtocart-full" type="button">
                        Agregar al carrito
                    </button>
                <?php } ?>
                <!-- <button class="product-card__wishlist" type="button">
                    <svg width="16" height="16">
                        <path d="M13.9,8.4l-5.4,5.4c-0.3,0.3-0.7,0.3-1,0L2.1,8.4c-1.5-1.5-1.5-3.8,0-5.3C2.8,2.4,3.8,2,4.8,2s1.9,0.4,2.6,1.1L8,3.7 l0.6-0.6C9.3,2.4,10.3,2,11.3,2c1,0,1.9,0.4,2.6,1.1C15.4,4.6,15.4,6.9,13.9,8.4z" />
                    </svg>
                    <span>Add to wishlist</span>
                </button>
                <button class="product-card__compare" type="button">
                    <svg width="16" height="16">
                        <path d="M9,15H7c-0.6,0-1-0.4-1-1V2c0-0.6,0.4-1,1-1h2c0.6,0,1,0.4,1,1v12C10,14.6,9.6,15,9,15z" />
                        <path d="M1,9h2c0.6,0,1,0.4,1,1v4c0,0.6-0.4,1-1,1H1c-0.6,0-1-0.4-1-1v-4C0,9.4,0.4,9,1,9z" />
                        <path d="M15,5h-2c-0.6,0-1,0.4-1,1v8c0,0.6,0.4,1,1,1h2c0.6,0,1-0.4,1-1V6C16,5.4,15.6,5,15,5z" />
                    </svg>
                    <span>Add to compare</span>
                </button> -->
            </div>
        </div>
    </div>
<?php } ?>

<script>
	$().ready(() => {
		let totalRegistros = parseInt("<?php echo count($productos); ?>")

		// Si no hay más datos o son menos del total configurado, se oculta el botón
		if(totalRegistros == 0 || totalRegistros < parseInt($('#cantidad_datos').val())) $("#btn_mostrar_mas").hide()
	})
</script>