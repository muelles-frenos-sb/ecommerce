<?php
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Carga de la plantilla
$archivo = \PhpOffice\PhpSpreadsheet\IOFactory::load('application/views/reportes/plantillas/proveedores_cotizaciones_matriz.xlsx');
$hoja = $archivo->getActiveSheet();
$fila_titulos = 5;
$fila = 6;

// Carga de la solicitud y el detalle de la cotización de los proveedores
$solicitud_cotizacion = $this->proveedores_model->obtener('proveedores_cotizaciones_solicitudes', ['id' => $id]);
$cotizacion_detalle = $this->proveedores_model->obtener('proveedores_cotizaciones_detalle', ['cotizacion_id' => $solicitud_cotizacion->id]);

// Del detalle se extraen los proveedores y productos
$proveedores = array_unique(array_column($cotizacion_detalle, 'proveedor_nit'));
$productos = array_unique(array_column($cotizacion_detalle, 'producto_id'));

// Encabezado
$hoja->setCellValue("F1", $solicitud_cotizacion->id);
$hoja->setCellValue("F2", $solicitud_cotizacion->fecha_inicio);
$hoja->setCellValue("F3", $solicitud_cotizacion->fecha_fin);

// Primero, recorremos los productos
foreach($productos as $id_producto) {
    $columna = "D";

    // Datos del producto
    $producto = $this->productos_model->obtener('producto', ['id' => $id_producto]);
    $hoja->setCellValue("A$fila", $producto->id);
    $hoja->setCellValue("B$fila", $producto->referencia);
    $hoja->setCellValue("C$fila", $producto->notas);

    // Recorrido de los proveedores
    foreach($proveedores as $nit_proveedor) {
        // Consulta de datos del proveedor
        $proveedor = $this->configuracion_model->obtener('terceros', ['nit' => $nit_proveedor]);
        
        // Nombre del proveedor
        $hoja->setCellValue("{$columna}5", $proveedor->f200_razon_social);

        // Del arreglo con el detalle de las cotizaciones, se extrae el arreglo que corresponda al proveedor y producto
        $producto_buscado = $id_producto;
        $proveedor_buscado = $nit_proveedor;
        $resultado = array_filter($cotizacion_detalle, function($reg) use ($producto_buscado, $proveedor_buscado) {
            return $reg->producto_id == $producto_buscado && $reg->proveedor_nit == $proveedor_buscado;
        });

        // Se extrae el precio
        $precio = (reset($resultado)->precio) ?? 0;

        $hoja->getColumnDimension($columna)->setWidth(20);
        $hoja->setCellValue("{$columna}{$fila}", $precio);
        $hoja->getStyle("{$columna}{$fila}")->getNumberFormat()->setFormatCode('_($* #,##0_);_($* (#,##0);_($* "-"_);_(@_)');
        $hoja->getStyle("{$columna}{$fila}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        $columna++;
    }

    $fila++;
}

// Se aplica los filtros a las columnas resultantes
$ultimaColumna = $hoja->getHighestColumn($fila_titulos);
$hoja->setAutoFilter("A5:{$ultimaColumna}{$fila_titulos}");

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=Matriz de precios de la cotización $id.xlsx");
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($archivo, 'Xlsx');
$writer->save('php://output');
exit;