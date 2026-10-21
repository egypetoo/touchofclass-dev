<?php

namespace App\Filament\Support;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Grid;

/**
 * Renders one translatable attribute as a field per locale (e.g. `title.ar` and `title.en`)
 * in the same form. Spatie Translatable fills these from and saves them to the JSON column.
 */
class LocalizedField
{
    public const PRIMARY_LOCALE = 'ar';

    /**
     * @var list<string>
     */
    public const LOCALES = ['ar', 'en'];

    /**
     * Rich editors are stacked; other fields sit side by side.
     *
     * Only the primary locale keeps `required()`, so a record can be saved before its English copy is ready.
     *
     * @param  Closure(string $name, string $locale): Field  $makeField
     */
    public static function make(string $attribute, Closure $makeField, ?bool $stacked = null): Grid
    {
        $fields = array_map(
            fn (string $locale): Field => static::localize($makeField("{$attribute}.{$locale}", $locale), $locale),
            self::LOCALES,
        );

        $stacked ??= $fields[0] instanceof RichEditor;

        return Grid::make($stacked ? 1 : count(self::LOCALES))
            ->schema($fields)
            ->columnSpanFull();
    }

    protected static function localize(Field $field, string $locale): Field
    {
        $field->label($field->getLabel().' ('.__('admin_trans.locale_name_'.$locale).')');

        if ($locale !== self::PRIMARY_LOCALE) {
            $field->required(false);
        }

        if (method_exists($field, 'extraInputAttributes')) {
            $field->extraInputAttributes(['dir' => $locale === 'ar' ? 'rtl' : 'ltr'], merge: true);
        }

        return $field;
    }
}
