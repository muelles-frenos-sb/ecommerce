<?php
if($this->session->userdata('usuario_id')) {
    $tercero = $this->configuracion_model->obtener('usuarios', ['id' => $this->session->userdata('usuario_id')]);
    echo "<input type='hidden' id='carrito_tercero_id' value='$tercero->id' />";
}
?>

<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <nav class="breadcrumb block-header__breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb__list">
                    <li class="breadcrumb__spaceship-safe-area" role="presentation"></li>
                    <li class="breadcrumb__item breadcrumb__item--parent breadcrumb__item--first">
                        <a href="<?php echo site_url('inicio'); ?>" class="breadcrumb__item-link">Inicio</a>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--parent">
                        <a href="<?php echo site_url('carrito/ver'); ?>" class="breadcrumb__item-link">Carrito de compras</a>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--current breadcrumb__item--last" aria-current="page">
                        <span class="breadcrumb__item-link">Pago</span>
                    </li>
                    <li class="breadcrumb__title-safe-area" role="presentation"></li>
                </ol>
            </nav>
            <h1 class="block-header__title">Proceso de pago</h1>
        </div>
    </div>
</div>
<div class="checkout block">
    <div class="container container--max--xl">
        <div class="row">
            <!-- Si no ha iniciado sesión -->
            <?php if(!$this->session->userdata('usuario_id')) { ?>
                <a href="<?php echo site_url('sesion?url='.current_url()); ?>">
                    <img src="<?php echo base_url(); ?>images/inicia_sesion.png" alt="Inicia sesión" class="mb-2" width="100%">
                </a>
            <?php } ?>

            <div class="col-12 col-lg-6 col-xl-7">
                <div class="card mb-lg-0">
                    <div class="card-body card-body--padding--2">
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="checkout_tipo_documento">Tipo de documento *</label>
                                <select id="checkout_tipo_documento" class="form-control">
                                    <option value="">Seleccione...</option>
                                    <option value="C" data-tipo_tercero="1">Cédula de ciudadanía</option>
                                    <option value="N" data-tipo_tercero="2">NIT</option>
                                    <option value="E" data-tipo_tercero="1">Cédula de extranjería</option>
                                </select>
                            </div>

                            <div class="form-group col-6">
                                <label for="checkout_documento_numero">Número de documento *</label>
                                <input type="number" class="form-control" id="checkout_documento_numero" value="<?php if(ENVIRONMENT == 'development') echo '1017250261'; ?>" autofocus>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block" id="btn_validar_documento">Validar datos</button>

                        <div id="contenedor_datos_cliente"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-5 mt-4 mt-lg-0">
                <div class="card mb-0">
                    <div class="card-body card-body--padding--2">
                        <h3 class="card-title">Detalle del pedido</h3>
                        <table class="checkout__totals">
                            <thead class="checkout__totals-header">
                                <tr>
                                    <th>Producto</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody class="checkout__totals-products">
                                <?php foreach ($this->cart->contents() as $item) {
                                    $datos = ['id' => $item['id']];
                                    $producto = $this->productos_model->obtener('productos', $datos);    
                                ?>
                                    <tr>
                                        <td><?php echo $producto->notas; ?> x <?php echo $item['qty']; ?></td>
                                        <td><?php echo formato_precio($item['subtotal']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tbody class="checkout__totals-subtotals">
                                <tr>
                                    <th>Subtotal</th>
                                    <td><?php echo formato_precio($this->cart->total()); ?></td>
                                </tr>
                                <tr id="descuento"></tr>
                            </tbody>
                            <tfoot class="checkout__totals-footer">
                                <tr>
                                    <th>Total</th>
                                    <td><?php echo formato_precio($this->cart->total()); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="checkout__agree form-group">
                            <div class="form-check">
                                <span class="input-check form-check-input">
                                    <span class="input-check__body">
                                        <input class="input-check__input" type="checkbox" id="checkout_terminos">
                                        <span class="input-check__box"></span>
                                        <span class="input-check__icon">
                                            <svg width="9px" height="7px">
                                                <path d="M9,1.395L3.46,7L0,3.5L1.383,2.095L3.46,4.2L7.617,0L9,1.395Z" />
                                            </svg>
                                        </span>
                                    </span>
                                </span>
                                <label class="form-check-label" for="checkout-terms">
                                    He leído los <a target="_blank" href="<?php echo site_url('blog/tratamiento_datos'); ?>">términos y condiciones</a>
                                </label>
                            </div>
                        </div>

                        <img src="<?php echo base_url(); ?>images/mensaje_flete.png" alt="Continuar comprando" class="mb-2" width="100%">

                        <input type="hidden" id="pedido_total_pago" value="<?php echo $this->cart->total(); ?>">
                        <button type="submit" class="btn btn-primary btn-xl btn-block" onClick="javascript:guardarFactura()" id="btn_pagar" disabled>
                            Realizar pago seguro
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<div id="contenedor_pago"></div>

<script>
    let esVendedor = ($('#codigo_vendedor').val() == 0) ? false : true
    
    cargarDatosCliente = async() => {
        if (!validarCamposObligatorios([
            $('#checkout_documento_numero'),
            $('#checkout_tipo_documento'),
        ])) return false
        
        // Se activa el spinner
        $('#btn_validar_documento').addClass('btn-loading').attr('disabled', true)

        cargarInterfaz('carrito/finalizar_datos_cliente', 'contenedor_datos_cliente', {numero_documento: $.trim($('#checkout_documento_numero').val())})
    }

    guardarFactura = async() => {
        let total = parseFloat($('#pedido_total_pago').val())
        
        // Alerta cuando no hay ítems en el carrito
        if(<?php echo $this->cart->total(); ?> == 0) {
            mostrarAviso('alerta', 'No hay ningún producto en el carrito.')
            return false
        }

        let camposObligatorios = [
            $('#checkout_tipo_tercero'),
            $('#checkout_tipo_documento'),
            $('#checkout_razon_social'),
            $('#checkout_direccion'),
            $('#checkout_telefono'),
            $('#checkout_email'),
            $('#checkout_municipio_id'),
            $('#checkout_sucursal'),
            $('#checkout_responsable_iva'),
        ]

        // Si es persona natural
        if ($('#checkout_tipo_tercero').val() == 1) {
            camposObligatorios.push($('#checkout_nombres'))
            camposObligatorios.push($('#checkout_primer_apellido'))
        }

        if(total < 50000) {
            mostrarAviso('alerta', 'Te informamos que si deseas pagar por este medio, el valor debe ser superior o igual a $50.000', 20000)
            return false
        }

        if(!$(`#checkout_terminos`).is(':checked')) {
            mostrarAviso('alerta', 'Por favor lee y acepta los términos y condiciones', 20000)
            return false
        }
        
        // Si tiene sucursales, es obligatorio
        if(parseInt($('#cantidad_sucursales').val()) > 0) camposObligatorios.push($('#checkout_sucursal'))

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datosRecibo = {
            tipo: 'recibos',
            documento_numero: $('#checkout_documento_numero').val(),
            abreviatura: 'pe',
            nombres: $('#checkout_nombres').val(),
            primer_apellido: $('#checkout_primer_apellido').val(),
            segundo_apellido: $('#checkout_segundo_apellido').val(),
            razon_social: $('#checkout_razon_social').val(),
            direccion: $('#checkout_direccion').val(),
            direccion_envio: $('#checkout_direccion_envio').val(),
            email: $('#checkout_email').val(),
            telefono: $('#checkout_telefono').val(),
            comentarios: $('#checkout_comentarios').val(),
            valor: $('#total_pedido').val(),
            recibo_tipo_id: 1,
        }

        // Se agrega la sucursal al pedido
        datosRecibo.sucursal_id = $('#checkout_sucursal').val()

        // // Si trae sucursales, se agrega a los datos
        // if(parseInt($('#cantidad_sucursales').val()) > 0) {}

        // Se crean las sucursales del tercero en la base de datos
        await gestionarSucursales($('#checkout_documento_numero').val())

        // Se obtienen los datos de la sucursal seleccionada para extraer la lista de precio
        let sucursal = await consulta('obtener', {tipo: 'cliente_sucursal', f200_nit: $('#checkout_documento_numero').val()}, false)

        // Si tiene sesión iniciada, la lista de precio es la de clientes
        datosRecibo.lista_precio = ($('#sesion_usuario_id').val() != '')
            ? '<?php echo $this->config->item('lista_precio_clientes'); ?>' // 010
            : '<?php echo $this->config->item('lista_precio'); ?>' // 009

        let recibo = await consulta('crear', datosRecibo, false)
        
        // Una vez creado el recibo
        if (recibo.resultado) {
            // Se crean los ítems de la factura
            let reciboItems = await consulta('crear', {tipo: 'recibos_detalle', 'recibo_id': recibo.resultado, lista_precio: datosRecibo.lista_precio}, false)

            if (reciboItems.resultado) cargarInterfaz('carrito/pago', 'contenedor_pago', {id: recibo.resultado})

            // Si el tercero no existe aun, se va a crear
            if(!$('#api_tercero_id').val()) {
                let datosTerceroSiesa = {
                    responsable_iva: $('#checkout_responsable_iva option:selected').attr('data-responsable_iva'), // Sí, No
                    causante_iva: $('#checkout_responsable_iva option:selected').attr('data-causante_iva'), // Sí, No
                    tipo_tercero: $('#checkout_tipo_tercero').val(), // Natural, jurídica
                    documento_tipo: $('#checkout_tipo_documento').val(),
                    documento_numero: $('#checkout_documento_numero').val(),
                    nombres: $('#checkout_nombres').val(),
                    primer_apellido: $('#checkout_primer_apellido').val(),
                    segundo_apellido: $('#checkout_segundo_apellido').val(),
                    razon_social: $('#checkout_razon_social').val(),
                    id_departamento: $('#checkout_departamento_id').val(),
                    id_ciudad: $('#checkout_municipio_id').val(),
                    direccion: $('#checkout_direccion').val(),
                    contacto: '-',
                    email: $('#checkout_email').val(),
                    telefono: $('#checkout_telefono').val(),
                    vendedor: 'U003',
                    lista_precio: (esVendedor) ? '001' : '<?php echo $this->config->item('lista_precio_clientes'); ?>',
                }
                
                // Si es cédula de extranjería, se envía una entidad dinámica adicional
                // para la creación del tercero en Siesa
                if($('#checkout_tipo_documento').val() == 'E') {
                    datosTerceroSiesa.entidad_dinamica_extranjero = {
                        f200_id: $('#checkout_documento_numero').val(),
                        f753_id_entidad: 'EUNOECO036',
                        f753_id_atributo: 'co036_id_procedencia_org',
                        f753_id_maestro: 'MUNOECO043',
                        f753_id_maestro_detalle: 11,
                    }
                }

                // Si es vendedor, se envía una entidad dinámica adicional
                // para la asignación del segmento
                if(esVendedor) {
                    datosTerceroSiesa.criterio_cliente = {
                        f207_id_tercero: $('#checkout_documento_numero').val(),
                        f207_id_sucursal: '001',
                        f207_id_plan_criterios: $('#usuario_segmento_id option:selected').attr('data-plan'),
                        f207_id_criterio_mayor: $('#usuario_segmento_id option:selected').attr('data-mayor'),
                    }
                }

                creacionTercero = crearTerceroCliente(datosTerceroSiesa)
                creacionTercero.then(resultado => console.log(resultado))
            }
        }
    }

    $().ready(() => {
        $('#btn_validar_documento').click(() => cargarDatosCliente())
    })
</script>