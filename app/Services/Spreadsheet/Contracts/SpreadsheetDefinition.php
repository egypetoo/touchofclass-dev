<?php

namespace App\Services\Spreadsheet\Contracts;

interface SpreadsheetDefinition
{
    /**
     * @return array<int, string>
     */
    public function headers(): array;

    /**
     * @return array<int, mixed>
     */
    public function exampleRow(): array;

    /**
     * @return array<int, array<int, mixed>>
     */
    public function exportRows(): array;

    /**
     * @param  array<int, mixed>  $row
     * @return array{inserted: int, updated: int, errors: array<int, string>}
     */
    public function importRow(array $row, int $rowNumber): array;

    public function filenamePrefix(): string;
}
