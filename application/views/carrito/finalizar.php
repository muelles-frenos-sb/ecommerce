<?php
if($this->session->userdata('usuario_id')) {
    $tercero = $this->configuracion_model->obtener('usuarios', ['id' => $this->session->userdata('usuario_id')]);
    echo "<input type='hidden' id='carrito_tercero_id' value='$tercero->id' />";
}
?>

<div class="site__body">
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
                <?php if(ENVIRONMENT == 'development' && !$this->session->userdata('usuario_id')) { ?>
                    <!-- <div class="col-12 mb-3">
                        <div class="alert alert-lg alert-primary">¿Ya estás registrado? <a href="<?php // echo site_url('sesion?url='.current_url()); ?>">Inicia sesión</a></div>
                    </div> -->
                <?php } ?>

                <div class="col-12 col-lg-6 col-xl-7">
                    <div class="card mb-lg-0">
                        <div class="card-body card-body--padding--2">
                            <h3 class="card-title">Detalles del pago</h3>
                            <div class="form-group">
                                <label for="checkout_documento_numero">Número de documento *</label>
                                <input type="number" class="form-control" id="checkout_documento_numero" value="" autofocus>
                            </div>
                            <button class="btn btn-primary btn-block" id="btn_validar_documento">Validar número de documento</button>

                            <div id="contenedor_datos_cliente"></div>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-body card-body--padding--2">
                            <h3 class="card-title">Detalles</h3>
                            <div class="form-group">
                                <label for="checkout_comentarios">Notas adicionales <span class="text-muted">(Opcional)</span></label>
                                <textarea id="checkout_comentarios" class="form-control" rows="4"></textarea>
                            </div>
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
                                    <tr>
                                        <th>Envío</th>
                                        <td>$ 0</td>
                                    </tr>
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
                                            <input class="input-check__input" type="checkbox" id="checkout-terms">
                                            <span class="input-check__box"></span>
                                            <span class="input-check__icon"><svg width="9px" height="7px">
                                                    <path d="M9,1.395L3.46,7L0,3.5L1.383,2.095L3.46,4.2L7.617,0L9,1.395Z" />
                                                </svg>
                                            </span>
                                        </span>
                                    </span>
                                    <label class="form-check-label" for="checkout-terms">
                                        He leído los <a target="_blank" href="terms.html">términos y condiciones</a>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-xl btn-block" onClick="javascript:guardarFactura()" id="btn_pagar" disabled>Realizar pago seguro</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="block-space block-space--layout--before-footer"></div>
</div>

<div id="contenedor_pago"></div>

<script>
    cargarDatosCliente = async() => {
        // Se activa el spinner
        $('#btn_validar_documento').addClass('btn-loading').attr('disabled', true)

        // e.target.addClass('btn-loading')
        if (!validarCamposObligatorios([$('#checkout_documento_numero')])) return false

        cargarInterfaz('carrito/finalizar_datos_cliente', 'contenedor_datos_cliente', {numero_documento: $.trim($('#checkout_documento_numero').val())})
    }

    guardarFactura = async() => {
        // Alerta cuando no hay ítems en el carrito
        if(<?php echo $this->cart->total(); ?> == 0) {
            mostrarAviso('alerta', 'No hay ningún producto en el carrito.')
            return false
        }

        let camposObligatorios = [
            $('#checkout_razon_social'),
            $('#checkout_direccion'),
            $('#checkout_email'),
            $('#checkout_telefono'),
        ]
        
        // Si tiene sucursales, es obligatorio
        if(parseInt($('#cantidad_sucursales').val()) > 0) camposObligatorios.push($('#checkout_sucursal'))

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datosRecibo = {
            tipo: 'recibos',
            abreviatura: 'pe',
            nombres: $('#checkout_nombres').val(),
            primer_apellido: $('#checkout_primer_apellido').val(),
            segundo_apellido: $('#checkout_segundo_apellido').val(),
            razon_social: $('#checkout_razon_social').val(),
            documento_numero: $('#checkout_documento_numero').val(),
            direccion: $('#checkout_direccion').val(),
            email: $('#checkout_email').val(),
            telefono: $('#checkout_telefono').val(),
            comentarios: $('#checkout_comentarios').val(),
            valor: <?php echo $this->cart->total(); ?>,
            recibo_tipo_id: 1,
        }

        // Si trae sucursales, se agrega a los datos
        if(parseInt($('#cantidad_sucursales').val()) > 0) datosRecibo.sucursal_id = $('#checkout_sucursal').val()

        let recibo = await consulta('crear', datosRecibo, false)
        
        // Una vez creado el recibo
        if (recibo.resultado) {
            // Se crean los ítems de la factura
            let reciboItems = await consulta('crear', {tipo: 'recibos_detalle', 'recibo_id': recibo.resultado}, false)

            if (reciboItems.resultado) cargarInterfaz('carrito/pago', 'contenedor_pago', {id: recibo.resultado})
        }
    }

    $().ready(() => {
        $('#btn_validar_documento').click(() => cargarDatosCliente())
    })
</script>