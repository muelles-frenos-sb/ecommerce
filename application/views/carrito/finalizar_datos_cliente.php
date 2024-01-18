<?php
// Consulta de las sucursales del cliente
$resultado_sucursales = json_decode(obtener_clientes_api($datos));
$codigo_resultado_sucursales = $resultado_sucursales->codigo;
$sucursales = ($codigo_resultado_sucursales == 0) ? $resultado_sucursales->detalle->Table : [] ;

// Campo oculto para almacenar la cantidad de sucursales del cliente
$cantidad_sucursales = count($sucursales);
echo "<input type='hidden' value='$cantidad_sucursales' id='cantidad_sucursales'>";

// Consulta de terceros
$resultado_tercero = json_decode(obtener_terceros_api($datos));
$codigo_resultado_tercero = $resultado_tercero->codigo;
$tercero = ($codigo_resultado_tercero == 0) ? $resultado_tercero->detalle->Table[0] : [] ;
?>
<input type="hidden" id="total_pedido">

<div class="form-row">
    <div class="form-group col-md-12">
        <label for="checkout_nombres">Nombres</label>
        <input type="text" class="form-control" id="checkout_nombres" value="<?php if(!empty($tercero)) echo $tercero->f200_nombres; ?>">
    </div>
    <div class="form-group col-md-6">
        <label for="checkout_primer_apellido">Primer apellido</label>
        <input type="text" class="form-control" id="checkout_primer_apellido" value="<?php if(!empty($tercero)) echo $tercero->f200_apellido1; ?>">
    </div>
    <div class="form-group col-md-6">
        <label for="checkout_segundo_apellido">Segundo apellido</label>
        <input type="text" class="form-control" id="checkout_segundo_apellido" value="<?php if(!empty($tercero)) echo $tercero->f200_apellido2; ?>">
    </div>
</div>
<div class="form-group">
    <label for="checkout_razon_social">Razón social *<span class="text-muted">(Opcional)</span></label>
    <input type="text" class="form-control" id="checkout_razon_social" value="<?php if(!empty($tercero)) echo $tercero->f200_razon_social; ?>" placeholder="Nombre de la empresa o nombre completo">
</div>
<div class="form-group">
    <label for="checkout_direccion">Dirección *</label>
    <input type="text" class="form-control" id="checkout_direccion" value="<?php // if(!empty($tercero)) echo $tercero->direccion1; ?>">
</div>
<div class="form-group">
    <label for="checkout_sucursal">Elija la sucursal o placa a la que desea asociar el pedido</label>
    <select id="checkout_sucursal" class="form-control form-control-select2">
        <option value="">Seleccione...</option>
        <?php foreach($sucursales as $sucursal) echo "<option value='$sucursal->f201_id_sucursal'>$sucursal->f201_descripcion_sucursal</option>"; ?>
    </select>
</div>
<div class="form-row">
    <div class="form-group col-md-6">
        <label for="checkout_email">Correo electrónico *</label>
        <input type="email" class="form-control" id="checkout_email" value="<?php // if(!empty($tercero)) echo $tercero->email; ?>">
    </div>
    <div class="form-group col-md-6">
        <label for="checkout_telefono">Teléfono *</label>
        <input type="text" class="form-control" id="checkout_telefono" value="<?php // if(!empty($tercero)) echo $tercero->telefono; ?>">
    </div>
</div>

<?php if(isset($tercero)) { ?>
    <script>
        $().ready(() => {
            $('#btn_validar_documento').removeClass('btn-loading').hide()
            $('#checkout_documento_numero').attr('disabled', true)
            $('#btn_pagar').attr('disabled', false)
        })
    </script>
<?php } ?>

<script>
    $().ready(() => {
        // Cuando se elija una sucursal
        $('#checkout_sucursal').change(async() => {
            let subtotal = <?php echo $this->cart->total(); ?>
            
            // Se toma la lista de precio porque ya se confirma que es cliente y tiene sucursal
            let listaPrecio = '<?php echo $this->config->item('lista_precio_clientes'); ?>' // 010
            let descuento = await consulta('obtener', {tipo: 'valores_detalle', lista_precio: listaPrecio}, false)
            let total = subtotal - descuento

            $('#descuento').html(`
                <th>Decuento</th>
                <td>$ ${formatearNumero(descuento)}</td>
            `)

            $('.checkout__totals-footer').html(`
                <tr>
                    <th>Total</th>
                    <td>$${formatearNumero(total)}</td>
                </tr>
            `)

            $('#total_pedido').val(total)
        })
    })
</script>