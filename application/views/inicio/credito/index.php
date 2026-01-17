<div class="block mt-4">
    <div class="container container--max--xl">
        <div class="row">
            <!-- Estado del cliente -->
            <div class="col-lg-8 order-md-2">
                <div class="row">
                    <div class="col-2">
                        <div class="semaforo mt-3">
                            <div class="item-semaforo">
                                <div class="circulo verde"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-10">
                        <div class="block-reviews__title text-left">Tu cartera se encuentra al día</div>
                        <div class="block-reviews__subtitle text-left">
                            Puedes comprar y usar tu cupo con normalidad
                        </div>
                    </div>
                </div>
            </div><!-- Estado del cliente -->

            <!-- Cupo disponible -->
            <div class="col-lg-4 order-md-3">
                <div class="block-reviews__title text-right">Cupo disponible</div>
                <div class="block-reviews__subtitle text-right">
                    <b>$ <span id="cupo_disponible">-</span></b>
                </div>
            </div><!-- Cupo disponible -->

            <!-- Banner -->
            <div class="col-lg-8 order-md-1">
                <img src="<?php echo base_url(); ?>archivos/banners/inicio/dashboard_credito.webp" width="100%">
            </div><!-- Banner -->

            <!-- Pedidos -->
            <div class="col-lg-4 text-left order-md-4">
                <div class="block-reviews__subtitle text-left mb-2">Mis pedidos:</div>
                <div class="wishlist" id="contenedor_pedidos"></div>
            </div><!-- Pedidos -->
        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    $().ready(async () => {
        let nit = '<?php echo $this->session->userdata('documento_numero'); ?>'
        nit = '811007434'
        
        consulta('obtener', {tipo: 'clientes_sucursales', numero_documento: nit}, false)
        .then(consultaSucursales => {
            if(consultaSucursales.codigo != 0) return
            
            let cliente = consultaSucursales.detalle.Table[0]
            let cupo = parseFloat(cliente.f201_cupo_credito)
            $('#cupo_disponible').text(formatearNumero(cupo))
        })

        cargarInterfaz('inicio/credito/pedidos', 'contenedor_pedidos', {nit: nit})
    })
</script>