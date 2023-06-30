<div class="block-finder block">
    <div class="decor block-finder__decor decor--type--bottom">
        <div class="decor__body">
            <div class="decor__start"></div>
            <div class="decor__end"></div>
            <div class="decor__center"></div>
        </div>
    </div>
    <div class="block-finder__image" style="background-image: url('<?php echo base_url(); ?>images/buscar_respuestos.jpg?');"></div>
    <div class="block-finder__body container container--max--xl">
        <div class="block-finder__title">Busque un repuesto para su vehículo</div>
        <div class="block-finder__subtitle">Seleccione una marca, grupo y línea para buscar un repuesto</div>
        <form class="block-finder__form">
            <div class="block-finder__form-control block-finder__form-control--select">
                <select id="repuesto_marca" aria-label="Marca">
                    <option value="">Escoja una marca</option>
                    <?php foreach($this->configuracion_model->obtener('marcas') as $marca) echo "<option value='$marca->id'>$marca->nombre</option>"; ?>
                </select>
            </div>
            <div class="block-finder__form-control block-finder__form-control--select">
                <select id="repuesto_grupo" aria-label="Grupo" disabled>
                    <option value="">Escoja un grupo</option>
                </select>
            </div>
            <div class="block-finder__form-control block-finder__form-control--select">
                <select id="repuesto_linea" aria-label="Línea" disabled>
                    <option value="">Escoja una línea</option>
                </select>
            </div>
            <button class="block-finder__form-control block-finder__form-control--button" type="submit">Buscar</button>
        </form>
    </div>
</div>

<script>
    $().ready(() => {
        $('form').submit(evento => {
            evento.preventDefault()
            
            let filtros = ''

            if($('#repuesto_marca').val()) filtros += `?marca=${$('#repuesto_marca option:selected').text()}`
            if($('#repuesto_grupo').val()) filtros += `&grupo=${$('#repuesto_grupo option:selected').text()}`
            if($('#repuesto_linea').val()) filtros += `&linea=${$('#repuesto_linea option:selected').text()}`

            location.href = `${$('#site_url').val()}productos${filtros}`
        })

        $('#repuesto_marca').change(async() => {
            $(`#repuesto_grupo`).html('')
            
            let grupos = await consulta('obtener', {
                tipo: 'grupos',
                marca: $('#repuesto_marca option:selected').text()
            })
            
            $(`#repuesto_grupo`).append(`<option value=''>Seleccione un grupo</option>`)

            $.each(grupos, function(key, grupo){
                $(`#repuesto_grupo`).append(`
                    <option value='${grupo.id}'>${grupo.nombre}</option>
                `)
            })

            $('#repuesto_grupo').attr('disabled', false)
        })

        $('#repuesto_grupo').change(async() => {
            $(`#repuesto_linea`).html('')
            
            let lineas = await consulta('obtener', {
                tipo: 'lineas',
                marca: $('#repuesto_marca option:selected').text()
            })
            
            $(`#repuesto_linea`).append(`<option value=''>Seleccione una línea</option>`)

            $.each(lineas, function(key, linea){
                $(`#repuesto_linea`).append(`
                    <option value='${linea.id}'>${linea.nombre}</option>
                `)
            })

            $('#repuesto_linea').attr('disabled', false)
        })
    })
</script>