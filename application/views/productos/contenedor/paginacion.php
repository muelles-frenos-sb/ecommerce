<?php print_r($datos); ?>

<div class="products-view__pagination">
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <!-- Anterior -->
            <li class="page-item disabled">
                <a class="page-link page-link--with-arrow" href="" aria-label="Previous">
                    <span class="page-link__arrow page-link__arrow--left" aria-hidden="true"><svg width="7" height="11">
                            <path d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z" />
                        </svg>
                    </span>
                </a>
            </li>
            
            <?php for ($pagina = 1; $pagina <= $datos['cantidad_paginas']; $pagina++) { ?>
                <?php if($pagina != $datos['pagina_actual']) { ?>
                    <li class="page-item"><a class="page-link" href="<?php echo site_url("productos?pagina=$pagina"); ?>"><?php echo $pagina; ?></a></li>
                <?php } else { ?>
                    <li class="page-item active" aria-current="page">
                        <span class="page-link">
                            <?php echo $pagina; ?>
                            <span class="sr-only">(actual)</span>
                        </span>
                    </li>
                <?php } ?>
            <?php } ?>
            
            <!-- <li class="page-item page-item--dots">
                <div class="pagination__dots"></div>
            </li> -->
            
            <!-- Sigiente -->
            <li class="page-item">
                <a class="page-link page-link--with-arrow" href="" aria-label="Next">
                    <span class="page-link__arrow page-link__arrow--right" aria-hidden="true"><svg width="7" height="11">
                            <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9 C-0.1,9.8-0.1,10.4,0.3,10.7z" />
                        </svg>
                    </span>
                </a>
            </li>
        </ul>
    </nav>
    <div class="products-view__pagination-legend">
        <?php echo "Mostrando {$datos['items_por_pagina']} de {$datos['cantidad_items']} elementos"; ?>
    </div>
</div>