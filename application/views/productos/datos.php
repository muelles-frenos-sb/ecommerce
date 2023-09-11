<?php
$productos = $this->productos_model->obtener('productos', $datos);

if(empty($productos)) echo '<p>No se encontraron resultados con los filtros seleccionados</p>';

foreach($productos as $producto) {
?>
    <div class="products-list__item">
        <div class="product-card">
            <?php
            // Detalle del producto
            $this->data['producto'] = $producto;
            $this->load->view('productos/item', $this->data)
            ?>
        </div>
    </div>
<?php } ?>

<script>
	$().ready(() => {
		let totalRegistros = parseInt("<?php echo count($productos); ?>")

		// Si no hay más datos o son menos del total configurado, se oculta el botón
		if(totalRegistros == 0 || totalRegistros < parseInt($('#cantidad_datos').val())) $("#btn_mostrar_mas").hide()
	})
</script>