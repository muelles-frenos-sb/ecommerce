
<div class="container">
    <div class="block-header__body">
        <div class="row">
            <div class="post-header__body">
                <h1 class="post-header__title"><?php echo "FACTURAS DE {$datos['f200_razon_social']}"; ?></h1>
                <div class="post-header__meta">
                    <ul class="post-header__meta-list">
                        <li class="post-header__meta-item"><a href="#" class="post-header__meta-link"><?php echo $datos['f200_nit']; ?></a></li>
                        <li class="post-header__meta-item"><?php echo $datos['f015_email']; ?></li>
                        <li class="post-header__meta-item"><a href="<?php echo site_url('clientes'); ?>" class="post-header__meta-link">Consultar con otro número de documento</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>