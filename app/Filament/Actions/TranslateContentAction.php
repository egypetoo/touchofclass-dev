<?php

namespace App\Filament\Actions;

use App\Filament\Support\LocalizedField;
use App\Services\Translation\MyMemoryTranslator;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Throwable;

class TranslateContentAction
{
    public const TARGET_LOCALE = 'en';

    /**
     * Form-level button that translates the primary-locale fields into empty target-locale fields.
     *
     * @param  list<string>  $attributes  Translatable attributes rendered with {@see LocalizedField}.
     */
    public static function make(array $attributes): Actions
    {
        return Actions::make([
            Action::make('translateContent')
                ->label(fn (): string => __('admin_trans.translate_to_other_locale'))
                ->icon(Heroicon::OutlinedLanguage)
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading(fn (): string => __('admin_trans.translate_content'))
                ->modalDescription(fn (): string => __('admin_trans.translate_content_help'))
                ->action(fn (Get $get, Set $set) => static::translateEmptyFields($attributes, $get, $set)),
        ])
            ->key('translateContentActions')
            ->alignEnd()
            ->columnSpanFull();
    }

    /**
     * @param  list<string>  $attributes
     */
    public static function translateEmptyFields(array $attributes, Get $get, Set $set): void
    {
        $sourceLocale = LocalizedField::PRIMARY_LOCALE;
        $targetLocale = self::TARGET_LOCALE;

        $sourceData = [];

        foreach ($attributes as $attribute) {
            $source = $get("{$attribute}.{$sourceLocale}");
            $target = $get("{$attribute}.{$targetLocale}");

            if (is_string($source) && filled(trim(strip_tags($source))) && blank(is_string($target) ? trim(strip_tags($target)) : $target)) {
                $sourceData[$attribute] = $source;
            }
        }

        if ($sourceData === []) {
            Notification::make()
                ->title(__('admin_trans.translate_nothing_to_fill'))
                ->warning()
                ->send();

            return;
        }

        try {
            $translated = app(MyMemoryTranslator::class)->translateFields($sourceData, $sourceLocale, $targetLocale);
        } catch (Throwable $exception) {
            Notification::make()
                ->title(__('admin_trans.translate_failed'))
                ->body($exception->getMessage())
                ->danger()
                ->send();

            return;
        }

        if ($translated === []) {
            Notification::make()
                ->title(__('admin_trans.translate_failed'))
                ->danger()
                ->send();

            return;
        }

        foreach ($translated as $attribute => $value) {
            $set("{$attribute}.{$targetLocale}", $value);
        }

        Notification::make()
            ->title(__('admin_trans.translate_success'))
            ->body(__('admin_trans.translate_success_body', [
                'from' => strtoupper($sourceLocale),
                'to' => strtoupper($targetLocale),
            ]))
            ->success()
            ->send();
    }
}
