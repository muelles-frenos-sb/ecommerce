<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de pedidos de los clientes</h1>
        </div>
    </div>
</div>

<div class="w-100 p-5">
    <div id="contenedor_pedidos"></div>
</div>

<div class="block-space block-space--layout--before-footer"></div>

<script>
    listarPedidos = () => {
        cargarInterfaz('clientes/pedidos/lista', 'contenedor_pedidos')
    }

    $().ready(() => {
        listarPedidos()
    })
</script>