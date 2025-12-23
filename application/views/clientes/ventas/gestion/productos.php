<style>
    #tabla_productos tbody td {
        font-size: 0.8em;
        padding: 4px;
    }
</style>

<?php $productos = json_decode($datos); ?>

<div class="table-responsive">
    <table class="table-striped table-bordered" id="tabla_productos" width="100%">
        <thead>
            <tr>
                <th class="text-center">Id</th>
                <th class="text-center">Producto</th>
                <th class="text-center">Bodega</th>
                <th class="text-center">Stock</th>
                <th class="text-center">Lista</th>
                <th class="text-center">Precio unitario</th>
                <th class="text-center">Cantidad</th>
                <th class="text-center">Subtotal</th>
                <th class="text-center">Seleccionar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($productos->resultado as $producto) { ?>
                <tr>
                    <td class="text-center" width="5%"><?php echo $producto->id; ?></td>
                    <td><?php echo $producto->notas; ?></td>
                    <td class="text-center" width="5%">
                        <!-- Se cargan las bodegas donde el producto tiene disponibilidad -->
                        <select id="xxx" class="form-control">
                            <option value=""></option>
                        </select>
                    </td>
                    <td class="text-center" width="5%"><?php echo $producto->disponible ?? '-'; ?></td>
                    <td class="text-center" width="5%">
                        <!-- Se cargan las listas donde el producto tiene disponibilidad -->
                        <select id="xxx" class="form-control">
                            <option value=""></option>
                        </select>
                    </td>
                    <td class="text-center" width="10%"><?php echo ($producto->precio) ? formato_precio($producto->precio) : '-' ; ?></td>
                    <td class="text-center" width="5%">
                        <input type="number" value="1">
                    </td>
                    <td class="text-center" width="10%"><?php echo ($producto->precio) ? formato_precio($producto->precio) : '-' ; ?></td>
                    <td class="text-center" width="5%">
                        <button type="button" onClick="javascript:seleccionarCliente('<?php // echo $producto['f200_nit']; ?>')" class="btn btn-success">+</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>