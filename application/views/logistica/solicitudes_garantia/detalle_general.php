<?php 
$menu = ["detalle", "archivos", "bitacora", "opciones"];

if (!$tipo) $tipo = "detalle"; 
if (!in_array($tipo, $menu)) redirect('inicio');

$vista = $tipo;
if($tipo ==="bitacora") $vista = "bitacora/index";
?>

<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de solicitudes de garantía</h1>
        </div>
    </div>
</div>

<div class="pl-5 pr-5">
    <div class="block-zone__widget-header">
        <div class="block-zone__tabs">
            <button type="button" class="block-zone__tabs-button" id="pestana_detalle">
                <a href="<?php echo site_url("logistica/garantias/ver/$solicitud_garantia->id/detalle"); ?>">
                    Formulario
                </a>
            </button>
            <button type="button" class="block-zone__tabs-button" id="pestana_archivos">
                <a href="<?php echo site_url("logistica/garantias/ver/$solicitud_garantia->id/archivos"); ?>">
                    Archivos
                </a>
            </button>
            <button type="button" class="block-zone__tabs-button" id="pestana_bitacora">
                <a href="<?php echo site_url("logistica/garantias/ver/$solicitud_garantia->id/bitacora"); ?>">
                    Bitácora
                </a>
            </button>
            <button type="button" class="block-zone__tabs-button" id="pestana_opciones">
                <a href="<?php echo site_url("logistica/garantias/ver/$solicitud_garantia->id/opciones"); ?>">
                    Opciones
                </a>
            </button>
        </div>
    </div>

    <div id="contenedor_detalle"></div>
</div>
<div class="block-space block-space--layout--before-footer"></div>


<script>
    var solicitudGarantia = JSON.parse('<?php echo addslashes(json_encode($solicitud_garantia)); ?>')
    
    cargarOpcionMenu = () => {
        cargarInterfaz('logistica/solicitudes_garantia/<?php echo $vista; ?>', 'contenedor_detalle', {solicitud_garantia: solicitudGarantia})

        $(`#pestana_<?php echo $tipo; ?>`).addClass('block-zone__tabs-button--active')
    }

    $().ready(() => {
        cargarOpcionMenu()
    })
</script>