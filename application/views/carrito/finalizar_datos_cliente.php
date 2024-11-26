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

// Se almacena un dato para reconocer que el tercero existía previamente, y no crearlo al ir al pago
if(!empty($tercero)) {
    echo "<input type='hidden' id='api_tercero_id' value='$tercero->f200_id' />";
    echo "<input type='hidden' id='api_departamento_codigo' value='$tercero->f015_id_depto' />";
    echo "<input type='hidden' id='api_municipio_codigo' value='$tercero->f015_id_ciudad' />";
}
?>

<input type="hidden" id="total_pedido">

<div class="form-row">
    <div class="form-group col-6">
        <label for="checkout_responsable_iva">¿Eres responsable de IVA? *</label>
        <select id="checkout_responsable_iva" class="form-control">
            <option value="">Selecciona...</option>
            <option value="0" data-responsable_iva="49" data-causante_iva="ZY">No</option>
            <option value="1" data-responsable_iva="48" data-causante_iva="01">Sí</option>
        </select>
    </div>

    <div class="form-group col-6">
        <label for="checkout_tipo_tercero">¿Eres persona natural o jurídica? *</label>
        <select id="checkout_tipo_tercero" class="form-control" autofocus>
            <option value="">Seleccione...</option>
            <option value="1">Persona natural</option>
            <option value="2">Persona jurídica</option>
        </select>
    </div>
</div>

<div class="form-row" id="checkout_datos_persona_natural">
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
<div class="form-row">
    <div class="form-group col-lg-6 col-sm-12">
        <label for="checkout_departamento_id">Departamento *</label>
        <select id="checkout_departamento_id" class="form-control"></select>
    </div>

    <div class="form-group col-lg-6 col-sm-12">
        <label for="checkout_municipio_id">Municipio *</label>
        <select id="checkout_municipio_id" class="form-control"></select>
    </div>
</div>
<div class="form-group">
    <label for="checkout_direccion">Dirección *</label>
    <input type="text" class="form-control" id="checkout_direccion" value="<?php if(!empty($tercero)) echo $tercero->f015_direccion1; ?>">
</div>
<div class="form-group">
    <label for="checkout_sucursal">Elija la sucursal o placa a la que desea asociar el pedido</label>
    <select id="checkout_sucursal" class="form-control form-control-select2">
        <option value="">Seleccione...</option>
        <?php if(empty($sucursales)) echo "<option value='001'>Principal</option>" ?>
        <?php foreach($sucursales as $sucursal) echo "<option value='$sucursal->f201_id_sucursal'>$sucursal->f201_descripcion_sucursal</option>"; ?>
    </select>
</div>
<div class="form-row">
    <div class="form-group col-md-6">
        <label for="checkout_email">Correo electrónico *</label>
        <input type="email" class="form-control" id="checkout_email" value="<?php if(!empty($tercero)) echo $tercero->f015_email; ?>">
    </div>
    <div class="form-group col-md-6">
        <label for="checkout_telefono">Teléfono *</label>
        <input type="text" class="form-control" id="checkout_telefono" value="<?php if(!empty($tercero)) echo $tercero->f015_celular; ?>">
    </div>
</div>

<?php if(isset($tercero)) { ?>
    <script>
        $().ready(() => {
            $('#btn_validar_documento').removeClass('btn-loading').hide()
            $('#checkout_documento_numero, #checkout_tipo_documento').attr('disabled', true)
            $('#btn_pagar').attr('disabled', false)
        })
    </script>
<?php } ?>

<script>
    mostrarTotales = async listaPrecio => {
        let descuento = 0
        let subtotal = parseFloat('<?php echo $this->cart->total(); ?>')

        if(listaPrecio == '<?php echo $this->config->item('lista_precio_clientes'); ?>') {
            descuento = await consulta('obtener', {tipo: 'valores_detalle', lista_precio: listaPrecio}, false)
        }
        
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
    }

    $().ready(async() => {
        // Si tiene sesión iniciada, la lista de precio es la de clientes
        let listaPrecioPorDefecto = 
            ($('#sesion_usuario_id').val() != '')
            ? '<?php echo $this->config->item('lista_precio_clientes'); ?>'
            : '<?php echo $this->config->item('lista_precio'); ?>'

        mostrarTotales(listaPrecioPorDefecto)

        await listarDatos('checkout_departamento_id', {tipo: 'departamentos', pais_id: 169})

        // Si es un tercero existente
        if($('#api_tercero_id').val()) {
            // Pone por defecto el departamento
            $(`#checkout_departamento_id option[data-codigo="${$('#api_departamento_codigo').val()}"]`).attr('selected', true)

            // Carga los datos de municipios
            await listarDatos('checkout_municipio_id', {tipo: 'municipios', departamento_id: $('#checkout_departamento_id').val()})

            // Pone por defecto el municipio
            $(`#checkout_municipio_id option[data-codigo="${$('#api_municipio_codigo').val()}"]`).attr('selected', true)
        }

        // Si es una sola sucursal, se pone por defecto
        if($('#cantidad_sucursales').val() == 1) $('#checkout_sucursal').val('001')

        // Cuando se seleccione el tipo de tercero
        $('#checkout_tipo_tercero').change(() => {
            // Persona natural
            if ($('#checkout_tipo_tercero').val() == 1) {
                $('#checkout_datos_persona_natural').show()
                $('#checkout_razon_social').attr('disabled', true)
            }

            // Persona jurídica
            if ($('#checkout_tipo_tercero').val() == 2) {
                $('#checkout_datos_persona_natural').hide()
                $('#checkout_razon_social').attr('disabled', false)
                $('#checkout_nombres, #checkout_primer_apellido, #checkout_segundo_apellido, #checkout_razon_social').val('')
            }
        })
        
        // Cuando se seleccione un departamento
        $('#checkout_departamento_id').change(() => {
            listarDatos('checkout_municipio_id', {tipo: 'municipios', departamento_id: $('#checkout_departamento_id').val()})
        })

        // Cuando se escribe nombres o apellidos
        $('#checkout_datos_persona_natural').keyup(function(e) {
            // Si es persona natural
            if($('#checkout_tipo_tercero').val() == '1') {
                // Se completa la razón social
                $('#checkout_razon_social').val(`${$('#checkout_nombres').val()} ${$('#checkout_primer_apellido').val()} ${$('#checkout_segundo_apellido').val()}`)
            }
        })
    })
</script>