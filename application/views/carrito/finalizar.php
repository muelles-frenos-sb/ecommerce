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
                <?php if(!$this->session->userdata('usuario_id')) { ?>
                    <div class="col-12 mb-3">
                        <div class="alert alert-lg alert-primary">¿Ya estás registrado? <a href="<?php echo site_url('sesion?url='.current_url()); ?>">Inicia sesión</a></div>
                    </div>
                <?php } ?>

                <div class="col-12 col-lg-6 col-xl-7">
                    <div class="card mb-lg-0">
                        <div class="card-body card-body--padding--2">
                            <h3 class="card-title">Detalles del pago</h3>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="checkout_nombres">Nombres *</label>
                                    <input type="text" class="form-control" id="checkout_nombres" value="<?php if(!empty($tercero)) echo $tercero->nombres; ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="checkout_primer_apellido">Primer apellido *</label>
                                    <input type="text" class="form-control" id="checkout_primer_apellido" value="<?php if(!empty($tercero)) echo $tercero->primer_apellido; ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="checkout_segundo_apellido">Segundo apellido</label>
                                    <input type="text" class="form-control" id="checkout_segundo_apellido" value="<?php if(!empty($tercero)) echo $tercero->segundo_apellido; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="checkout_documento_numero">Número de documento *</label>
                                <input type="text" class="form-control" id="checkout_documento_numero" value="<?php if(!empty($tercero)) echo $tercero->documento_numero; ?>">
                            </div>
                            <div class="form-group">
                                <label for="checkout_razon_social">Razón social <span class="text-muted">(Opcional)</span></label>
                                <input type="text" class="form-control" id="checkout_razon_social" value="<?php if(!empty($tercero)) echo $tercero->razon_social; ?>">
                            </div>
                            <div class="form-group">
                                <label for="checkout_direccion">Dirección</label>
                                <input type="text" class="form-control" id="checkout_direccion" value="<?php if(!empty($tercero)) echo $tercero->direccion1; ?>">
                            </div>
                            <div class="form-group">
                                <label for="checkout_pais">País</label>
                                <select id="checkout_pais" class="form-control form-control-select2">
                                    <option value="">Seleccione...</option>
                                    <?php foreach($this->configuracion_model->obtener('paises') as $pais) echo "<option value='$pais->pais_id'>$pais->nombre</option>"; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="checkout_departamento">Departamento</label>
                                <select id="checkout_departamento" class="form-control form-control-select2">
                                    <option value="">Seleccione...</option>
                                    <?php foreach($this->configuracion_model->obtener('departamentos') as $departamento) echo "<option value='$departamento->departamento_id'>$departamento->nombre</option>"; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="checkout_ciudad">Ciudad</label>
                                <select id="checkout_ciudad" class="form-control form-control-select2">
                                    <option value="">Seleccione...</option>
                                    <?php foreach($this->configuracion_model->obtener('ciudades') as $ciudad) echo "<option value='$ciudad->ciudad_id'>$ciudad->nombre</option>"; ?>
                                </select>
                            </div>
                            <!-- <div class="form-group">
                                <label for="checkout-postcode">Postcode / ZIP</label>
                                <input type="text" class="form-control" id="checkout-postcode">
                            </div> -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="checkout_email">Correo electrónico *</label>
                                    <input type="email" class="form-control" id="checkout_email" value="<?php if(!empty($tercero)) echo $tercero->email; ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="checkout_telefono">Teléfono</label>
                                    <input type="text" class="form-control" id="checkout_telefono" value="<?php if(!empty($tercero)) echo $tercero->telefono; ?>">
                                </div>
                            </div>
                            <!-- <div class="form-group">
                                <div class="form-check">
                                    <span class="input-check form-check-input">
                                        <span class="input-check__body">
                                            <input class="input-check__input" type="checkbox" id="checkout-create-account">
                                            <span class="input-check__box"></span>
                                            <span class="input-check__icon"><svg width="9px" height="7px">
                                                    <path d="M9,1.395L3.46,7L0,3.5L1.383,2.095L3.46,4.2L7.617,0L9,1.395Z" />
                                                </svg>
                                            </span>
                                        </span>
                                    </span>
                                    <label class="form-check-label" for="checkout-create-account">Create an account?</label>
                                </div>
                            </div> -->
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
                            <!-- <div class="checkout__payment-methods payment-methods">
                                <ul class="payment-methods__list">
                                    <li class="payment-methods__item payment-methods__item--active">
                                        <label class="payment-methods__item-header">
                                            <span class="payment-methods__item-radio input-radio">
                                                <span class="input-radio__body">
                                                    <input class="input-radio__input" name="checkout_payment_method" type="radio" checked>
                                                    <span class="input-radio__circle"></span>
                                                </span>
                                            </span>
                                            <span class="payment-methods__item-title">Direct bank transfer</span>
                                        </label>
                                        <div class="payment-methods__item-container">
                                            <div class="payment-methods__item-details text-muted">
                                                Make your payment directly into our bank account. Please use your Order ID as the payment
                                                reference. Your order will not be shipped until the funds have cleared in our account.
                                            </div>
                                        </div>
                                    </li>
                                    <li class="payment-methods__item">
                                        <label class="payment-methods__item-header">
                                            <span class="payment-methods__item-radio input-radio">
                                                <span class="input-radio__body">
                                                    <input class="input-radio__input" name="checkout_payment_method" type="radio">
                                                    <span class="input-radio__circle"></span>
                                                </span>
                                            </span>
                                            <span class="payment-methods__item-title">Check payments</span>
                                        </label>
                                        <div class="payment-methods__item-container">
                                            <div class="payment-methods__item-details text-muted">
                                                Please send a check to Store Name, Store Street, Store Town, Store State / County, Store Postcode.
                                            </div>
                                        </div>
                                    </li>
                                    <li class="payment-methods__item">
                                        <label class="payment-methods__item-header">
                                            <span class="payment-methods__item-radio input-radio">
                                                <span class="input-radio__body">
                                                    <input class="input-radio__input" name="checkout_payment_method" type="radio">
                                                    <span class="input-radio__circle"></span>
                                                </span>
                                            </span>
                                            <span class="payment-methods__item-title">Cash on delivery</span>
                                        </label>
                                        <div class="payment-methods__item-container">
                                            <div class="payment-methods__item-details text-muted">
                                                Pay with cash upon delivery.
                                            </div>
                                        </div>
                                    </li>
                                    <li class="payment-methods__item">
                                        <label class="payment-methods__item-header">
                                            <span class="payment-methods__item-radio input-radio">
                                                <span class="input-radio__body">
                                                    <input class="input-radio__input" name="checkout_payment_method" type="radio">
                                                    <span class="input-radio__circle"></span>
                                                </span>
                                            </span>
                                            <span class="payment-methods__item-title">PayPal</span>
                                        </label>
                                        <div class="payment-methods__item-container">
                                            <div class="payment-methods__item-details text-muted">
                                                Pay via PayPal; you can pay with your credit card if you don’t have a PayPal account.
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div> -->
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

                            <button type="submit" class="btn btn-primary btn-xl btn-block" onClick="javascript:guardarFactura()">Realizar pago seguro</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="block-space block-space--layout--before-footer"></div>
</div>

<div id="contenedor_pago"></div>

<?php if(isset($tercero)) { ?>
    <script>
        $().ready(() => {
            $('#checkout_pais').val('<?php echo $tercero->pais_id; ?>')
            $('#checkout_departamento').val('<?php echo $tercero->departamento_id; ?>')
            $('#checkout_ciudad').val('<?php echo $tercero->ciudad_id; ?>')
        })
    </script>
<?php } ?>

<script>
    guardarFactura = async() => {
        if(<?php echo $this->cart->total(); ?> == 0) {
            mostrarAviso('alerta', 'No hay ningún producto en el carrito.')
            return false
        }

        let camposObligatorios = [
            $('#checkout_nombres'),
            $('#checkout_primer_apellido'),
            $('#checkout_direccion'),
            $('#checkout_email'),
            $('#checkout_telefono'),
            $('#checkout_documento_numero'),
        ]

        if (!validarCamposObligatorios(camposObligatorios)) return false

        let datosFactura = {
            tipo: 'facturas',
            nombres: $('#checkout_nombres').val(),
            primer_apellido: $('#checkout_primer_apellido').val(),
            segundo_apellido: $('#checkout_segundo_apellido').val(),
            razon_social: $('#checkout_razon_social').val(),
            documento_numero: $('#checkout_documento_numero').val(),
            direccion: $('#checkout_direccion').val(),
            pais_id: $('#checkout_pais').val(),
            departamento_id: $('#checkout_departamento').val(),
            ciudad_id: $('#checkout_ciudad').val(),
            email: $('#checkout_email').val(),
            telefono: $('#checkout_telefono').val(),
            comentarios: $('#checkout_comentarios').val(),
            valor: <?php echo $this->cart->total(); ?>,
        }

        let factura = await consulta('crear', datosFactura, false)
        
        if (factura.resultado) {
            cargarInterfaz('carrito/pago', 'contenedor_pago', {id: factura.resultado})
        }
    }
</script>