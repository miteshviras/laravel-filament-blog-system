<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Set;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;

use function Laravel\Prompts\textarea;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\PostResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    public static function form(Form $form): Form
    {
        return $form->schema(self::formSchema($form->getLivewire()->record));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->byUser())
            ->columns(self::tableSchema())
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function formSchema(?Post $post = null)
    {
        $categoryOptions = Category::active()->limit(10)->pluck('name', 'id')->toArray();
        $strLength = config('custom.string_length');
        $userOptions = User::active()->where('is_admin', false)->limit(10)->pluck('name', 'id')->toArray();

        $section2 = [
            Select::make('categories')
                ->multiple()
                ->searchable()
                ->required()
                ->label('Categories')
                ->options($categoryOptions)
                ->relationship(titleAttribute: 'name')
                ->getSearchResultsUsing(
                    fn(string $search): array =>
                    Category::active()
                        ->where('name', 'like', "%{$search}%")
                        ->limit(10)
                        ->pluck('name', 'id')
                        ->toArray()
                )
                ->preload(),
            TextInput::make('duration')->placeholder('eg: 2 hours')->maxLength($strLength)->nullable(),
            Select::make('difficulty_level')->options(Post::DIFFICULTY_LEVELS),
            Toggle::make('is_published')->default(true),
            DateTimePicker::make('created_at')->disabled(),
            DateTimePicker::make('updated_at')->disabled(),
        ];

        auth()->user()->is_admin ? array_unshift($section2, Select::make('user_id')
            ->searchable()
            ->required()
            ->label('User')
            ->options($userOptions)
            ->getSearchResultsUsing(
                fn(string $search): array =>
                User::where('is_admin', false)
                    ->where('is_active', true)
                    ->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->limit(10)
                    ->pluck('name', 'id')
                    ->toArray()
            )
            ->preload()) : array_push(
            $section2,
            Hidden::make('user_id')->default(auth()->id()),
        );

        return [Grid::make([
            'default' => 1,
        ])
            ->schema([
                Split::make([
                    Section::make([
                        TextInput::make('title')->maxLength($strLength * 2)
                            ->live(1000)
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state)))
                            ->required(),
                        TextInput::make('slug')->maxLength($strLength * 3)->required()->unique(Post::class, 'slug', $post),
                        textarea::make('summary')->maxLength($strLength * 2)->nullable(),
                        FileUpload::make('thumbnail')
                            ->image()->imageEditor()
                            ->required()
                            ->disk(config('filesystems.default'))
                            ->directory('thumbnails/post')
                            ->visibility('public'),
                        RichEditor::make('content')->fileAttachmentsDisk(config('filesystems.default'))
                            ->fileAttachmentsDirectory('attachments')
                            ->required(),

                    ]),
                    Section::make($section2)->grow(false),
                ])->from('xl')
            ])];
    }

    public static function tableSchema(): array
    {
        return [
            TextColumn::make('id')->label('#'),
            TextColumn::make('title', 'Title')->limit(30)->searchable(),
            TextColumn::make('categories.name')->limit(25)->searchable(),
            TextColumn::make('user.name')->label('Created By')->limit(25)->searchable(),
            ToggleColumn::make('is_published')->label('Published'),
            TextColumn::make('created_at', 'Created At')->toggleable(isToggledHiddenByDefault: true)->dateTime(),
            TextColumn::make('updated_at', 'Updated At')->toggleable(isToggledHiddenByDefault: true)->dateTime(),
        ];
    }
}
