<?php
 namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelService
{
    public function createExcelFile($data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Define your headers
        $headers = ['ID', 'request_date', 'Available Start Time', 'Available End Time', 'Status', 'User', 'Service', 'Description', 'Created at'];
    
        // Add headers to the first row
        $sheet->fromArray($headers, null, 'A1');
    
        // Add data starting from the second row
        $sheet->fromArray($data, null, 'A2');
         // Custom row height
    $customRowHeight = 20; // Change this value as needed

    // Set row height for the header row
    $sheet->getRowDimension(1)->setRowHeight($customRowHeight);

    // Set row height for each data row
    foreach ($data as $rowIndex => $row) {
        $sheet->getRowDimension($rowIndex + 2)->setRowHeight($customRowHeight);
    }
        // Style for the entire sheet content
        $contentStyleArray = [
            'font' => [
                'size' => 12, // Set the font size for content
            ],
        ];
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->applyFromArray($contentStyleArray);
    
        // Style the header row
        $headerStyleArray = [
            'font' => [
                'bold' => true,
                'size' => 12, // You can have a different size for the header if required
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCCCCC', // Gray color, change as needed
                ],
            ],
        ];
        $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . '1')->applyFromArray($headerStyleArray);
    
        // Set each column to auto-size
        foreach(range('A', $sheet->getHighestDataColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    
        // Save the Excel file to a temporary location
        $writer = new Xlsx($spreadsheet);
        $fileName = 'spreadsheet.xlsx';
        $tempFilePath = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFilePath);
    
        return $tempFilePath;
    }
    
    
    
}
