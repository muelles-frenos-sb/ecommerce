<div class="block block-slideshow">
        <div class="block-slideshow__carousel">
            <div class="owl-carousel">
                <a class="block-slideshow__item" href="<?php echo site_url('productos'); ?>">
                    <span class="block-slideshow__item-image block-slideshow__item-image--desktop" style="background-image: url('images/slides/slide-3.jpg')"></span>
                    <span class="block-slideshow__item-image block-slideshow__item-image--mobile" style="background-image: url('images/slides/slide-3-mobile.jpg')"></span>
                    <span class="block-slideshow__item-offer">
                        30% de descuento
                    </span>
                    <span class="block-slideshow__item-title">
                        Cuando adquieres respuestos <br>
                        con instalación
                    </span>
                    <span class="block-slideshow__item-details">
                        Instalación de repuestos<br>
                        de nuestros aliados
                    </span>
                    <span class="block-slideshow__item-button">
                        Comprar ahora
                    </span>
                </a>
                <a class="block-slideshow__item" href="<?php echo site_url('productos'); ?>">
                    <span class="block-slideshow__item-image block-slideshow__item-image--desktop" style="background-image: url('images/slides/slide-2.jpg')"></span>
                    <span class="block-slideshow__item-image block-slideshow__item-image--mobile" style="background-image: url('images/slides/slide-2-mobile.jpg')"></span>
                    <span class="block-slideshow__item-title">
                        ¿No encuentras<br>
                        lo que necesitas?
                    </span>
                    <span class="block-slideshow__item-details">
                        Tenemos todo lo que necesita: repuestos,<br>
                        Piezas de rendimiento accesorios, aceites,<br>
                        herramientas y mucho más
                    </span>
                    <span class="block-slideshow__item-button">
                        Comprar
                    </span>
                </a>
                <a class="block-slideshow__item" href="<?php echo site_url('productos'); ?>">
                    <span class="block-slideshow__item-image block-slideshow__item-image--desktop" style="background-image: url('images/slides/slide-1.jpg')"></span>
                    <span class="block-slideshow__item-image block-slideshow__item-image--mobile" style="background-image: url('images/slides/slide-1-mobile.jpg')"></span>
                    <span class="block-slideshow__item-offer">
                        30% de descuento
                    </span>
                    <span class="block-slideshow__item-title">
                        Gran variedad de<br>
                        neumáticos para llantas
                    </span>
                    <span class="block-slideshow__item-details">
                        Cualquier tamaño y diámetro,<br>
                        marca y rendimiento
                    </span>
                    <span class="block-slideshow__item-button">
                        Comprar
                    </span>
                </a>
            </div>
    </div>
</div>

<!-- <div class="block-space block-space--layout--divider-xs"></div> -->

<div class="block-finder block">
    <div class="decor block-finder__decor decor--type--bottom">
        <div class="decor__body">
            <div class="decor__start"></div>
            <div class="decor__end"></div>
            <div class="decor__center"></div>
        </div>
    </div>
    <div class="block-finder__image" style="background-image: url('<?php echo base_url(); ?>images/finder-1903x500.jpg');"></div>
    <div class="block-finder__body container container--max--xl">
        <div class="block-finder__title">Busque un repuesto para su vehículo</div>
        <div class="block-finder__subtitle">Over hundreds of brands and tens of thousands of parts</div>
        <form class="block-finder__form">
            <div class="block-finder__form-control block-finder__form-control--select">
                <select name="year" aria-label="Vehicle Year">
                    <option value="none">Select Year</option>
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
            <div class="block-finder__form-control block-finder__form-control--select">
                <select name="make" aria-label="Vehicle Make" disabled>
                    <option value="none">Select Make</option>
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
            <div class="block-finder__form-control block-finder__form-control--select">
                <select name="model" aria-label="Vehicle Model" disabled>
                    <option value="none">Select Model</option>
                    <option>Explorer</option>
                    <option>Focus S</option>
                    <option>Fusion SE</option>
                    <option>Mustang</option>
                </select>
            </div>
            <div class="block-finder__form-control block-finder__form-control--select">
                <select name="engine" aria-label="Vehicle Engine" disabled>
                    <option value="none">Select Engine</option>
                    <option>Gas 1.6L 125 hp AT/L4</option>
                    <option>Diesel 2.5L 200 hp AT/L5</option>
                    <option>Diesel 3.0L 250 hp MT/L5</option>
                </select>
            </div>
            <button class="block-finder__form-control block-finder__form-control--button" type="submit">Search</button>
        </form>
    </div>
</div>