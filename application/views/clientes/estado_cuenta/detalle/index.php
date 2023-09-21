<!-- <div class="card-divider"></div> -->
<div class="card-table">
    <form class="form-group" id="formulario_buscar_factura">
        <div class="row">
            <div class="col-lg-10 col-sm-12">
                <input type="text" class="form-control" id="estado_cuenta_buscar" placeholder="Buscar una factura por número, placa, fecha, valor, etc.">
            </div>
            <div class="col-lg-2 col-sm-12">
                <button type="submit" class="btn btn-primary btn-block">Buscar</button>
            </div>
        </div>
    </form>
    
    <div id="contenedor_lista_facturas"></div>
</div>

<div class="card-divider"></div>

<div class="card-footer">
    <!-- <ul class="pagination">
        <li class="page-item disabled">
            <a class="page-link page-link--with-arrow" href="" aria-label="Previous">
                <span class="page-link__arrow page-link__arrow--left" aria-hidden="true"><svg width="7" height="11">
                        <path d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z" />
                    </svg>
                </span>
            </a>
        </li>
        <li class="page-item"><a class="page-link" href="#">1</a></li>
        <li class="page-item active" aria-current="page">
            <span class="page-link">
                2
                <span class="sr-only">(current)</span>
            </span>
        </li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item"><a class="page-link" href="#">4</a></li>
        <li class="page-item page-item--dots">
            <div class="pagination__dots"></div>
        </li>
        <li class="page-item"><a class="page-link" href="#">9</a></li>
        <li class="page-item">
            <a class="page-link page-link--with-arrow" href="" aria-label="Next">
                <span class="page-link__arrow page-link__arrow--right" aria-hidden="true"><svg width="7" height="11">
                        <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9 C-0.1,9.8-0.1,10.4,0.3,10.7z" />
                    </svg>
                </span>
            </a>
        </li>
    </ul> -->
</div>

<script>
    listarFacturas = async() => {
        if($('#estado_cuenta_buscar').val() == '' && localStorage.simonBolivar_buscarFacturaEstadoCuenta) $('#estado_cuenta_buscar').val(localStorage.simonBolivar_buscarFacturaEstadoCuenta)
        
        if(localStorage.simonBolivar_buscarFacturaEstadoCuenta) $('#estado_cuenta_buscar').val(localStorage.simonBolivar_buscarFacturaEstadoCuenta)

        let datos = {
            numero_documento: '<?php echo $datos['numero_documento']; ?>',
            busqueda: $("#estado_cuenta_buscar").val(),
        }
        console.log(datos)

        cargarInterfaz('clientes/estado_cuenta/detalle/lista', 'contenedor_lista_facturas', datos)
    }

    $().ready(() => {
        listarFacturas()

        $('#formulario_buscar_factura').submit(evento => {
            evento.preventDefault()

            // Se almacena el valor de búsqueda en local storage
            localStorage.simonBolivar_buscarFacturaEstadoCuenta = $('#estado_cuenta_buscar').val()

            listarFacturas()
        })
    })
</script>