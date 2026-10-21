<?php

namespace App\Services\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SpreadsheetService
{
    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    public function download(string $filename, array $headers, array $rows = []): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');

        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:'.$lastColumn.'1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        if ($rows !== []) {
            $sheet->fromArray($rows, null, 'A2');
        }

        for ($colIndex = 1; $colIndex <= count($headers); $colIndex++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex))->setAutoSize(true);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_export_');
        (new Xlsx($spreadsheet))->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function parseRows(string $path, string $extension): array
    {
        $extension = strtolower($extension);
        $rows = [];

        if (in_array($extension, ['xlsx', 'xls'], true)) {
            $reader = IOFactory::createReader($extension === 'xls' ? 'Xls' : 'Xlsx');
            $reader->setReadDataOnly(true);
            $worksheet = $reader->load($path)->getActiveSheet();

            foreach ($worksheet->getRowIterator() as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $rows[] = $rowData;
            }

            return $rows;
        }

        if (($handle = fopen($path, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        }

        return $rows;
    }
}
