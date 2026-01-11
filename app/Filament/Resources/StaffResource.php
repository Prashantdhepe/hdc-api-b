<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffResource\Pages;
use App\Filament\Resources\StaffResource\RelationManagers;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
// use Filament\Resources\Form;
use Filament\Resources\Resource;
// use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ImageColumn;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('school_type_id')
                                    ->relationship('school_type', 'name')
                                    ->required(),
                                Forms\Components\Select::make('staff_type_id')
                                    ->relationship('staff_type', 'name')
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('mobile_no')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('address')
                                    ->maxLength(65535),
                                Forms\Components\Textarea::make('qualification')
                                    ->required()
                                    ->maxLength(65535),
                                Forms\Components\Textarea::make('teaching_as')
                                    ->maxLength(65535),
                                Forms\Components\DatePicker::make('date_of_joining')
                                    ->required(),
                                Forms\Components\Toggle::make('is_salaried')
                                    ->required(),
                                Forms\Components\FileUpload::make('photo')
                                    ->required()
                                    ->disk(config('filesystems.default'))
                                    ->directory('staff_photos')
                            ])
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('school_type.name'),
                Tables\Columns\TextColumn::make('staff_type.name'),
                Tables\Columns\TextColumn::make('name'),
                ImageColumn::make('photo')->square(),
                Tables\Columns\TextColumn::make('mobile_no'),
                Tables\Columns\TextColumn::make('qualification'),
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
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'view' => Pages\ViewStaff::route('/{record}'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }
}
