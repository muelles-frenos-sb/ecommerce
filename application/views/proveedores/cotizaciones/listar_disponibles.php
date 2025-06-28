<!-- Se obtienen todas las solicitudes de cotización disponibles -->
<?php $solicitudes_disponibles = $this->proveedores_model->obtener('solicitudes_disponibles'); ?>

<div class="block">
    <div class="container">
        <div class="card flex-grow-1 mt-4">
            <div class="card-body card-body--padding--2">
                <?php foreach ($solicitudes_disponibles as $solicitud) { ?>
                    <div class="vehicles-list__body">
                        <div class="vehicles-list__item">
                            <div class="vehicles-list__item-info">
                                <div class="vehicles-list__item-name"><h3><?php echo "Solicitud de precio #$solicitud->id"; ?></h3></div>
                                <div class="vehicles-list__item-details"><h5><?php echo "Disponible hasta $solicitud->fecha_fin"; ?></h5></div>
                                <a class="btn btn-success btn-md" href="<?php echo site_url("proveedores/cotizacion/$solicitud->id/{$datos['numero_documento']}") ?>">Quiero enviar mi cotización</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>