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

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(30.6, 38.5);
$pdf->Write(0, ($solicitud->nueva) ? "X": "");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(47, 38.5);
$pdf->Write(0, ($solicitud->nueva) ? "": "X");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(39, 46.5);
$pdf->Write(0, $fecha);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(57, 57.5);
$pdf->Write(0, $solicitud->nombre);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(143, 57.5);
$pdf->Write(0, $solicitud->documento_numero);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(40, 61.5);
$pdf->Write(0, $solicitud->direccion);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(147, 61.5);
$pdf->Write(0, $solicitud->telefono);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(36, 65.5);
$pdf->Write(0, $solicitud->email);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(145, 65.5);
$pdf->Write(0, $solicitud->celular);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(40, 69);
$pdf->Write(0, $solicitud->representante_legal);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(138, 69);
$pdf->Write(0, $solicitud->representante_legal_documento_numero);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(65, 72.7);
$pdf->Write(0, $solicitud->email_factura_electronica);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(145, 72.7);
$pdf->Write(0, utf8_decode("¿Cuántos vehículos posee? $solicitud->cantidad_vehiculos"));

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(59.3, 86.8);
$pdf->Write(0, $solicitud->tesoreria_nombre);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(92.4, 86.8);
$pdf->Write(0, $solicitud->tesoreria_email);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(126, 86.8);
$pdf->Write(0, $solicitud->tesoreria_telefono);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(159, 86.8);
$pdf->Write(0, $solicitud->tesoreria_celular);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(59.3, 90.3);
$pdf->Write(0, $solicitud->comercial_nombre);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(92.4, 90.3);
$pdf->Write(0, $solicitud->comercial_email);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(126, 90.3);
$pdf->Write(0, $solicitud->comercial_telefono);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(159, 90.3);
$pdf->Write(0, $solicitud->comercial_celular);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(59.3, 93.7);
$pdf->Write(0, $solicitud->contabilidad_nombre);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(92.4, 93.7);
$pdf->Write(0, $solicitud->contabilidad_email);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(126, 93.7);
$pdf->Write(0, $solicitud->contabilidad_telefono);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(159, 93.7);
$pdf->Write(0, $solicitud->contabilidad_celular);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(62, 105.5);
$pdf->Write(0, $solicitud->referencia_comercial_entidad1);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(109, 105.5);
$pdf->Write(0, $solicitud->referencia_comercial_cel1);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(155, 105.5);
$pdf->Write(0, $solicitud->referencia_comercial_direccion1);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(62, 109.5);
$pdf->Write(0, $solicitud->referencia_comercial_entidad2);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(109, 109.5);
$pdf->Write(0, $solicitud->referencia_comercial_cel2);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(155, 109.5);
$pdf->Write(0, $solicitud->referencia_comercial_direccion2);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(62, 119.8);
$pdf->Write(0, $solicitud->referencia_bancaria_entidad);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(109, 119.8);
$pdf->Write(0, $solicitud->referencia_bancaria_tipo);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(155, 119.8);
$pdf->Write(0, $solicitud->referencia_bancaria_numero);

$coordenada = 140;
foreach ($personas_autorizadas as $registro) {
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(100, $coordenada);
    $pdf->Write(0, $registro->nombre);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(140, $coordenada);
    $pdf->Write(0, $registro->documento_numero);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(169, $coordenada);
    $pdf->Write(0, $registro->celular);

    $coordenada = $coordenada + 5;
}

$pdf->AddPage();
$tplIdx = $pdf->importPage(2);
// Usar la página del archivo como plantilla
$pdf->useTemplate($tplIdx, 10, 10, 200);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(129, 106.3);
$pdf->Write(0, ($solicitud->recursos_publicos) ? "X": "");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(143, 106.3);
$pdf->Write(0, ($solicitud->recursos_publicos) ? "": "X");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(169, 106.3);
$pdf->Write(0, $solicitud->recursos_publicos_cual);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(129, 109.8);
$pdf->Write(0, ($solicitud->poder_publico) ? "X": "");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(143, 109.8);
$pdf->Write(0, ($solicitud->poder_publico) ? "": "X");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(169, 109.8);
$pdf->Write(0, $solicitud->poder_publico_cual);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(129, 113.1);
$pdf->Write(0, ($solicitud->reconocimiento_publico) ? "X": "");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(143, 113.1);
$pdf->Write(0, ($solicitud->reconocimiento_publico) ? "": "X");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(169, 113.1);
$pdf->Write(0, $solicitud->reconocimiento_publico_cual);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(129, 116.4);
$pdf->Write(0, ($solicitud->persona_expuesta) ? "X": "");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(143, 116.4);
$pdf->Write(0, ($solicitud->persona_expuesta) ? "": "X");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(169, 116.4);
$pdf->Write(0, $solicitud->persona_expuesta_cual);

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(62, 126.3);
$pdf->Write(0, ($solicitud->ingresos_mensuales) ? number_format($solicitud->ingresos_mensuales, 2, ',', '.'): "");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(144, 126.3);
$pdf->Write(0, ($solicitud->egresos_mensuales) ? number_format($solicitud->egresos_mensuales, 2, ',', '.'): "");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(62, 129.7);
$pdf->Write(0, ($solicitud->activos) ? number_format($solicitud->activos, 2, ',', '.'): "");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(144, 129.7);
$pdf->Write(0, ($solicitud->pasivos) ? number_format($solicitud->pasivos, 2, ',', '.'): "");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(62, 133.2);
$pdf->Write(0, ($solicitud->otros_ingresos) ? number_format($solicitud->otros_ingresos, 2, ',', '.'): "");

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(144, 133.2);
$pdf->Write(0, ($solicitud->concepto_otros_ingresos) ? number_format($solicitud->concepto_otros_ingresos, 2, ',', '.'): "");

$coordenada = 52;
foreach ($clientes_socios_accionistas as $registro) {
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(26.5, $coordenada);
    $pdf->Write(0, $registro->nombre);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(68, $coordenada);
    $pdf->Write(0, $registro->tipo_identificacion);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(110, $coordenada);
    $pdf->Write(0, $registro->documento_numero);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(151, $coordenada);
    $pdf->Write(0, $registro->porcentaje_participacion);

    $coordenada = $coordenada + 4;
}

$coordenada = 80.7;
foreach ($beneficiarios_clientes_socios_accionistas as $registro) {
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(26.5, $coordenada);
    $pdf->Write(0, $registro->nombre);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(68, $coordenada);
    $pdf->Write(0, $registro->tipo_identificacion);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(110, $coordenada);
    $pdf->Write(0, $registro->documento_numero);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetXY(151, $coordenada);
    $pdf->Write(0, $registro->porcentaje_participacion);

    $coordenada = $coordenada + 4;
}

$pdf->Output("F", "archivos/solicitudes_credito/$solicitud_id/Solicitud de crédito.pdf");