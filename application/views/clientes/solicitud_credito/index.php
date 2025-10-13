<div class="block-header block-header--has-breadcrumb block-header--has-title">
    <div class="container">
        <div class="block-header__body">
            <h1 class="block-header__title">Gestión de solicitudes de crédito</h1>
        </div>
    </div>
</div>

<div class="pl-5 pr-5">
    <!-- Si tiene permiso para asignar solicitudes de crédito -->
    <?php if(isset($permisos) && in_array(['clientes' => 'clientes_asignacion_automatica_solicitudes_credito'], $permisos)) { ?>
        <input type="hidden" id="rol_asignacion_automatica" value="1">
        
        <?php
        $disponibilidad = "En línea";
        $disponibilidad_clase = "success";
        ?>        
    <?php } else {
        $disponibilidad = "No disponible";
        $disponibilidad_clase = "warning";
    } ?>

    <a class="btn btn-primary mb-3" href="#" onClick="javascript:listarUsuariosDisponibles()">Ver <span id="usuarios_disponibles"></span> usuarios disponibles</a>

    <div class="status-badge status-badge--style--<?php echo $disponibilidad_clase; ?> product__fit status-badge--has-icon status-badge--has-text">
        <div class="status-badge__body">
            <div class="status-badge__icon"><svg width="13" height="13">
                    <path d="M12,4.4L5.5,11L1,6.5l1.4-1.4l3.1,3.1L10.6,3L12,4.4z" />
                </svg>
            </div>
            <div class="status-badge__text"><?php echo "$disponibilidad para asignación automática de solicitudes de crédito" ?></div>
            <div class="status-badge__tooltip" tabindex="0" data-toggle="tooltip" title="Part&#x20;Fit&#x20;for&#x20;2011&#x20;Ford&#x20;Focus&#x20;S"></div>
        </div>
    </div>
    
    <div id="contenedor_solicitudes_credito"></div>
    <div id="contenedor_asignar_usuario"></div>
</div>
<div class="block-space block-space--layout--before-footer"></div>
<div id="contenedor_modal_usuarios_disponibles"></div>

<script>
    class GestorDisponibilidad {
        constructor() {
            this.intervalo = null
            this.iniciar()
        }
        
        iniciar() {
            // Si no tiene rol para signación automática, se detiene la ejecución
            if($('#rol_asignacion_automatica').val() != '1') return
            
            // Marcar como disponible al cargar
            this.marcarDisponibilidad(true)
            
            // Actualizar cada 2 minutos
            this.intervalo = setInterval(() => {
                this.marcarDisponibilidad(true)
            }, 120000)

            // Marcar como no disponible al cerrar la página
            window.addEventListener('beforeunload', () => {
                this.marcarDisponibilidad(false)
            })
        }
        
        marcarDisponibilidad(disponible) {
            fetch(`${$('#site_url').val()}/configuracion/marcar_disponibilidad`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `disponible=${(disponible ? 1 : 0)}`
            })
            .then(response => response.json())
            .then(resultado => {
                // Se obtienen los usuarios disponibles
                this.obtenerUsuariosDisponibles()
                
                // console.log(usuariosDisponibles)
                // console.log('Estado:', resultado.estado)
            })
        }

        obtenerUsuariosDisponibles() {
            obtenerPromesa(`${$('#site_url').val()}/configuracion/obtener`, {tipo: 'usuarios_disponibles'})
            .then(usuariosDisponibles => {
                if(usuariosDisponibles.length) {
                    $('#usuarios_disponibles').text(usuariosDisponibles.length)
                }
            })
        }
    }

    // Se inicializa el gestor de disponibilidad del usuario
    const gestor = new GestorDisponibilidad()

    cargarAsignarUsuario = (id) => {
        cargarInterfaz('clientes/solicitud_credito/asignar_usuario', 'contenedor_asignar_usuario', {id: id})
    }

    listarSolicitudesCredito = () => {
        cargarInterfaz('clientes/solicitud_credito/lista', 'contenedor_solicitudes_credito')
    }

    listarUsuariosDisponibles = (id) => {
        cargarInterfaz('clientes/solicitud_credito/usuarios_disponibles', 'contenedor_modal_usuarios_disponibles')
    }

    $().ready(() => {
        listarSolicitudesCredito()
    })
</script>