<?php
use setasign\Fpdi\Fpdi;

// Crear una instancia de FPDI
$pdf = new FPDI();

$solicitud = $this->clientes_model->obtener("clientes_solicitudes_credito", ["id" => $solicitud_id]);
$clientes_socios_accionistas = $this->clientes_model->obtener("clientes_solicitudes_credito_detalle", ["solicitud_id" => $solicitud_id, "formulario_tipo" => 1]);
$beneficiarios_clientes_socios_accionistas = $this->clientes_model->obtener("clientes_solicitudes_credito_detalle", ["solicitud_id" => $solicitud_id, "formulario_tipo" => 2]);
$personas_autorizadas = $this->clientes_model->obtener("clientes_solicitudes_credito_detalle", ["solicitud_id" => $solicitud_id, "formulario_tipo" => 3]);

$fecha =  date("d / m / Y", strtotime($solicitud->fecha_creacion));

// Cargar el archivo PDF como plantilla
$pdf->AddPage();
$pageCount = $pdf->setSourceFile('application/views/reportes/plantillas/solicitud_credito.pdf');
$tplIdx = $pdf->importPage(1);

// Usar la página del archivo como plantilla
$pdf->useTemplate($tplIdx, 10, 10, 200);

$pdf->SetFont('Arial', '', 7);
$pdf->SetXY(30.6, 38.5);
$pdf->Write(0, ($solicitud->nueva) ? "X": "");

$pdf->SetXY(47, 38.5);
$pdf->Write(0, ($solicitud->nueva) ? "": "X");

$pdf->SetXY(39, 46.5);
$pdf->Write(0, $fecha);

$pdf->SetXY(57, 57.5);
$pdf->Write(0, utf8_decode($solicitud->nombre));

$pdf->SetXY(143, 57.5);
$pdf->Write(0, utf8_decode($solicitud->documento_numero));

$pdf->SetXY(40, 61.5);
$pdf->Write(0, utf8_decode($solicitud->direccion));

$pdf->SetXY(147, 61.5);
$pdf->Write(0, utf8_decode($solicitud->telefono));

$pdf->SetXY(36, 65.5);
$pdf->Write(0, utf8_decode($solicitud->email));

$pdf->SetXY(145, 65.5);
$pdf->Write(0, utf8_decode($solicitud->celular));

$pdf->SetXY(40, 69);
$pdf->Write(0, utf8_decode($solicitud->representante_legal));

$pdf->SetXY(138, 69);
$pdf->Write(0, utf8_decode($solicitud->representante_legal_documento_numero));

$pdf->SetXY(65, 72.7);
$pdf->Write(0, $solicitud->email_factura_electronica);

$pdf->SetXY(145, 72.7);
$pdf->Write(0, utf8_decode("¿Cuántos vehículos posee? $solicitud->cantidad_vehiculos"));

$pdf->SetXY(51.3, 86.8);
$pdf->Write(0, utf8_decode($solicitud->tesoreria_nombre));

$pdf->SetXY(93, 86.8);
$pdf->Write(0, utf8_decode($solicitud->tesoreria_email));

$pdf->SetXY(144, 86.8);
$pdf->Write(0, utf8_decode($solicitud->tesoreria_telefono));

$pdf->SetXY(172, 86.8);
$pdf->Write(0, utf8_decode($solicitud->tesoreria_celular));

$pdf->SetXY(51.3, 90.3);
$pdf->Write(0, utf8_decode($solicitud->comercial_nombre));

$pdf->SetXY(93, 90.3);
$pdf->Write(0, utf8_decode($solicitud->comercial_email));

$pdf->SetXY(144, 90.3);
$pdf->Write(0, utf8_decode($solicitud->comercial_telefono));

$pdf->SetXY(172, 90.3);
$pdf->Write(0, utf8_decode($solicitud->comercial_celular));

$pdf->SetXY(51.3, 93.7);
$pdf->Write(0, utf8_decode($solicitud->contabilidad_nombre));

$pdf->SetXY(93, 93.7);
$pdf->Write(0, utf8_decode($solicitud->contabilidad_email));

$pdf->SetXY(144, 93.7);
$pdf->Write(0, utf8_decode($solicitud->contabilidad_telefono));

$pdf->SetXY(172, 93.7);
$pdf->Write(0, utf8_decode($solicitud->contabilidad_celular));

$pdf->SetXY(40, 105.5);
$pdf->Write(0, utf8_decode($solicitud->referencia_comercial_entidad1));

$pdf->SetXY(89, 105.5);
$pdf->Write(0, $solicitud->referencia_comercial_cel1);

$pdf->SetXY(137, 105.5);
$pdf->Write(0, utf8_decode(substr($solicitud->referencia_comercial_direccion1, 0, 39)));

$pdf->SetXY(40, 109.5);
$pdf->Write(0, utf8_decode($solicitud->referencia_comercial_entidad2));

$pdf->SetXY(89, 109.5);
$pdf->Write(0, $solicitud->referencia_comercial_cel2);

$pdf->SetXY(137, 109.5);
$pdf->Write(0, utf8_decode(substr($solicitud->referencia_comercial_direccion2, 0, 39)));

$pdf->SetXY(62, 119.8);
$pdf->Write(0, utf8_decode($solicitud->referencia_bancaria_entidad));

$pdf->SetXY(109, 119.8);
$pdf->Write(0, utf8_decode($solicitud->referencia_bancaria_tipo));

$pdf->SetXY(155, 119.8);
$pdf->Write(0, utf8_decode($solicitud->referencia_bancaria_numero));

$coordenada = 138;
foreach ($personas_autorizadas as $registro) {
    $pdf->SetXY(105, $coordenada);
    $pdf->Write(0, utf8_decode($registro->nombre));

    $pdf->SetXY(154, $coordenada);
    $pdf->Write(0, utf8_decode($registro->documento_numero));

    $pdf->SetXY(174, $coordenada);
    $pdf->Write(0, utf8_decode($registro->celular));

    $coordenada = $coordenada + 5;
}

$pdf->AddPage();
$tplIdx = $pdf->importPage(2);
// Usar la página del archivo como plantilla
$pdf->useTemplate($tplIdx, 10, 10, 200);

$pdf->SetXY(129, 106.3);
$pdf->Write(0, ($solicitud->recursos_publicos) ? "X": "");

$pdf->SetXY(143, 106.3);
$pdf->Write(0, ($solicitud->recursos_publicos) ? "": "X");

$pdf->SetXY(169, 106.3);
$pdf->Write(0, $solicitud->recursos_publicos_cual);

$pdf->SetXY(129, 109.8);
$pdf->Write(0, ($solicitud->poder_publico) ? "X": "");

$pdf->SetXY(143, 109.8);
$pdf->Write(0, ($solicitud->poder_publico) ? "": "X");

$pdf->SetXY(169, 109.8);
$pdf->Write(0, $solicitud->poder_publico_cual);

$pdf->SetXY(129, 113.1);
$pdf->Write(0, ($solicitud->reconocimiento_publico) ? "X": "");

$pdf->SetXY(143, 113.1);
$pdf->Write(0, ($solicitud->reconocimiento_publico) ? "": "X");

$pdf->SetXY(169, 113.1);
$pdf->Write(0, $solicitud->reconocimiento_publico_cual);

$pdf->SetXY(129, 116.4);
$pdf->Write(0, ($solicitud->persona_expuesta) ? "X": "");

$pdf->SetXY(143, 116.4);
$pdf->Write(0, ($solicitud->persona_expuesta) ? "": "X");

$pdf->SetXY(169, 116.4);
$pdf->Write(0, $solicitud->persona_expuesta_cual);

$pdf->SetXY(62, 126.3);
$pdf->Write(0, ($solicitud->ingresos_mensuales) ? '$'.number_format($solicitud->ingresos_mensuales, 0, ',', '.'): "");

$pdf->SetXY(144, 126.3);
$pdf->Write(0, ($solicitud->egresos_mensuales) ? '$'.number_format($solicitud->egresos_mensuales, 0, ',', '.'): "");

$pdf->SetXY(62, 129.7);
$pdf->Write(0, ($solicitud->activos) ? '$'.number_format($solicitud->activos, 0, ',', '.'): "");

$pdf->SetXY(144, 129.7);
$pdf->Write(0, ($solicitud->pasivos) ? '$'.number_format($solicitud->pasivos, 0, ',', '.'): "");

$pdf->SetXY(62, 133.2);
$pdf->Write(0, ($solicitud->otros_ingresos) ? '$'.number_format($solicitud->otros_ingresos, 0, ',', '.'): "");

$pdf->SetXY(144, 133.2);
$pdf->Write(0, ($solicitud->concepto_otros_ingresos) ? '$'.number_format(floatval($solicitud->concepto_otros_ingresos), 0, ',', '.'): "");

$coordenada = 52;
foreach ($clientes_socios_accionistas as $registro) {
    $pdf->SetXY(26.5, $coordenada);
    $pdf->Write(0, utf8_decode($registro->nombre));

    $pdf->SetXY(68, $coordenada);
    $pdf->Write(0, utf8_decode($registro->tipo_identificacion));

    $pdf->SetXY(110, $coordenada);
    $pdf->Write(0, utf8_decode($registro->documento_numero));

    $pdf->SetXY(151, $coordenada);
    $pdf->Write(0, utf8_decode($registro->porcentaje_participacion));

    $coordenada = $coordenada + 4;
}

$coordenada = 80.7;
foreach ($beneficiarios_clientes_socios_accionistas as $registro) {
    $pdf->SetXY(26.5, $coordenada);
    $pdf->Write(0, utf8_decode($registro->nombre));

    $pdf->SetXY(68, $coordenada);
    $pdf->Write(0, utf8_decode($registro->tipo_identificacion));

    $pdf->SetXY(110, $coordenada);
    $pdf->Write(0, utf8_decode($registro->documento_numero));

    $pdf->SetXY(151, $coordenada);
    $pdf->Write(0, utf8_decode($registro->porcentaje_participacion));

    $coordenada = $coordenada + 4;
}

$pdf->Output("F", "archivos/solicitudes_credito/$solicitud_id/Solicitud de crédito.pdf");