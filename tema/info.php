<?php


header('Content-type: application/json');

require_once("../php/restrict.php");
require_once('../libraries/PhpSpreadsheet/src/PhpSpreadsheet/Spreadsheet.php');

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$writer->save('prueba.xlsx');

?>