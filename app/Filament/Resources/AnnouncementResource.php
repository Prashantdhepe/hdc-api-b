<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Filament\Resources\AnnouncementResource\RelationManagers;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('school_type_id')
                        ->columns(1)
                        ->required()
                        ->columnSpan(2)
                        ->relationship('school_type','name'),
                        Forms\Components\TextInput::make('user_id')
                        ->hidden(true), 
                        Forms\Components\DateTimePicker::make('start_date')
                        ->required(),
                        Forms\Components\DateTimePicker::make('end_date')
                        ->required(),
                        Forms\Components\Textarea::make('short_description')
                        ->required(),
                        Forms\Components\Textarea::make('content')
                        ->required(),
                        Forms\Components\Toggle::make('is_active'),
                        Forms\Components\DateTimePicker::make('published_at')
                        ->required(),
                        
                        
                    ])
                ])
            ]);
    }



    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('school_type.name'),
                // Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('start_date')
                ->dateTime(),
                Tables\Columns\TextColumn::make('end_date')
                ->dateTime(),
                Tables\Columns\TextColumn::make('published_at')
                ->dateTime(),
                Tables\Columns\IconColumn::make('is_active')
                ->boolean(),
                Tables\Columns\TextColumn::make('short_description')
                
            ])
            ->filters([
                //
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'view' => Pages\ViewAnnouncement::route('/{record}'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
