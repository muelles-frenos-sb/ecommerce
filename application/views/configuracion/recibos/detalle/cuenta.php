<?php $recibo = $this->productos_model->obtener('recibo', ['id' => $datos['id_recibo']]); ?>

<label class="vehicles-list__item">
    <div class="form-group col-4">
        <label for="cuenta_<?php echo $datos['id']; ?>">Cuenta</label>
        <select id="cuenta_<?php echo $datos['id']; ?>" class="form-control">
            <option value="">Seleccione...</option>
            <?php foreach($this->configuracion_model->obtener('cuentas_bancarias') as $cuenta) echo "<option value='$cuenta->id' data-codigo='$cuenta->codigo'>$cuenta->codigo - $cuenta->nombre</option>"; ?>
        </select>
    </div>

    <div class="form-group col-4">
        <label for="fecha_pago_<?php echo $datos['id']; ?>">Fecha de documento banco</label>
        <input type="date" class="form-control" id="fecha_pago_<?php echo $datos['id']; ?>">
    </div>
    
    <div class="form-group col-4">
        <label for="fecha_pago">Valor</label>
        <input 
            type="number"
            id="valor_<?php echo $datos['id']; ?>"
            class="form-control valor_cuenta_recibo"
            style="text-align: right"
            value="0"
            data-id="<?php echo $datos['id']; ?>"
            onChange="javascript:calcularTotal()"
        >
    </div>
</label>