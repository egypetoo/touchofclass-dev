<?php

namespace App\Filament\Resources\Admins\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique(),
                TextInput::make('password')
                    ->password()
                    ->label(__('site.password'))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->revealable()
                    ->live(onBlur: true)
                    ->dehydrated(fn ($state) => filled($state))
                    ->confirmed(),
                TextInput::make('password_confirmation')
                    ->password()
                    ->label(__('site.password_confirmation'))
                    ->revealable()
                    ->required(fn (string $operation, callable $get): bool => $operation === 'create' || filled($get('password')))
                    ->dehydrated(false),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

            ]);
    }
}
