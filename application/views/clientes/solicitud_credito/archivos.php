<?php
$ruta_archivos = "./archivos/solicitudes_credito/{$datos['id']}/*.*";
$archivos = glob($ruta_archivos, GLOB_BRACE);
?>

<div class="block">
    <div class="container">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Archivos</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($archivos as $indice => $archivo) { 
                    $nombre = pathinfo($archivo, PATHINFO_FILENAME);
                ?>
                    <tr>
                        <td><?php echo $indice + 1; ?></td>
                        <td><?php echo $nombre; ?></td>
                        <td>

                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>