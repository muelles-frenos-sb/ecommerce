<?php $tercero = $this->clientes_model->obtener('tercero', ['f200_nit' => $datos['numero_documento']]); ?>

<input type="hidden" id="factura_tercero_razon_social" value="<?php echo $tercero->f200_razon_social; ?>">
<input type="hidden" id="factura_tercero_documento_numero" value="<?php echo $tercero->f200_nit; ?>">

<!-- Modal que se usa para abrir la interfaz de pago de Wompi -->
<div id="contenedor_pago_estado_cuenta"></div>

<div class="card flex-grow-1 mb-md-0 mr-0 mr-lg-3 ml-0 ml-lg-4">
    <div class="card-body card-body--padding--2">
        <?php if($datos['nit_comprobante']) { ?>
            <div class="form-row mb-4">
                <div class="form-group col-md-4">
                    <label for="fecha_consignacion">Fecha de consignación *</label>
                    <input type="date" class="form-control" id="fecha_consignacion" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group col-md-4">
                    <label for="monto">Monto *</label>
                    <input type='text' id="monto" class="form-control" placeholder='Valor pagado' style="text-align: right">
                </div>

                <div class="form-group col-4">
                    <label for="cuenta">Cuenta</label>
                    <select id="cuenta" class="form-control">
                        <option value="">Seleccione...</option>
                        <?php foreach($this->configuracion_model->obtener('cuentas_bancarias') as $cuenta) echo "<option value='$cuenta->id' data-codigo='$cuenta->codigo'>$cuenta->numero - $cuenta->nombre</option>"; ?>
                    </select>
                </div>

                <div class="input-group col-12 mt-2">
                    <input type="file" class="form-control" aria-label="Subir" id="estado_cuenta_archivos" multiple>
                </div>
            </div>
        <?php } ?>

        <div class="tag-badge tag-badge--theme badge_formulario mb-1 mt-1">Facturas pendientes</div>

        <div class="card-table">
            <div id="contenedor_lista_facturas"></div>
        </div>
    </div>
</div>

<div class="mb-3 mt-3"></div>

<div class="card flex-grow-1 mb-md-0 mr-0 mr-lg-3 ml-0 ml-lg-4">
    <div class="card-body card-body--padding--2">
        <div class="tag-badge tag-badge--new badge_formulario mb-1 mt-1">Facturas seleccionadas para pago</div>

        <div id="contenedor_carrito_facturas"></div>
    </div>
</div>

<div id="contenedor_modal"></div>

<script>
    cargarProductos = async(datos) => {
        // Se consulta en el API de Siesa el detalle de la factura (detalle de productos)
        datos.tipo = 'facturas_desde_pedido'
        let productosFactura = await consulta('obtener', datos, false)

        Promise.all([productosFactura])
        .then(async() => {
            if(productosFactura.codigo && productosFactura.codigo == 1) {
                mostrarAviso('alerta', 'No se encontraron resultados con el número de pedido. Intenta de nuevo más tarde.', 30000)
                agregarLog(27, JSON.stringify(datos))
                return false
            }

            // Se insertan en la base de datos todos los registros obtenidos del cliente
            await consulta('crear', {tipo: 'clientes_facturas_detalle', valores: productosFactura.detalle.Table}, false)

            agregarLog(28, JSON.stringify(datos))

            cargarInterfaz('clientes/estado_cuenta/facturas/productos', 'contenedor_modal', datos)
        })
        .catch(error => {
            agregarLog(29, JSON.stringify(datos))
            mostrarAviso('error', 'Ocurrió un error consultando los productos. Intenta de nuevo más tarde.', 30000)
            return false
        })
    }

    cargarMovimientos = async(datos, abrirModal = true) => {
        datos.tipo = 'movimientos_contables'
        let movimientosFactura = await consulta('obtener', datos, false)

        Promise.all([movimientosFactura])
        .then(async() => {
            if(movimientosFactura.codigo && movimientosFactura.codigo == 1) {
                mostrarAviso('alerta', 'No se encontraron movimientos con el número de pedido.', 30000)
                agregarLog(32, JSON.stringify(datos))
                return false
            }

            // Se insertan en la base de datos todos los movimientos obtenidos de la factura
            await consulta('crear', {tipo: 'clientes_facturas_movimientos', valores: movimientosFactura.detalle.Table}, false)

            agregarLog(30, JSON.stringify(datos))

            // Si se necesita cargar la interfaz
            if(abrirModal) cargarInterfaz('clientes/estado_cuenta/facturas/movimientos', 'contenedor_modal', datos)
        })
        .catch(error => {
            agregarLog(31, JSON.stringify(datos))
            mostrarAviso('error', 'Ocurrió un error consultando los movimientos de la factura. Intenta de nuevo más tarde.', 30000)
            return false
        })
    }

    listarFacturas = async() => {
        let datos = {
            numero_documento: '<?php echo $datos['numero_documento']; ?>',
        }

        cargarInterfaz('clientes/estado_cuenta/facturas/lista', 'contenedor_lista_facturas', datos)
    }

    $().ready(() => {
        listarFacturas()

        cargarInterfaz('clientes/estado_cuenta/carrito/index', 'contenedor_carrito_facturas', {numero_documento: '<?php echo $datos['numero_documento']; ?>', nit_comprobante: '<?php echo $datos['nit_comprobante']; ?>'})

        // Datos del cliente para mostrar al inicio de la interfaz
        let datosCliente = JSON.parse('<?php echo json_encode($tercero) ?>')
        cargarInterfaz('clientes/estado_cuenta/facturas/detalle_cliente', 'contenedor_cabecera_cliente', datosCliente)
    })
</script>