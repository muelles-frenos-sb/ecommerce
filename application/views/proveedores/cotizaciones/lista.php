<?php $cotizacion_detalle = $this->proveedores_model->obtener('proveedores_cotizaciones_detalle', ['cotizacion_id' => $datos['cotizacion_id']]); ?>

<div class="table-responsive">
    <table class="table-striped table-bordered w-100">
        <thead>
            <tr>
                <th class="text-center">Producto</th>
                <th class="text-center">Proveedor</th>
                <th class="text-center w-25">Precio</th>
                <th class="text-center">Observación</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($cotizacion_detalle as $registro) {
                echo "
                <tr>
                    <td>$registro->producto</td>
                    <td>$registro->f200_razon_social</td>
                    <td class='text-right w-25'>$ ".number_format($registro->precio, 0, ',', '.')."</td>
                    <td>$registro->observacion</td>
                </tr>
                ";
            }
            ?>
        </tbody>
    </table>
</div>