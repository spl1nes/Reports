<?php

// Create new PHPExcel object
$excel = new \phpOMS\Utils\Excel\Excel();

$newCustomers = $this->getData('newCustomers');  
$lostCustomers = $this->getData('lostCustomers');  


// Set document properties
$excel->getProperties()->setCreator('Orange Management Solutions')
    ->setLastModifiedBy('Dennis Eichhorn')
    ->setTitle('Dentist New/Lost Customers')
    ->setSubject('Kundenauswertung')
    ->setDescription('Auswertung von gewonnen und verlorenen Kunden in den Sparten (16, 61, 62, 63) in Deutschland.')
    ->setKeywords('Inland, Kunden, 16, 61, 62, 63')
    ->setCategory('Sales');


$i = 0;
foreach($newCustomers as $rep => $customers) {
    $excel->setActiveSheetIndex($i);

    $excel->getActiveSheet()
        ->mergeCells('A1:J1')
        ->setCellValue('A1', trim($rep))
        ->getStyle('A1:J1')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $excel->getActiveSheet()
        ->setCellValue('A2', 'Kd.')
        ->setCellValue('B2', 'Name')
        ->setCellValue('C2', 'Tel.')
        ->setCellValue('D2', 'Str.')
        ->setCellValue('E2', 'PLZ')
        ->setCellValue('F2', 'Ort')
        ->setCellValue('G2', 'Beleg')
        ->setCellValue('H2', 'Datum')
        ->setCellValue('I2', 'Art.')
        ->setCellValue('J2', 'Art.');

    $j = 1;
    foreach($customers as $customer) {
        $excel->getActiveSheet()
        ->setCellValue('A' . (2+$j), (int) $customer['KUNDENNUMMER'])
        ->setCellValue('B' . (2+$j), trim($customer['KD_Name1']))
        ->setCellValue('C' . (2+$j), trim($customer['KD_Telefon']))
        ->setCellValue('D' . (2+$j), trim($customer['KD_Strasse']))
        ->setCellValue('E' . (2+$j), trim($customer['KD_PLZ']))
        ->setCellValue('F' . (2+$j), trim($customer['KD_Ort']))
        ->setCellValue('G' . (2+$j), (int) $customer['BELEGNUMMER'])
        ->setCellValue('H' . (2+$j), (new \DateTime($customer['BELEGDATUM']))->format('Y-m-d'))
        ->setCellValue('I' . (2+$j), (int) $customer['ARTIKELNUMMER'])
        ->setCellValue('J' . (2+$j), trim($customer['ArtikelBez1']));

        for($col = 'A'; $col !== 'J'; $col++) {
            $excel->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        }

        $j++;
    }

    $excel->getActiveSheet()->setTitle(trim($rep) . '-NEW');
    $excel->createSheet($i+1);
    $i++;
}

foreach($lostCustomers as $rep => $customers) {
    $excel->setActiveSheetIndex($i);

    $excel->getActiveSheet()
        ->mergeCells('A1:F1')
        ->setCellValue('A1', trim($rep))
        ->getStyle('A1:F1')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $excel->getActiveSheet()
        ->setCellValue('A2', 'Kd.')
        ->setCellValue('B2', 'Name')
        ->setCellValue('C2', 'Tel.')
        ->setCellValue('D2', 'Str.')
        ->setCellValue('E2', 'PLZ')
        ->setCellValue('F2', 'Ort');

    $j = 1;
    foreach($customers as $customer) {
        $excel->getActiveSheet()
        ->setCellValue('A' . (2+$j), (int) $customer['KUNDENNUMMER'])
        ->setCellValue('B' . (2+$j), trim($customer['KD_Name1']))
        ->setCellValue('C' . (2+$j), trim($customer['KD_Telefon']))
        ->setCellValue('D' . (2+$j), trim($customer['KD_Strasse']))
        ->setCellValue('E' . (2+$j), trim($customer['KD_PLZ']))
        ->setCellValue('F' . (2+$j), trim($customer['KD_Ort']));

        for($col = 'A'; $col !== 'E'; $col++) {
            $excel->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        }

        $j++;
    }

    $excel->getActiveSheet()->setTitle(trim($rep) . '-LOST');
    $excel->createSheet($i+1);
    $i++;
}

$excel->removeSheetByIndex($i);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$excel->setActiveSheetIndex(0);

$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$objWriter->save('php://output');
