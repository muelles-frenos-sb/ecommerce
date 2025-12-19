<div class="table-responsive">
    <table class="table-striped table-bordered" id="tabla_facturas" width="100%">
        <thead>
            <tr>
                <th class="text-center">Id</th>
                <th class="text-center">Producto</th>
                <th class="text-center">Razon social</th>
                <th class="text-center">Seleccionar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($datos['resultado'] as $producto) { ?>
                <tr>
                    <td class="text-center"><?php echo $producto['id']; ?></td>
                    <td width="50%"><?php echo $producto['notas']; ?></td>
                    <td><?php // echo $producto['f200_razon_social']; ?></td>
                    <td class="text-center">
                        <button type="button" onClick="javascript:seleccionarCliente('<?php // echo $producto['f200_nit']; ?>')" class="btn btn-primary">+</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>