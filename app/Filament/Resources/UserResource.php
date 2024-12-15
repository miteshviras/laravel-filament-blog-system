<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\UserResource\Pages;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::formSchema($form->getLivewire()->record));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('id', '!=', auth()->id()))
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()->label('Active')->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function formSchema(?User $user = null): array
    {
        $strLength = config('custom.string_length');
        $section1 = [
            TextInput::make('name')
                ->required()
                ->maxLength($strLength),
            TextInput::make('email')
                ->email()
                ->unique(User::class, 'email', $user)
                ->required()
                ->maxLength($strLength),
        ];

        if (auth()->user()->is_admin) {
            if (empty($user)) {
                $section1[] = TextInput::make('password')->password()->minLength(6)->maxLength(32)->required();
            } else {
                $section1[] = TextInput::make('password')->password()->minLength(6)->maxLength(32);
            }
        }

        $section1[] = FileUpload::make('avatar_url')
            ->image()->imageEditor()
            ->required()
            ->disk(config('filesystems.default'))
            ->directory('avatars')
            ->visibility('public');

        return [Grid::make([
            'default' => 1,
        ])
            ->schema([
                Split::make([
                    Section::make($section1),
                    Section::make([
                        Toggle::make('is_admin')->label('Is Admin')->default(false),
                        Toggle::make('is_active')->label('Active')->default(true),
                        DateTimePicker::make('email_verified_at')->disabled(),
                        DateTimePicker::make('created_at')->disabled(),
                        DateTimePicker::make('updated_at')->disabled(),
                    ])->grow(false),
                ])->from('xl')
            ])];
    }
}
