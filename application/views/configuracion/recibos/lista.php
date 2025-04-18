<input type="hidden" id="recibo_id_tipo" value="<?php echo $datos['id_tipo_recibo']; ?>">

<?php if($datos['id_tipo_recibo'] == 3) { ?>
    <a class="btn btn-success mb-2" href="<?php echo site_url('configuracion/comprobantes/crear'); ?>">Subir comprobante</a>
<?php } ?>

<div class="table-responsive">
    <table class="table-striped table-bordered" id="tabla_recibos">
        <thead>
            <tr>
                <th class="text-center">Fecha ingreso</th>
                <th class="text-center">Hora ingreso</th>
                <th class="text-center">Fecha pago</th>
                <th class="text-center">NIT</th>
                <th class="text-center">Nombre</th>
                <!-- <th class="text-center">Referencia</th> -->
                <th class="text-center">Forma pago</th>
                <th class="text-center">Recibo Siesa</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Creador</th>
                <th class="text-center">Gestionador</th>
                <th class="text-center">Valor</th>
                <th class="text-center">Opciones</th>
            </tr>
        </thead>
        <tbody id="datos">
            <?php $this->load->view("configuracion/recibos/datos"); ?>
        </tbody>
    </table>
</div>