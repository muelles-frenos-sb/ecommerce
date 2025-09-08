<?php
// Carga de la plantilla
$archivo = \PhpOffice\PhpSpreadsheet\IOFactory::load('application/views/reportes/plantillas/proveedores_orden_compra.xlsx');

// Variables para almacenar posiciones
$fila_documentos = 2;
$fila_movimientos = 2;
$consecutivo_documento = 0;

// Datos de la solicitud de cotización
$solicitud = $this->proveedores_model->obtener('proveedores_cotizaciones_solicitudes', ['id' => $id]);

// Se obtienen los productos que tienen un precio, ordenado por los proveedores
$registros = $this->proveedores_model->obtener('cotizaciones_mejores_precios', ['id' => $id]);

// Variable para capturar el NIT
$nit_actual = null;

foreach ($registros as $registro) {
    // Si cambia de NIT, se va a alimentar la hoja de documentos
    if($nit_actual !== $registro->proveedor_nit) {
        // Se resetea el consecutivo del movimiento
        $consecutivo_movimiento = 0;
        
        // Se aumenta el consecutivo del documento
        $consecutivo_documento++;

        // Posicionado sobre la primera hoja
        $hoja_documentos = $archivo->setActiveSheetIndexByName('Documentos');

        // Datos para los documentos de la orden de compra
        $hoja_documentos->setCellValue("A$fila_documentos", '500'); // Centro de operación
        $hoja_documentos->setCellValue("B$fila_documentos", 'FOC'); // Tipo de documento
        $hoja_documentos->setCellValue("C$fila_documentos", $consecutivo_documento); // Consecutivo de documento
        $hoja_documentos->setCellValue("D$fila_documentos", date("Ymd", strtotime($solicitud->fecha_inicio))); // Fecha del documento
        $hoja_documentos->setCellValue("E$fila_documentos", '32243764'); // NIT del comprador
        $hoja_documentos->setCellValue("F$fila_documentos", $registro->proveedor_nit); // NIT del proveedor
        $hoja_documentos->setCellValue("G$fila_documentos", ''); // Condición de pago
        $hoja_documentos->setCellValue("H$fila_documentos", 'COP'); // Moneda
        $hoja_documentos->setCellValue("I$fila_documentos", $registro->proveedor_nombre); // Nombre del proveedor
        
        $fila_documentos++;

        // Se asigna el valor del NIT en la variable del último NIT
        $nit_actual = $registro->proveedor_nit;
    }

    // Se obtiene el registro del detalle de la solicitud de cotización, para obtener la cantidad
    $detalle = $this->proveedores_model->obtener('proveedores_cotizaciones_solicitudes_detalle', ['cotizacion_id' => $id, 'producto_id' => $registro->producto_id]);
    
    // Se aumenta el consecutivo del movimiento
    $consecutivo_movimiento++;
    
    // Posicionado sobre la hoja de movimientos
    $hoja_movimientos = $archivo->setActiveSheetIndexByName('Movimientos');

    // Datos para los movimientos de la orden de compra
    $hoja_movimientos->setCellValue("A$fila_movimientos", '500'); // Centro de operación
    $hoja_movimientos->setCellValue("B$fila_movimientos", 'FOC'); // Tipo de documento
    $hoja_movimientos->setCellValue("C$fila_movimientos", $consecutivo_documento); // Consecutivo de documento
    $hoja_movimientos->setCellValue("D$fila_movimientos", $consecutivo_movimiento); // Consecutivo de Excel
    // $hoja_movimientos->setCellValue("E$fila_movimientos", ''); // Bodega
    $hoja_movimientos->setCellValue("F$fila_movimientos", '500'); // Centro de operación
    $hoja_movimientos->setCellValue("G$fila_movimientos", 'UNID'); // Unidad de medida
    $hoja_movimientos->setCellValue("H$fila_movimientos", $detalle->cantidad); // Unidad de medida
    $hoja_movimientos->setCellValue("I$fila_movimientos", date("Ymd", strtotime($solicitud->fecha_inicio))); // Fecha de entrega del documento
    $hoja_movimientos->setCellValue("J$fila_movimientos", $registro->precio_final); // Precio unitario
    $hoja_movimientos->setCellValue("K$fila_movimientos", $registro->referencia); // Referencia del producto

    $fila_movimientos++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=Orden de compra de la cotización $id.xlsx");
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($archivo, 'Xlsx');
$writer->save('php://output');
exit;