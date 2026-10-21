<?php

namespace App\Filament\Resources\Redirects\Pages;

use App\Filament\Resources\Redirects\RedirectResource;
use App\Models\Redirect;
use App\Services\RedirectService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ManageRedirects extends ManageRecords
{
    protected static string $resource = RedirectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label(__('admin_trans.download_template'))
                ->color('gray')
                ->action(fn () => Response::streamDownload(
                    function (): void {
                        echo "source,target,status_code,is_active,notes\n";
                        echo "/en/old-url,/en/new-url,301,1,optional note\n";
                        echo "/en/removed-page,,410,1,removed permanently\n";
                    },
                    'redirects_template.csv',
                    ['Content-Type' => 'text/csv']
                )),
            Action::make('importCsv')
                ->label(__('admin_trans.import'))
                ->color('gray')
                ->form([
                    FileUpload::make('file')
                        ->label(__('admin_trans.import_file'))
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $path = Storage::disk('local')->path($data['file']);
                    $service = app(RedirectService::class);
                    $inserted = 0;
                    $updated = 0;
                    $errors = [];

                    if (($handle = fopen($path, 'r')) === false) {
                        Notification::make()->danger()->title(__('admin_trans.import_failed'))->send();

                        return;
                    }

                    $rowNum = 0;
                    while (($row = fgetcsv($handle)) !== false) {
                        $rowNum++;
                        if ($rowNum === 1 && isset($row[0]) && stripos($row[0], 'source') !== false) {
                            continue;
                        }

                        $source = trim($row[0] ?? '');
                        $target = trim($row[1] ?? '');
                        $status = (int) trim((string) ($row[2] ?? 301));
                        $active = trim((string) ($row[3] ?? '1'));
                        $notes = trim($row[4] ?? '');

                        if ($source === '') {
                            $errors[] = __('admin_trans.import_row_missing_source', ['row' => $rowNum]);

                            continue;
                        }

                        if (! in_array($status, [301, 302, 410], true)) {
                            $status = 301;
                        }

                        if ($status !== 410 && $target === '') {
                            $errors[] = __('admin_trans.import_row_missing_target', ['row' => $rowNum]);

                            continue;
                        }

                        if ($status !== 410 && ! RedirectService::isSafeRedirectTarget($target)) {
                            $errors[] = __('admin_trans.import_row_invalid_target', ['row' => $rowNum]);

                            continue;
                        }

                        $payload = [
                            'target' => $target !== '' ? $target : null,
                            'status_code' => $status,
                            'is_active' => in_array(strtolower($active), ['1', 'true', 'yes', 'y'], true),
                            'notes' => $notes ?: null,
                        ];

                        $existing = Redirect::query()->where('source', $source)->first();
                        if ($existing) {
                            $service->update($existing, $payload);
                            $updated++;
                        } else {
                            $service->create(array_merge(['source' => $source], $payload));
                            $inserted++;
                        }
                    }

                    fclose($handle);
                    Storage::disk('local')->delete($data['file']);

                    $message = __('admin_trans.import_summary', ['inserted' => $inserted, 'updated' => $updated]);
                    if ($errors !== []) {
                        $message .= ' — '.implode('; ', array_slice($errors, 0, 5));
                    }

                    Notification::make()
                        ->success()
                        ->title($message)
                        ->send();
                }),
            Action::make('exportCsv')
                ->label(__('admin_trans.export'))
                ->color('gray')
                ->action(fn () => Response::streamDownload(
                    function (): void {
                        $handle = fopen('php://output', 'w');
                        fputcsv($handle, ['source', 'target', 'status_code', 'is_active', 'notes']);
                        Redirect::query()->orderBy('id')->each(function (Redirect $redirect) use ($handle): void {
                            fputcsv($handle, [
                                $redirect->source,
                                $redirect->target,
                                $redirect->status_code,
                                $redirect->is_active ? 1 : 0,
                                $redirect->notes,
                            ]);
                        });
                        fclose($handle);
                    },
                    'redirects_'.date('Y-m-d_His').'.csv',
                    ['Content-Type' => 'text/csv']
                )),
            CreateAction::make(),
        ];
    }
}
