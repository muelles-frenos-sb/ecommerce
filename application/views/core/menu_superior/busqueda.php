<div class="header__search">
    <div class="search">
        <form class="search__body">
            <div class="search__shadow"></div>
            
            <!-- Búsqueda por palabra clave -->
            <input class="search__input" id="buscar" type="text" placeholder="Buscar por una palabra clave">

            <button class="search__button search__button--start" type="button" onClick="javascript:location.href='<?php echo site_url("contacto"); ?>'">
                <span class="search__button-icon">
                    <img src="<?php echo base_url(); ?>images/icons/camion.svg" width='25'>
                </span>
                <span class="search__button-title">Seleccione un vehículo</span>
            </button>

            <button class="search__button search__button--end" type="submit">
                <span class="search__button-icon">
                    <svg width="20" height="20">
                        <path d="M19.2,17.8c0,0-0.2,0.5-0.5,0.8c-0.4,0.4-0.9,0.6-0.9,0.6s-0.9,0.7-2.8-1.6c-1.1-1.4-2.2-2.8-3.1-3.9C10.9,14.5,9.5,15,8,15 c-3.9,0-7-3.1-7-7s3.1-7,7-7s7,3.1,7,7c0,1.5-0.5,2.9-1.3,4c1.1,0.8,2.5,2,4,3.1C20,16.8,19.2,17.8,19.2,17.8z M8,3C5.2,3,3,5.2,3,8 c0,2.8,2.2,5,5,5c2.8,0,5-2.2,5-5C13,5.2,10.8,3,8,3z" />
                    </svg>
                </span>
            </button>
            <div class="search__box"></div>
            <div class="search__decor">
                <div class="search__decor-start"></div>
                <div class="search__decor-end"></div>
            </div>
            <div class="search__dropdown search__dropdown--suggestions suggestions">
                <div class="suggestions__group">
                    <div class="suggestions__group-title">Productos</div>
                    <div class="suggestions__group-content">
                        <a class="suggestions__item suggestions__product" href="">
                            <div class="suggestions__product-image image image--type--product">
                                <div class="image__body">
                                    <img class="image__tag" src="<?php echo base_url(); ?>images/products/product-2-40x40.jpg" alt="">
                                </div>
                            </div>
                            <div class="suggestions__product-info">
                                <div class="suggestions__product-name">Producto 1</div>
                                <div class="suggestions__product-rating">
                                    <div class="suggestions__product-rating-stars">
                                        <div class="rating">
                                            <div class="rating__body">
                                                <div class="rating__star rating__star--active"></div>
                                                <div class="rating__star rating__star--active"></div>
                                                <div class="rating__star rating__star--active"></div>
                                                <div class="rating__star rating__star--active"></div>
                                                <div class="rating__star rating__star--active"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="suggestions__product-rating-label">5 on 22 reviews</div>
                                </div>
                            </div>
                            <div class="suggestions__product-price">$224.00</div>
                        </a>
                        <a class="suggestions__item suggestions__product" href="">
                            <div class="suggestions__product-image image image--type--product">
                                <div class="image__body">
                                    <img class="image__tag" src="<?php echo base_url(); ?>images/products/product-3-40x40.jpg" alt="">
                                </div>
                            </div>
                            <div class="suggestions__product-info">
                                <div class="suggestions__product-name">Producto 2</div>
                                <div class="suggestions__product-rating">
                                    <div class="suggestions__product-rating-stars">
                                        <div class="rating">
                                            <div class="rating__body">
                                                <div class="rating__star rating__star--active"></div>
                                                <div class="rating__star rating__star--active"></div>
                                                <div class="rating__star rating__star--active"></div>
                                                <div class="rating__star"></div>
                                                <div class="rating__star"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="suggestions__product-rating-label">3 on 14 reviews</div>
                                </div>
                            </div>
                            <div class="suggestions__product-price">$349.00</div>
                        </a>
                        <a class="suggestions__item suggestions__product" href="">
                            <div class="suggestions__product-image image image--type--product">
                                <div class="image__body">
                                    <img class="image__tag" src="<?php echo base_url(); ?>images/products/product-4-40x40.jpg" alt="">
                                </div>
                            </div>
                            <div class="suggestions__product-info">
                                <div class="suggestions__product-name">Producto 3</div>
                                <div class="suggestions__product-rating">
                                    <div class="suggestions__product-rating-stars">
                                        <div class="rating">
                                            <div class="rating__body">
                                                <div class="rating__star rating__star--active"></div>
                                                <div class="rating__star rating__star--active"></div>
                                                <div class="rating__star rating__star--active"></div>
                                                <div class="rating__star rating__star--active"></div>
                                                <div class="rating__star"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="suggestions__product-rating-label">4 on 26 reviews</div>
                                </div>
                            </div>
                            <div class="suggestions__product-price">$589.00</div>
                        </a>
                    </div>
                </div>
                <div class="suggestions__group">
                    <div class="suggestions__group-title">Categorías</div>
                    <div class="suggestions__group-content">
                        <a class="suggestions__item suggestions__category" href="">Categoría 1</a>
                        <a class="suggestions__item suggestions__category" href="">Categoría 2</a>
                        <a class="suggestions__item suggestions__category" href="">Categoría 3</a>
                        <a class="suggestions__item suggestions__category" href="">Categoría 4</a>
                    </div>
                </div>
            </div>
            <!-- <div class="search__dropdown search__dropdown--vehicle-picker vehicle-picker">
                <div class="search__dropdown-arrow"></div>
                <div class="vehicle-picker__panel vehicle-picker__panel--list vehicle-picker__panel--active" data-panel="list">
                    <div class="vehicle-picker__panel-body">
                        <div class="vehicle-picker__text">
                            Selecccione un vehículo para buscar los repuestos disponibles
                        </div>
                        <div class="vehicles-list">
                            <div class="vehicles-list__body">
                                <label class="vehicles-list__item">
                                    <span class="vehicles-list__item-radio input-radio">
                                        <span class="input-radio__body">
                                            <input class="input-radio__input" name="header-vehicle" type="radio">
                                            <span class="input-radio__circle"></span>
                                        </span>
                                    </span>
                                    <span class="vehicles-list__item-info">
                                        <span class="vehicles-list__item-name">2011 Ford Focus S</span>
                                        <span class="vehicles-list__item-details">Engine 2.0L 1742DA L4 FI Turbo</span>
                                    </span>
                                    <button type="button" class="vehicles-list__item-remove">
                                        <svg width="16" height="16">
                                            <path d="M2,4V2h3V1h6v1h3v2H2z M13,13c0,1.1-0.9,2-2,2H5c-1.1,0-2-0.9-2-2V5h10V13z" />
                                        </svg>
                                    </button>
                                </label>
                                <label class="vehicles-list__item">
                                    <span class="vehicles-list__item-radio input-radio">
                                        <span class="input-radio__body">
                                            <input class="input-radio__input" name="header-vehicle" type="radio">
                                            <span class="input-radio__circle"></span>
                                        </span>
                                    </span>
                                    <span class="vehicles-list__item-info">
                                        <span class="vehicles-list__item-name">2019 Audi Q7 Premium</span>
                                        <span class="vehicles-list__item-details">Engine 3.0L 5626CC L6 QK</span>
                                    </span>
                                    <button type="button" class="vehicles-list__item-remove">
                                        <svg width="16" height="16">
                                            <path d="M2,4V2h3V1h6v1h3v2H2z M13,13c0,1.1-0.9,2-2,2H5c-1.1,0-2-0.9-2-2V5h10V13z" />
                                        </svg>
                                    </button>
                                </label>
                            </div>
                        </div>
                        <div class="vehicle-picker__actions">
                            <button type="button" class="btn btn-primary btn-sm" data-to-panel="form">Add A Vehicle</button>
                        </div>
                    </div>
                </div>
                <div class="vehicle-picker__panel vehicle-picker__panel--form" data-panel="form">
                    <div class="vehicle-picker__panel-body">
                        <div class="vehicle-form vehicle-form--layout--search">
                            <div class="vehicle-form__item vehicle-form__item--select">
                                <select class="form-control form-control-select2" aria-label="Year">
                                    <option value="none">Modelo</option>
                                    <option>2010</option>
                                    <option>2011</option>
                                    <option>2012</option>
                                    <option>2013</option>
                                    <option>2014</option>
                                    <option>2015</option>
                                    <option>2016</option>
                                    <option>2017</option>
                                    <option>2018</option>
                                    <option>2019</option>
                                    <option>2020</option>
                                </select>
                            </div>
                            <div class="vehicle-form__item vehicle-form__item--select">
                                <select class="form-control form-control-select2" aria-label="Brand" disabled>
                                    <option value="none">Marca</option>
                                    <option>Audi</option>
                                    <option>BMW</option>
                                    <option>Ferrari</option>
                                    <option>Ford</option>
                                    <option>KIA</option>
                                    <option>Nissan</option>
                                    <option>Tesla</option>
                                    <option>Toyota</option>
                                </select>
                            </div>
                            <div class="vehicle-form__item vehicle-form__item--select">
                                <select class="form-control form-control-select2" aria-label="Model" disabled>
                                    <option value="none">Línea</option>
                                    <option>Explorer</option>
                                    <option>Focus S</option>
                                    <option>Fusion SE</option>
                                    <option>Mustang</option>
                                </select>
                            </div>
                            <div class="vehicle-form__item vehicle-form__item--select">
                                <select class="form-control form-control-select2" aria-label="Engine" disabled>
                                    <option value="none">Motor</option>
                                    <option>Gas 1.6L 125 hp AT/L4</option>
                                    <option>Diesel 2.5L 200 hp AT/L5</option>
                                    <option>Diesel 3.0L 250 hp MT/L5</option>
                                </select>
                            </div>
                            <div class="vehicle-form__divider">Or</div>
                            <div class="vehicle-form__item">
                                <input type="text" class="form-control" placeholder="Número VIN" aria-label="Número VIN">
                            </div>
                        </div>
                        <div class="vehicle-picker__actions">
                            <div class="search__car-selector-link">
                                <a href="" data-to-panel="list">Volver a la lista de vehículos</a>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" disabled>Añadir</button>
                        </div>
                    </div>
                </div>
            </div> -->
        </form>
    </div>
</div>

<script>
    $('form').submit(e => {
        e.preventDefault()

        localStorage.simonBolivar_buscarProducto = $('#buscar').val()

        location.href = '<?php echo site_url("productos?busqueda="); ?>' + $('#buscar').val()
    })
</script>