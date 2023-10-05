<?php 
$factura = $this->productos_model->obtener('factura', ['token' => $datos['token']]);
?>

<div class="card">
    <div class="card-header">
        <h5>Comprobante adjuntado al pago</h5>
    </div>
    <div class="card-divider"></div>
    
</div>