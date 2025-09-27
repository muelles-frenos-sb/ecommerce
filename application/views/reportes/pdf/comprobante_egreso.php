<?php
use setasign\Fpdi\Fpdi;

// Crear una instancia de FPDI
$pdf = new FPDI('P', 'mm', 'LEGAL');

$cuenta = $this->proveedores_model->obtener("api_cuentas_por_pagar", ["id" => $id]);

// Cargar el archivo PDF como plantilla
$pdf->AddPage();
$numero_pagina = $pdf->setSourceFile('application/views/reportes/plantillas/comprobante_egreso.pdf');
$plantilla = $pdf->importPage(1);

// Usar la página del archivo como plantilla
$pdf->useTemplate($plantilla, 10, 10, 200);

$pdf->SetFont('Courier', '', 7);

$pdf->SetXY(14, 44);
$pdf->Write(0, $cuenta->provedor_nombre);

$pdf->SetXY(103, 44);
$pdf->Write(0, $cuenta->row_id);

$pdf->SetXY(172, 44);
$pdf->Write(0, date("d/m/Y", strtotime($cuenta->fecha)));

$pdf->SetXY(103, 53);
$pdf->Write(0, $cuenta->row_id);

$pdf->SetXY(22, 58);
$pdf->Write(0, $cuenta->notas);

$pdf->SetXY(14, 72);
$pdf->Write(0, $cuenta->row_id);

$pdf->SetXY(33, 72);
$pdf->Write(0, $cuenta->sede_codigo);

$pdf->SetXY(41, 72);
$pdf->Write(0, $cuenta->provedor_nit);

$pdf->SetXY(46.2, 72);
// $pdf->Write(0, "$cuenta->f200_id-$cuenta->f202_id_sucursal");

$pdf->SetXY(142, 69);
$pdf->Cell(30, 6, "$".number_format($cuenta->valor_documento, 2, '.', ','), 0, 0, 'R');

$pdf->SetXY(172, 69);
$pdf->Cell(30, 6, "$".number_format($cuenta->valor_saldo, 2, '.', ','), 0, 0, 'R');

$pdf->SetXY(142, 76);
$pdf->Cell(30, 4, "$".number_format($cuenta->valor_documento, 2, '.', ','), 0, 0, 'R');

$pdf->SetXY(172, 76);
$pdf->Cell(30, 4, "$".number_format($cuenta->valor_saldo, 2, '.', ','), 0, 0, 'R');

$pdf->Output("I", "Comprobante de egreso.pdf");