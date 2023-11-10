<?php
use Fpdf\Fpdf;

$recibo = $this->productos_model->obtener('recibo', ['token' => $token]);
$cuentas_bancarias_recibo = $this->configuracion_model->obtener('recibos_cuentas_bancarias', ['recibo_id' => $recibo->id]);

$pdf = new FPDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();

$gris = array('r' => '211', 'g' => '211', 'b' => '211');

// // Parámetros adicionales
// $titulo = utf8_decode("$equipo->Nombre - Hoja de vida");
// $pdf->SetAuthor('John Arley Cano Salinas - johnarleycano@hotmail.com');
// $pdf->SetTitle($titulo);
// $pdf->SetCreator('John Arley Cano Salinas - johnarleycano@hotmail.com');
// $pdf->SetMargins(0, 0, 5);

// Título
$pdf->setXY(60, 10);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(70, 5, 'MUELLES Y FRENOS SIMON BOLIVAR S.A.S.', 0, 0, 'L', 0);
$pdf->setXY(60, $pdf->getY() + 5);
$pdf->SetFont('Arial', '', 8);
$pdf->setXY(60, $pdf->getY());
$pdf->Cell(70, 5, 'Nit: 900296641', 0, 1, 'L', 0);
$pdf->setXY(60, $pdf->getY());
$pdf->Cell(70, 5, 'CL 31 41 15 LC 6 P 2', 0, 1, 'L', 0);
$pdf->setXY(60, $pdf->getY());
$pdf->Cell(70, 5, 'Tel: 44447232 Fax:', 0, 1, 'L', 0);
$pdf->setXY(60, $pdf->getY());
$pdf->Cell(70, 5, utf8_decode('ITAGÜÍ'), 0, 1, 'L', 0);
$pdf->Ln();

// Recibo de caja
$pdf->setXY(130, 10);
$pdf->SetFont('Arial', 'B', 9);
$pdf->setFillColor($gris['r'], $gris['g'], $gris['b']);
$pdf->Cell(70, 8, 'RECIBO DE CAJA', 1, 0, 'C', 1);
$pdf->setXY(130, $pdf->getY() + 8);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(70, 8, '-----', 1, 0, 'C', 0);
$pdf->Ln(25);

// Información del tercero
$pdf->setXY(10, $pdf->getY()-6);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 6, 'Nombre:', 'L,T', 0, 'L', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(160, 6, utf8_decode($recibo->razon_social), 'T,R', 1, 'L', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 6, 'Nit/CC:', 'L', 0, 'L', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(160, 6, $recibo->documento_numero, 'R', 1, 'L', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 6, 'Fecha', 'L', 0, 'L', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(160, 6, "$recibo->dia/$recibo->mes/$recibo->anio", 'R', 1, 'L', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 6, 'Caja', 'L,B', 0, 'L', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(160, 6, '---', 'B,R', 1, 'L', 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 6, 'Notas', 'L,B', 0, 'L', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(160, 6, '---', 'B,R', 1, 'L', 0);
$pdf->Ln(5);

// Medio de pago
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 6, 'Medio de pago', 'L,T', 0, 'C', 1);
$pdf->Cell(30, 6, 'Banco', 'L,T', 0, 'C', 1);
$pdf->Cell(30, 6, utf8_decode('Número'), 'L,T', 0, 'C', 1);
$pdf->Cell(30, 6, 'Fecha', 'L,T', 0, 'C', 1);
$pdf->Cell(30, 6, utf8_decode('Autorización'), 'L,T', 0, 'C', 1);
$pdf->Cell(40, 6, 'Valor', 'L,T,R', 1, 'C', 1);

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(30, 6, '----', 'B,R,L', 0, 'L', 0);
$pdf->Cell(30, 6, '----', 'B,R', 0, 'L', 0);
$pdf->Cell(30, 6, '----', 'B,R', 0, 'L', 0);
$pdf->Cell(30, 6, '----', 'B,R', 0, 'L', 0);
$pdf->Cell(30, 6, '----', 'B,R', 0, 'L', 0);
$pdf->Cell(40, 6, '----', 'B,R', 1, 'R', 0);
$pdf->Ln(10);

// Cuentas bancarias
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(20, 6, 'Auxiliar', 'L,T', 0, 'C', 1);
$pdf->Cell(60, 6, 'Concepto:', 'L,T', 0, 'C', 1);
$pdf->Cell(10, 6, 'U.N.', 'L,T', 0, 'C', 1);
$pdf->Cell(20, 6, 'Tercero:', 'L,T', 0, 'C', 1);
$pdf->Cell(20, 6, 'Doc. Cruce', 'L,T', 0, 'C', 1);
$pdf->Cell(30, 6, utf8_decode('Débitos:'), 'L,T,R', 0, 'C', 1);
$pdf->Cell(30, 6, utf8_decode('Créditos:'), 'L,T,R', 1, 'C', 1);

$total_debitos = 0;
$total_creditos = 0;

$pdf->SetFont('Arial', '', 6);
foreach($cuentas_bancarias_recibo as $cuenta) {
    $pdf->Cell(20, 5, $cuenta->auxiliar, 'B,R,L', 0, 'L', 0);
    $pdf->Cell(60, 5, utf8_decode($cuenta->nombre), 'B,R', 0, 'L', 0);
    $pdf->Cell(10, 5, '--', 'B,R', 0, 'L', 0);
    $pdf->Cell(20, 5, $recibo->documento_numero, 'B,R', 0, 'L', 0);
    $pdf->Cell(20, 5, '----', 'B,R', 0, 'L', 0);
    $pdf->Cell(30, 5, formato_precio($cuenta->valor), 'B,R', 0, 'R', 0);
    $pdf->Cell(30, 5, '----', 'B,R', 1, 'R', 0);

    $total_debitos += $cuenta->valor;
}

// Sumas iguales
$pdf->SetFont('Arial', '', 6);
$pdf->Cell(130, 6, 'Sumas iguales', 1, 0, 'R', 0);
$pdf->SetFont('Arial', '', 6);
$pdf->Cell(30, 6, formato_precio($total_debitos), 'B,R', 0, 'R', 0);
$pdf->Cell(30, 6, '----', 'B,R', 1, 'R', 0);
$pdf->Ln(5);

$pdf->Output("Recibo.pdf", "I");