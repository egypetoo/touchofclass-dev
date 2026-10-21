<?php

namespace App\Filament\Concerns;

use App\Services\Spreadsheet\Contracts\SpreadsheetDefinition;
use App\Services\Spreadsheet\SpreadsheetService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait InteractsWithSpreadsheet
{
    /**
     * @return class-string<SpreadsheetDefinition>
     */
    abstract protected static function spreadsheetDefinition(): string;

    /**
     * @return array<int, Action>
     */
    protected function getSpreadsheetHeaderActions(): array
    {
        $definitionClass = static::spreadsheetDefinition();
        /** @var SpreadsheetDefinition $definition */
        $definition = app($definitionClass);
        $spreadsheet = app(SpreadsheetService::class);

        return [
            Action::make('exportExcel')
                ->label(__('admin_trans.export_excel'))
                ->color('gray')
                ->action(fn () => $spreadsheet->download(
                    $definition->filenamePrefix().'_'.date('Y-m-d_His').'.xlsx',
                    $definition->headers(),
                    $definition->exportRows(),
                )),
            Action::make('downloadTemplate')
                ->label(__('admin_trans.download_template'))
                ->color('gray')
                ->action(fn () => $spreadsheet->download(
                    $definition->filenamePrefix().'_template.xlsx',
                    $definition->headers(),
                    [$definition->exampleRow()],
                )),
            Action::make('importExcel')
                ->label(__('admin_trans.import_excel'))
                ->color('gray')
                ->form([
                    FileUpload::make('file')
                        ->label(__('admin_trans.import_file'))
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                            'text/plain',
                        ])
                        ->required(),
                ])
                ->action(function (array $data) use ($definition, $spreadsheet): void {
                    $storedPath = Storage::disk('local')->path($data['file']);
                    $extension = pathinfo($storedPath, PATHINFO_EXTENSION);
                    $rows = $spreadsheet->parseRows($storedPath, $extension);
                    Storage::disk('local')->delete($data['file']);

                    $inserted = 0;
                    $updated = 0;
                    $errors = [];

                    DB::transaction(function () use ($rows, $definition, &$inserted, &$updated, &$errors): void {
                        foreach ($rows as $index => $row) {
                            if ($index === 0 && isset($row[0]) && is_string($row[0])) {
                                $header = strtolower($row[0]);
                                if (str_contains($header, 'title') || str_contains($header, 'name') || str_contains($header, 'question') || str_contains($header, 'category')) {
                                    continue;
                                }
                            }

                            $result = $definition->importRow($row, $index + 1);
                            $inserted += $result['inserted'];
                            $updated += $result['updated'];
                            $errors = array_merge($errors, $result['errors']);
                        }
                    });

                    $message = __('admin_trans.import_summary', ['inserted' => $inserted, 'updated' => $updated]);
                    if ($errors !== []) {
                        $message .= ' — '.implode('; ', array_slice($errors, 0, 5));
                    }

                    Notification::make()->success()->title($message)->send();
                }),
        ];
    }
}
