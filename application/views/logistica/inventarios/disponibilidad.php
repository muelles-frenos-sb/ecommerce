<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Parametrización de disponibilidad de inventario</h1>
        </div>
    </div>
</div>

<div class="block">
    <div class="container-fluid">
        <div class="row">
            <!-- Columna izquierda: bodega y búsqueda -->
            <div class="col-lg-3">
                <!-- Bodega y porcentaje general -->
                <div class="card mb-3">
                    <div class="card-body card-body--padding--2">
                        <div class="form-group">
                            <label for="inventario_bodega">Bodega *</label>
                            <select id="inventario_bodega" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <label for="inventario_bodega_porcentaje">Porcentaje general de disponibilidad de la bodega *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="inventario_bodega_porcentaje" min="0" max="100" step="0.1" placeholder="100">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">Este porcentaje aplica como límite general para todos los productos de la bodega.</small>
                        </div>
                    </div>
                </div>

                <!-- Formulario de búsqueda de productos -->
                <div class="card">
                    <div class="card-body card-body--padding--2">
                        <form id="formulario_buscar_productos_disponibilidad">
                            <div class="form-group">
                                <label for="buscar_producto_disponibilidad">Buscar producto (nombre, referencia, marca...) *</label>
                                <input type="text" class="form-control" id="buscar_producto_disponibilidad" autocomplete="off">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block" id="btn_buscar_producto_disponibilidad">Buscar</button>
                        </form>
                        <div class="mt-2" id="contenedor_mensaje_producto_disponibilidad"></div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: resultados y seleccionados -->
            <div class="col-lg-9">
                <!-- Resultados de búsqueda -->
                <div class="card mb-3">
                    <div class="card-body card-body--padding--1">
                        <div class="tag-badge tag-badge--new badge_formulario badge_formulario_azul">
                            Resultados de búsqueda
                        </div>
                        <div id="contenedor_resultado_productos_disponibilidad" style="height: 30vh; overflow-y: auto;"></div>
                    </div>
                </div>

                <!-- Productos con disponibilidad parametrizada -->
                <div class="card">
                    <div class="card-body card-body--padding--1">
                        <div class="tag-badge tag-badge--new badge_formulario badge_formulario_azul mb-2">
                            Productos con disponibilidad parametrizada en la bodega seleccionada
                        </div>
                        <div id="contenedor_productos_disponibilidad_seleccionados" style="height: 30vh; overflow-y: auto;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-info" onClick="javascript:history.back()">Volver</button>
        </div>
    </div>
</div>
<div class="block-space block-space--layout--before-footer"></div>

<script>
    const obtenerBodegaCodigoSeleccionada = () => {
        const opcion = $('#inventario_bodega option:selected')
        return opcion.length ? opcion.attr('data-codigo') : null
    }

    const cargarProductosDisponibilidadSeleccionados = () => {
        const bodegaCodigo = obtenerBodegaCodigoSeleccionada()
        if (!bodegaCodigo) {
            $('#contenedor_productos_disponibilidad_seleccionados').html(`
                <div class="text-center p-4 text-muted">
                    <i class="fa fa-info-circle"></i> Selecciona una bodega para ver los productos parametrizados.
                </div>
            `)
            return
        }

        cargarInterfaz('logistica/inventarios/disponibilidad_seleccionados', 'contenedor_productos_disponibilidad_seleccionados', {
            bodega: bodegaCodigo
        })
    }

    const cargarPorcentajeBodega = async () => {
        const bodegaId = $('#inventario_bodega').val()
        if (!bodegaId) {
            $('#inventario_bodega_porcentaje').val('')
            return
        }

        let resultado = await consulta('obtener', { tipo: 'erp_bodegas', f150_rowid: bodegaId }, false)

        if (resultado && resultado.length > 0) {
            const bodega = resultado[0]
            const porcentaje = bodega.pocentaje_disponibilidad ?? bodega.porcentaje_disponibilidad ?? 100
            $('#inventario_bodega_porcentaje').val(porcentaje)
        } else {
            $('#inventario_bodega_porcentaje').val(100)
        }
    }

    const actualizarPorcentajeBodega = async () => {
        const bodegaId = $('#inventario_bodega').val()
        if (!bodegaId) {
            mostrarAviso('alerta', 'Primero selecciona una bodega.')
            return
        }

        let valor = parseFloat($('#inventario_bodega_porcentaje').val())
        if (isNaN(valor) || valor < 0) {
            mostrarAviso('alerta', 'Ingresa un porcentaje válido para la bodega.')
            return
        }

        if (valor > 100) valor = 100

        await consulta('actualizar', {
            tipo: 'erp_bodegas',
            id: bodegaId,
            pocentaje_disponibilidad: valor
        }, false)

        mostrarAviso('exito', 'Porcentaje general de disponibilidad de la bodega actualizado correctamente.')
    }

    agregarProductoDisponibilidad = async (productoId, referencia, descripcion, btn) => {
        const bodegaCodigo = obtenerBodegaCodigoSeleccionada()
        if (!bodegaCodigo) {
            mostrarAviso('alerta', 'Selecciona una bodega antes de parametrizar productos.')
            return
        }

        const fila = $(btn).closest('tr')
        let porcentaje = parseFloat(fila.find('.porcentaje_input').val())

        if (isNaN(porcentaje) || porcentaje < 0) {
            mostrarAviso('alerta', 'Ingresa un porcentaje válido para el producto.')
            return
        }
        if (porcentaje > 100) porcentaje = 100

        let respuesta = await consulta('crear', {
            tipo: 'productos_inventario_disponibilidad',
            producto_id: productoId,
            bodega: bodegaCodigo,
            porcentaje: porcentaje
        }, false)

        if (respuesta && respuesta.resultado) {
            mostrarAviso('exito', `Producto "${referencia}" parametrizado con ${porcentaje}% de disponibilidad en la bodega ${bodegaCodigo}.`)
            cargarProductosDisponibilidadSeleccionados()
        } else {
            mostrarAviso('error', `No se pudo parametrizar la disponibilidad del producto "${referencia}".`)
        }
    }

    $().ready(async function() {
        // Select2 para bodegas (si está disponible)
        if ($.fn.select2) {
            $('#inventario_bodega').select2({ width: '100%' })
        }

        // Carga de bodegas (lista desplegable)
        await listarDatos('inventario_bodega', { tipo: 'erp_bodegas' })

        // Al cambiar de bodega
        $('#inventario_bodega').change(async () => {
            // Cargar porcentaje general de la bodega
            await cargarPorcentajeBodega()

            // Limpiar resultados de búsqueda
            $('#buscar_producto_disponibilidad').val('')
            $('#contenedor_resultado_productos_disponibilidad').html('')
            $('#contenedor_mensaje_producto_disponibilidad').html('')

            // Cargar productos ya parametrizados para la bodega
            cargarProductosDisponibilidadSeleccionados()
        })

        // Cambio en el porcentaje general de la bodega
        $('#inventario_bodega_porcentaje').change(actualizarPorcentajeBodega)

        // Formulario de búsqueda de productos
        $('#formulario_buscar_productos_disponibilidad').submit(async (evento) => {
            evento.preventDefault()

            const buscarProducto = $('#buscar_producto_disponibilidad')
            const bodegaCodigo = obtenerBodegaCodigoSeleccionada()

            let camposObligatorios = [buscarProducto]
            if (!validarCamposObligatorios(camposObligatorios)) return false

            if (!bodegaCodigo) {
                mostrarAviso('alerta', 'Selecciona una bodega antes de buscar productos.')
                return false
            }

            $('#btn_buscar_producto_disponibilidad').addClass('btn-loading').attr('disabled', true)
            $('#contenedor_mensaje_producto_disponibilidad').html(`<button class='btn btn-muted btn-loading btn-xs btn-icon'></button> Buscando coincidencias con ${buscarProducto.val()}...`)

            $('#btn_buscar_producto_disponibilidad').removeClass('btn-loading').attr('disabled', false)

            let datos = {
                tipo: 'productos',
                busqueda: buscarProducto.val(),
                filtro_bodega: bodegaCodigo,
                mostrar_agotados: true,
            }

            cargarInterfaz('logistica/inventarios/disponibilidad_productos', 'contenedor_resultado_productos_disponibilidad', datos)
        })
    })
</script>
