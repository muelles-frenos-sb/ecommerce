<div class="col-4">
    <div class="block-products-columns__title"><?php echo $titulo; ?></div>
    <div class="block-products-columns__list">
        <?php
        for ($i=$desde; $i < $hasta; $i++) {
            $posicion = array_keys($productos);
            $item = $productos[$posicion[$i]];
            $producto = $this->productos_model->obtener('productos', ['id' => $item->producto_id]);

            if(isset($producto)) {
            ?>
                <div class="block-products-columns__list-item">
                    <div class="product-card">
                        <!-- <div class="product-card__actions-list">
                            <button class="product-card__action product-card__action--quickview" type="button" aria-label="Quick view">
                                <svg width="16" height="16">
                                    <path d="M14,15h-4v-2h3v-3h2v4C15,14.6,14.6,15,14,15z M13,3h-3V1h4c0.6,0,1,0.4,1,1v4h-2V3z M6,3H3v3H1V2c0-0.6,0.4-1,1-1h4V3z M3,13h3v2H2c-0.6,0-1-0.4-1-1v-4h2V13z" />
                                </svg>
                            </button>
                        </div> -->
                        <div class="product-card__image">
                            <div class="image image--type--product">
                                <a href="<?php echo site_url("productos/ver/$producto->id"); ?>" class="image__body">
                                    <img class="image__tag" src="<?php echo url_fotos($producto->marca, $producto->referencia); ?>">
                                </a>
                            </div>
                        </div>
                        <div class="product-card__info">
                            <div class="product-card__name">
                                <div>
                                    <!-- <div class="product-card__badges">
                                        <div class="tag-badge tag-badge--sale">sale</div>
                                        <div class="tag-badge tag-badge--new">new</div>
                                        <div class="tag-badge tag-badge--hot">hot</div>
                                    </div> -->
                                    <a href="<?php echo site_url("productos/ver/$producto->id"); ?>"><?php echo $producto->notas; ?></a>
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
                        </div>
                        <div class="product-card__footer">
                            <div class="product-card__prices">
                                <div class="product-card__price product-card__price--current">
                                    <?php echo formato_precio($producto->precio); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>