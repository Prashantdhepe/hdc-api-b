<?php

namespace App\Filament\Resources;

use Illuminate\Validation\Rule;
use App\Filament\Resources\GalleryResource\Pages;
use App\Filament\Resources\GalleryResource\RelationManagers;
use App\Models\Gallery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = "Medias";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Select::make('school_type_id')
                                    ->label('School Type')   
                                    ->relationship('school_type', 'name'),
    
                                Forms\Components\Select::make('media_type')
                                    ->label('Media Type')
                                    ->options([
                                        'image' => 'Image',
                                        'video' => 'Video',
                                    ])
                                    ->default('image')
                                    ->live()
                                    ->required(),
    
                                Forms\Components\DateTimePicker::make('published_at'),
    
                                Forms\Components\FileUpload::make('media_path')
                                    ->label('Upload Image')
                                    ->image()
                                    ->multiple()
                                    ->required(fn ($get) => $get('media_type') === 'image')
                                    ->visible(fn ($get) => $get('media_type') === 'image')
                                    ->dehydrated(fn ($get) => $get('media_type') === 'image')
                                    ->disk(config('filesystems.default'))
                                    ->directory('galleries')
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('youtube_url')
                                    ->label('YouTube Link')
                                    ->placeholder('https://www.youtube.com/watch?v=...')
                                    ->url()
                                    ->required(fn ($get) => $get('media_type') === 'video')
                                    ->visible(fn ($get) => $get('media_type') === 'video')
                                    ->dehydrated(fn ($get) => $get('media_type') === 'video')
                                    ->regex('/^(https?\:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/')
                                    ->validationAttribute('YouTube URL')
                                    ->columnSpan(2),
                                
                            ]),
                    ])
                        ]);
           
    }
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('school_type.name')->sortable(),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('media_type')->sortable(),
            ])
            ->filters([
                // Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'view' => Pages\ViewGallery::route('/{record}'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
