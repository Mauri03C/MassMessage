namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TCPDF;

class ExportService {
    public function exportToPDF($data, $title, $headers) {
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator('MassMessage');
        $pdf->SetAuthor('MassMessage System');
        $pdf->SetTitle($title);
        
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, $title, 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 11);
        
        // Headers
        foreach ($headers as $index => $header) {
            $pdf->Cell(50, 7, $header, 1, ($index == count($headers) - 1 ? 1 : 0), 'C');
        }
        
        $pdf->SetFont('helvetica', '', 10);
        foreach ($data as $row) {
            foreach ($row as $index => $cell) {
                $pdf->Cell(50, 6, $cell, 1, ($index == count($row) - 1 ? 1 : 0), 'L');
            }
        }
        
        return $pdf->Output('report.pdf', 'S');
    }
    
    public function exportToExcel($data, $title, $headers) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Title
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:' . chr(65 + count($headers) - 1) . '1');
        
        // Headers
        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . '2', $header);
        }
        
        // Data
        $row = 3;
        foreach ($data as $dataRow) {
            foreach ($dataRow as $index => $cell) {
                $sheet->setCellValue(chr(65 + $index) . $row, $cell);
            }
            $row++;
        }
        
        // Styling
        $sheet->getStyle('A1:' . chr(65 + count($headers) - 1) . '1')
            ->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:' . chr(65 + count($headers) - 1) . '2')
            ->getFont()->setBold(true);
        
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }
}