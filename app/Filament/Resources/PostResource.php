<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
// use Filament\Resources\Form;
use Filament\Resources\Resource;
// use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-2-stack';

    protected static ?string $navigationGroup = "Posts";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('school_type_id')
                                    ->relationship('school_type', 'name'),
                                Forms\Components\Select::make('post_category_id')
                                    ->relationship('post_category', 'name'),
                                Forms\Components\TextInput::make('user_id')
                                    ->hidden(true),

                                //->value(Auth::user()->id),
                                //->relationship('user', 'name'),
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->reactive()
                                    ->afterStateUpdated(function ($set, $state) {
                                        $set('slug', Str::slug($state));
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255),
                                    // ->disabled(),
                                Forms\Components\RichEditor::make('short_description')
                                    ->columnSpan(2),
                                Forms\Components\RichEditor::make('content')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\DateTimePicker::make('published_at'),
                                Forms\Components\FileUpload::make('media_galleries')
                                    ->columns(1)
                                    ->multiple()
                                    ->enableReordering()
                                    ->enableDownload()
                                    ->enableOpen()
                                    ->disk(config('filesystems.default'))
                                    ->directory('media_galleries')
                                    ->visibility('private')
                                    ->storeFileNamesIn('media_gallery_original_filenames')
                                    ->columnSpan(2),
                            ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('school_type.name'),
                Tables\Columns\TextColumn::make('post_category.name'),
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
